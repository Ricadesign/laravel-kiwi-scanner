<?php

namespace Ricadesign\LaravelKiwiScanner;

use Ricadesign\LaravelKiwiScanner\Model\Passenger;

class PassengersManager
{
    public function __construct(private FlightApi $api)
    {
    }

    function getPassengers($bookingId)
    {
        return $this->api->getBookingPassengers($bookingId);
    }

    /**
     * @param string $bookingId The ID of the booking.
     * @param Passenger[] $passengers An array of Passenger objects.
     */
    function updatePassengers(string $bookingId, array $passengers)
    {
        $bookingPassengers = $this->getPassengers($bookingId)['passengers'];

        $body = "{";

            foreach ($bookingPassengers as $key => $value) {
                $passengerId = strval($value['id']);
                $documentExpiry = $passengers[$key]->document_expiry;
                $documentNumber = $passengers[$key]->document_number;

                $body .= '
                    "' . $passengerId . '": {
                      "document_expiry": "' . $documentExpiry . '",
                      "document_number": "' . $documentNumber . '"
                    }';
                $body .= $key < (count($bookingPassengers) - 1) ? ',' : '';
            }

            $body .= '}';

        return $this->api->updateBookingPassengers($bookingId, $body);
    }
}
