<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Represents a city as a flight origin or destination location.
 */
class CityFlightLocation extends FlightLocation
{
    private $cityCode;

    public function __construct($cityCode) {
        $this->cityCode = $cityCode;
    }

    function getKiwiFormat() {
        return "city:" . $this->cityCode;
    }
}
