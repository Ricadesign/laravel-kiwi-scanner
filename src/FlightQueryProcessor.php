<?php

namespace Ricadesign\LaravelKiwiScanner;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class FlightQueryProcessor
{
    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    function getFlights($parameters)
    {
        $apiParameters = $this->buildApiParameters($parameters);
        $response = $this->api->getFlights($apiParameters);
        //echo '<pre>'; print_r($response); echo '</pre>';
        $flights = $this->parseApiResponse($response);
        $flights = $this->applyPostApiQueryFilters($flights, $parameters);
        return $this->aggregateResults($flights, $parameters);
    }

    private function buildApiParameters($parameters) {
        $apiParameters = [
            'v' => 3,
            'curr' => 'EUR',
            'locale' => 'es',
            'flight_type' => 'round',
            'max_stopovers' => 0
        ];

        if (isset($parameters->origins))
            $apiParameters['fly_from'] = $this->joinLocationsToKiwiFormat($parameters->origins);

        if (isset($parameters->destinations))
            $apiParameters['fly_to'] = $this->joinLocationsToKiwiFormat($parameters->destinations);

        if (isset($parameters->daysBetweenFlights)) {
            $apiParameters['nights_in_dst_from'] = $parameters->daysBetweenFlights;
            $apiParameters['nights_in_dst_to'] = $parameters->daysBetweenFlights;
        }

        if (isset($parameters->startDate))
            $apiParameters['date_from'] = $parameters->startDate->format('d/m/Y');

        if (isset($parameters->endDate))
            $apiParameters['date_to'] = $parameters->endDate->format('d/m/Y');

        if (isset($parameters->numAdults))
            $apiParameters['adults'] = $parameters->numAdults;

        return $apiParameters;
    }

    private function joinLocationsToKiwiFormat($locations) {
        return implode(',', array_map(
            function($loc){ return $loc->getKiwiFormat(); },
            $locations));
    }

    private function parseApiResponse($response) {
        $flights = [];

        // TODO: Validate if response is not error

        foreach ($response['data'] as $trip) {
            $flight = new RoundFlight();
            $flight->id = "TODO";
            $flight->price = $trip['price'];
            $flight->routes = $trip['routes'];
            $flight->cityFrom = $trip['cityFrom'];
            $flight->cityTo = $trip['cityTo'];
            $flight->airportFrom = $trip['flyFrom'];
            $flight->airportTo = $trip['flyTo'];
            $flight->journeyFlightDepartureTime = $this->parseTimeStampAndInferTimeZone(
                $trip['route'][0]['dTime'], $trip['route'][0]['dTimeUTC']);
            $flight->journeyFlightArrivalTime = $this->parseTimeStampAndInferTimeZone(
                $trip['route'][0]['aTime'], $trip['route'][0]['aTimeUTC']);
            $flight->returnFlightDepartureTime = $this->parseTimeStampAndInferTimeZone(
                $trip['route'][1]['dTime'], $trip['route'][1]['dTimeUTC']);
            $flight->returnFlightArrivalTime = $this->parseTimeStampAndInferTimeZone(
                $trip['route'][1]['aTime'], $trip['route'][1]['aTimeUTC']);
            // TODO: Account time from-to airport to-from city (or is this out-of-scope?)
            $flight->minutesInDestination = $flight->returnFlightDepartureTime->diffInMinutes($flight->journeyFlightArrivalTime);
            $flight->bookingToken = $trip['booking_token'];
            $flights[] = $flight;
        }
        return $flights;
    }

    private function parseTimeStampAndInferTimeZone($timeStampLocal, $timeStampUTC) {
        $timeZoneOffsetSeconds = $timeStampLocal - $timeStampUTC;
        assert(($timeZoneOffsetSeconds % 60) === 0);
        $timezone = CarbonTimeZone::createFromMinuteOffset($timeZoneOffsetSeconds / 60);

        return Carbon::createFromTimestamp($timeStampLocal, $timezone);
    }

    private function applyPostApiQueryFilters($flights, $parameters) {
        if (isset($parameters->minimumMinutesInDestination)) {
            $flights = array_filter($flights, function($f) use ($parameters) {
                return $f->minutesInDestination >= $parameters->minimumMinutesInDestination;
            });
        }

        return $flights;
    }

    private function aggregateResults($flights, $parameters) {
        if (isset($parameters->groupBy) && $parameters->groupBy === FlightQuery::GROUP_BY_DAY) {
            $flightsByDate = [];
            foreach ($flights as $f) {
                $dayKey = $f->journeyFlightDepartureTime->format("Y-m-d");
                $flightsByDate[$dayKey][] = $f;
            }
            ksort($flightsByDate);
            return $flightsByDate;
        }
    }
}
