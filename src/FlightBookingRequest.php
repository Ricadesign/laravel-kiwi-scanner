<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Information necessary to book (or check) a flight.
 */
class FlightBookingRequest
{
    //TODO: Define all needed attributes for wizard
    public $bookingToken;
    public $numBags;
    public $numAdults;
    public $numChildren;
    public $numInfants;
}
