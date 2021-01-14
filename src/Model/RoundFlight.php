<?php

namespace Ricadesign\LaravelKiwiScanner\Model;

/**
 * Information about a round flight (i.e. both journey and return).
 */
class RoundFlight
{
    public $price;
    public $routes;
    public $cityFrom;
    public $cityCodeFrom;
    public $cityTo;
    public $cityCodeTo;
    public $airportFrom;
    public $airportTo;
    public $journeyFlightDepartureTime;
    public $journeyFlightArrivalTime;
    public $returnFlightDepartureTime;
    public $returnFlightArrivalTime;
    public $minutesInDestination;
    public $bookingToken;
    public $airlines;
}
