<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Represents an airport as a flight origin or destination location.
 */
class AirportFlightLocation extends FlightLocation
{
    private $airportCode;

    public function  __construct($airportCode) {
        $this->airportCode = $airportCode;
    }

    function getKiwiFormat() {
        return "airport:" . $this->airportCode;
    }
}
