<?php

namespace Ricadesign\LaravelKiwiScanner;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;

/**
 * High level interface over the Kiwi Flights API.
 */
class FlightSearcher
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
        if (!isset($response['data'])) {
            // TODO: Improve error handling logic
            //echo '<pre>'; print_r($response); echo '</pre>';
            throw new FlightOperationException("Invalid API response");
        }
        $response = $this->applyPostApiQueryFilters($response, $parameters);
        $flights = $this->parseApiResponse($response);
        return $this->aggregateResults($flights, $parameters);
    }

    private function buildApiParameters($parameters) {
        $apiParameters = [
            'v' => 3,
            'curr' => 'EUR',
            'locale' => 'es',
            'max_stopovers' => 0,
        ];

        if (isset($parameters->origins))
            $apiParameters['fly_from'] = $this->joinLocationsToKiwiFormat($parameters->origins);

        if (isset($parameters->destinations))
            $apiParameters['fly_to'] = $this->joinLocationsToKiwiFormat($parameters->destinations);

        if (isset($parameters->maxStopovers))
            $apiParameters['max_stopovers'] = $parameters->maxStopovers;

        if (isset($parameters->nightsInDestinationFrom))
            $apiParameters['nights_in_dst_from'] = $parameters->nightsInDestinationFrom;

        if (isset($parameters->nightsInDestinationTo))
            $apiParameters['nights_in_dst_to'] = $parameters->nightsInDestinationTo;

        if (isset($parameters->startDate))
            $apiParameters['date_from'] = $parameters->startDate->format('d/m/Y');

        if (isset($parameters->endDate))
            $apiParameters['date_to'] = $parameters->endDate->format('d/m/Y');

        if (isset($parameters->returnFrom))
            $apiParameters['return_from'] = $parameters->returnFrom->format('d/m/Y');

        if (isset($parameters->returnTo))
            $apiParameters['return_to'] = $parameters->returnTo->format('d/m/Y');

        if (isset($parameters->departureTimeFrom))
            $apiParameters['dtime_from'] = $parameters->departureTimeFrom->format('H:i');

        if (isset($parameters->departureTimeTo))
            $apiParameters['dtime_to'] = $parameters->departureTimeTo->format('H:i');

        if (isset($parameters->arrivalTimeFrom))
            $apiParameters['atime_from'] = $parameters->arrivalTimeFrom->format('H:i');

        if (isset($parameters->arrivalTimeTo))
            $apiParameters['atime_to'] = $parameters->arrivalTimeTo->format('H:i');

        if (isset($parameters->returnDepartureTimeFrom))
            $apiParameters['ret_dtime_from'] = $parameters->returnDepartureTimeFrom->format('H:i');

        if (isset($parameters->returnDepartureTimeTo))
            $apiParameters['ret_dtime_to'] = $parameters->returnDepartureTimeTo->format('H:i');

        if (isset($parameters->returnArrivalTimeFrom))
            $apiParameters['ret_atime_from'] = $parameters->returnArrivalTimeFrom->format('H:i');

        if (isset($parameters->returnArrivalTimeTo))
            $apiParameters['ret_atime_to'] = $parameters->returnArrivalTimeTo->format('H:i');

        if (isset($parameters->numAdults))
            $apiParameters['adults'] = $parameters->numAdults;

        if (isset($parameters->numChildren))
            $apiParameters['children'] = $parameters->numChildren;

        if (isset($parameters->numInfants))
            $apiParameters['infants'] = $parameters->numInfants;

        if (isset($parameters->priceFrom))
            $apiParameters['price_from'] = $parameters->priceFrom;

        if (isset($parameters->priceTo))
            $apiParameters['price_to'] = $parameters->priceTo;

        if (isset($parameters->onePerCity) && $parameters->onePerCity)
            $apiParameters['one_for_city'] = $parameters->onePerCity;

        if (isset($parameters->returnFromDifferentAirport))
            $apiParameters['ret_from_diff_airport'] = $parameters->returnFromDifferentAirport;

        return $apiParameters;
    }

    private function joinLocationsToKiwiFormat($locations) {
        return implode(',', $locations);
    }

    private function parseApiResponse($response) {
        $flights = [];

        foreach ($response['data'] as $trip) {
            $flight = new RoundFlight();
            $flight->price = $trip['price'];
            $flight->routes = $trip['routes'];
            $flight->cityFrom = $trip['cityFrom'];
            $flight->cityCodeFrom = $trip['cityCodeFrom'];
            $flight->cityTo = $trip['cityTo'];
            $flight->cityCodeTo = $trip['cityCodeTo'];
            $flight->airportFrom = $trip['flyFrom'];
            $flight->airportTo = $trip['flyTo'];
            $flight->airlines = $trip['airlines'];
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
        // The Kiwi API responses do not contain the timezone of the origin / destination,
        // but they contain the timestamp of the flight in both local and UTC time,
        // from which the time zone offset can be inferred
        $timeZoneOffsetSeconds = $timeStampLocal - $timeStampUTC;
        assert(($timeZoneOffsetSeconds % 60) === 0);
        $timezone = CarbonTimeZone::createFromMinuteOffset($timeZoneOffsetSeconds / 60);

        return Carbon::createFromTimestamp($timeStampLocal, $timezone);
    }

    private function applyPostApiQueryFilters($response, $parameters) {
        $haveOnePerCity = isset($parameters->onePerCity) && $parameters->onePerCity;
        $haveAnyPostApiQueryFilter =
            isset($parameters->minimumMinutesInDestination) ||
            (isset($parameters->returnFromDifferentCity) && !$parameters->returnFromDifferentCity);
        if ($haveOnePerCity && $haveAnyPostApiQueryFilter) {
            throw new FlightOperationException("Post-query API filters are not compatible with the one-per-city API option.");
        }

        if (isset($parameters->minimumMinutesInDestination)) {
            $response['data'] = array_values(array_filter($response['data'],
                function($trip) use ($parameters) {
                // TODO: Account time from-to airport to-from city (or is this out-of-scope?)
                $minutesInDestination = ($trip['route'][1]['dTimeUTC'] -
                                         $trip['route'][0]['aTimeUTC']) / 60;
                return $minutesInDestination >= $parameters->minimumMinutesInDestination;
            }));
        }

        if (isset($parameters->returnFromDifferentCity) && !$parameters->returnFromDifferentCity) {
            $response['data'] = array_values(array_filter($response['data'],
                function($trip) use ($parameters) {
                    return $trip['route'][0]['cityCodeTo'] == $trip['route'][1]['cityCodeFrom'];
            }));
        }

        return $response;
    }

    private function aggregateResults($flights, $parameters) {
        if (isset($parameters->groupBy) && $parameters->groupBy === FlightSearchQuery::GROUP_BY_DAY) {
            $flightsByDate = [];
            foreach ($flights as $f) {
                $dayKey = $f->journeyFlightDepartureTime->format("Y-m-d");
                $flightsByDate[$dayKey][] = $f;
            }
            ksort($flightsByDate);
            return $flightsByDate;
        }

        return $flights;
    }
}
