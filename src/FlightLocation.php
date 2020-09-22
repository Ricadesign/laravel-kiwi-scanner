<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Base class to represent different kinds of flight origins or destinations
 * (e.g. a city or an airport).
 */
abstract class FlightLocation
{
    abstract function getKiwiFormat();
}
