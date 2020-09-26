<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Builder class for flight booking requests.
 */
class FlightBookingRequestBuilder
{
    private $flightBooker;

    private $origins;
    private $destinations;
    private $daysBetweenFlights;
    private $minimumMinutesInDestination;
    private $startDate;
    private $endDate;
    private $numAdults;
    private $groupBy;

    public function __construct($flightBooker) {
        $this->flightBooker = $flightBooker;

        $this->bookingToken = null;
        $this->numBags = null;
        $this->numAdults = null;
        $this->numChildren = null;
        $this->numInfants = null;
    }

    function setBookingToken($bookingToken) {
        $this->bookingToken = $bookingToken;
        return $this;
    }

    function setNumBags($numBags) {
        $this->numBags = $numBags;
        return $this;
    }

    function setNumAdults($numAdults) {
        $this->numAdults = $numAdults;
        return $this;
    }

    function setNumChildren($numChildren) {
        $this->numChildren = $numChildren;
        return $this;
    }

    function setNumInfants($numInfants) {
        $this->numInfants = $numInfants;
        return $this;
    }

    function checkFlight() {
        $query = new FlightSearchQuery();
        $query->bookingToken = $this->bookingToken;
        $query->numBags = $this->numBags;
        $query->numAdults = $this->numAdults;
        $query->numChildren = $this->numChildren;
        $query->numInfants = $this->numInfants;
        return $this->flightBooker->checkFlight($query);
    }
}
