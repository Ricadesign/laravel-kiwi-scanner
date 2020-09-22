<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * High level interface over the Kiwi Booking API.
 */
class FlightBooker
{
    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    function checkFlight($flight)
    {
        $apiParameters = [
            'v' => 3,
            'currency' => 'EUR',
            'booking_token' => $flight->bookingToken,
            'bnum' => $flight->numBags,
            'pnum' => $flight->numAdults + $flight->numChildren + $flight->numInfants,
            'adults' => $flight->numAdults,
            'children' => $flight->numChildren,
            'infants' => $flight->numInfants,
        ];

        $response = $this->api->checkFlights($apiParameters);
        //echo '<pre>'; print_r($response); echo '</pre>';

        $checkResult = new FlightCheckResult();
        $checkResult->flightChecked = $response['flights_checked'];
        $checkResult->flightInvalid = $response['flights_invalid'];
        $checkResult->price = $response['total'];
        return $checkResult;
    }
}
