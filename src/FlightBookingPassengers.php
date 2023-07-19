<?php

namespace Ricadesign\LaravelKiwiScanner;

class FlightBookingPassengers {
    public function __construct(private FlightApi $api) {}


    function getPassengers($bookingId) {
        return $this->api->getBookingPassengers($bookingId);
    }

    function updatePassengers($bookingId, $passengers) {
        return $this->api->updateBookingPassengers($bookingId, $passengers);
    }
}
