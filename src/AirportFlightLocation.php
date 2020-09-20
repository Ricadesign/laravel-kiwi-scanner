<?php

namespace Ricadesign\LaravelKiwiScanner;

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
