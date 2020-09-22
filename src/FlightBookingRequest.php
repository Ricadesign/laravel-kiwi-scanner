<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Information necessary to book (or check) a flight.
 */
class FlightBookingRequest
{
    public $bookingToken;
    public $numBags;
    public $numAdults;
    public $numChildren;
    public $numInfants;
}
