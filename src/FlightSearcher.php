<?php

namespace Ricadesign\LaravelKiwiScanner;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Ricadesign\LaravelKiwiScanner\Model\FlightScheduleParameter;
use Ricadesign\LaravelKiwiScanner\Model\RoundFlight;

/**
 * High level interface over the Kiwi Flights API.
 */
class FlightSearcher
{
    private $api;

    public function __construct( FlightApi $api) {
        $this->api = $api;
    }

    function getFlights(FlightSearchQuery $parameters)
    {
        if(count($parameters->destinations)>1){
            $apiParameters = [];
            //Concurrent petitions
            foreach ($parameters->destinations as $destination) {
                $cloneParameters = clone $parameters;
                $cloneParameters->destinations = $destination;
                //TODO: Fix in buildApiParameters
                $apiParameters[] = $this->buildApiParameters($cloneParameters);
            }
            $responses = $this->api->getConcurrentFlights($apiParameters);
            $flights = $this->mergeAndParseResponses($responses);
            return $flights;
        } else if(count($parameters->destinations) == 1) {
            //TODO: Fix in buildApiParameters
            $parameters->destinations = $parameters->destinations[0];
            $apiParameters = $this->buildApiParameters($parameters);
            $response = $this->api->getFlights($apiParameters);
            //echo '<pre>'; print_r($response); echo '</pre>';
            if (!isset($response['data'])) {
                // TODO: Improve error handling logic
                //echo '<pre>'; print_r($response); echo '</pre>';
                throw new FlightOperationException("Invalid API response");
            }
        }
        $response = $this->applyPostApiQueryFilters($response, $parameters);
        $flights = $this->parseApiResponse($response);
        return $this->aggregateResults($flights, $parameters);
    }

    private function buildApiParameters(FlightSearchQuery $parameters) {
        $apiParameters = [
            'v' => 3,
            'curr' => 'EUR',
            'locale' => 'es',
            'limit' => 2000,
            'max_stopovers' => 0,
        ];
        if (isset($parameters->destinations) && count($parameters->destinations)>1){
            $destinationCodes = $parameters->destinations[0];
            /** @var FlightScheduleParameter $flightSchedule FlightScheduleParameter */
            $flightSchedule =  $parameters->destinations[1];
        }
        if (isset($parameters->origins))
            $apiParameters['fly_from'] = $this->joinLocationsToKiwiFormat($parameters->origins);

        if (isset($destinationCodes))
            $apiParameters['fly_to'] = $this->joinLocationsToKiwiFormat($destinationCodes);

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

        if (isset($flightSchedule) && $flightSchedule != null){
            if($flightSchedule->flightDepartureTimeFrom)
                $apiParameters['dtime_from'] = $flightSchedule->flightDepartureTimeFrom->format('H:i');
            if($flightSchedule->flightDepartureTimeTo)
                $apiParameters['dtime_to'] = $flightSchedule->flightDepartureTimeTo->format('H:i');
            if($flightSchedule->flightReturnTimeFrom)
                $apiParameters['ret_dtime_from'] = $flightSchedule->flightReturnTimeFrom->format('H:i');
            if($flightSchedule->flightReturnTimeTo)
                $apiParameters['ret_dtime_to'] = $flightSchedule->flightReturnTimeTo->format('H:i');
        }

        if (isset($parameters->arrivalTimeFrom))
            $apiParameters['atime_from'] = $parameters->arrivalTimeFrom->format('H:i');

        if (isset($parameters->arrivalTimeTo))
            $apiParameters['atime_to'] = $parameters->arrivalTimeTo->format('H:i');

        if (isset($parameters->returnDepartureTimeFrom))
            $apiParameters['ret_dtime_from'] = $parameters->returnDepartureTimeFrom->format('H:i');

        if (isset($parameters->returnDepartureTimeTo))
            $apiParameters['ret_dtime_to'] = $parameters->returnDepartureTimeTo->format('H:i');

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

        if (isset($parameters->onePerDate) && $parameters->onePerDate)
            $apiParameters['one_per_date'] = $parameters->onePerDate;

        if (isset($parameters->returnFromDifferentAirport))
            $apiParameters['ret_from_diff_airport'] = $parameters->returnFromDifferentAirport;

        return $apiParameters;
    }

    private function joinLocationsToKiwiFormat($locations) {
        return is_string($locations) ? $locations : implode(',', $locations);
    }

    private function mergeAndParseResponses($responses) {
        $flights = [];
        foreach ($responses as $response ) {
            $flights = array_merge($flights, $this->parseApiResponse($response));
        }
        return $flights;
    }

    private function parseApiResponse($response) {
        $flights = [];
        $data = is_array($response) ? $response['data'] : $response->data;
        foreach ($data as $trip) {
            //HACK
            $trip = (array) $trip;
            $flight = new RoundFlight();
            $flight->id = $trip['id'];
            $flight->price = $trip['price'];
            $flight->routes = $trip['route'];
            $flight->cityFrom = $trip['cityFrom'];
            $flight->cityCodeFrom = $trip['cityCodeFrom'];
            $flight->cityTo = $trip['cityTo'];
            $flight->cityCodeTo = $trip['cityCodeTo'];
            $flight->airportFrom = $trip['flyFrom'];
            $flight->airportTo = $trip['flyTo'];
            $flight->airlines = $trip['airlines'];
            $journey = (array)$trip['route'][0];
            $flight->journeyFlightDepartureTime = $this->parseTimeStampAndInferTimeZone(
                $journey['local_departure'], $journey['utc_departure']);
            $flight->journeyFlightArrivalTime = $this->parseTimeStampAndInferTimeZone(
                $journey['local_arrival'], $journey['utc_arrival']);
            $return = (array)$trip['route'][1];
            $flight->returnFlightDepartureTime = $this->parseTimeStampAndInferTimeZone(
                $return['local_departure'], $return['utc_departure']);
            $flight->returnFlightArrivalTime = $this->parseTimeStampAndInferTimeZone(
                $return['local_arrival'], $return['utc_arrival']);
            // TODO: Account time from-to airport to-from city (or is this out-of-scope?)
            $flight->minutesInDestination = $flight->returnFlightDepartureTime->diffInMinutes($flight->journeyFlightArrivalTime);
            $flight->bookingToken = $trip['booking_token'];
            $flight->departureAirline = $journey['airline'];
            $flight->returnAirline = $return['airline'];
            $flights[] = $flight;
        }
        return $flights;
    }

    private function parseTimeStampAndInferTimeZone($timeStampLocal, $timeStampUTC) {
        // The Kiwi API responses do not contain the timezone of the origin / destination,
        // but they contain the timestamp of the flight in both local and UTC time,
        // from which the time zone offset can be inferred
        $timeStampLocal = new Carbon($timeStampLocal);
        $timeStampUTC = new Carbon($timeStampUTC);

        $timeZoneOffsetSeconds = $timeStampLocal->timestamp - $timeStampUTC->timestamp;
        assert(($timeZoneOffsetSeconds % 60) === 0);
        $timezone = CarbonTimeZone::createFromMinuteOffset($timeZoneOffsetSeconds / 60);
        return Carbon::createFromTimestamp($timeStampLocal->timestamp, $timezone);
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
                $minutesInDestination = ($trip['route'][1]['utc_departure'] -
                                         $trip['route'][0]['utc_arrival']) / 60;
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
