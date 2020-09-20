<?php

namespace Ricadesign\LaravelKiwiScanner;

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
