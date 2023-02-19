<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * High level interface over the Kiwi Booking API.
 */
class FlightBooker
{
    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    function checkFlight($flight)
    {
        $apiParameters = [
            'booking_token' => $flight->bookingToken,
            'bnum' => $flight->numBags,
            'adults' => $flight->numAdults,
            'children' => $flight->numChildren,
            'infants' => $flight->numInfants,
        ];

        $response = $this->api->checkFlights($apiParameters);
        $checkResult = new FlightCheckResult();
        
        $checkResult->flightChecked = $response['flights_checked'];
        $checkResult->flightInvalid = $response['flights_invalid'];
        $checkResult->price = $response['total'];
        $checkResult->session_id = $response['session_id'];
        $checkResult->booking_token = $response['booking_token'];
        return $checkResult;
    }

    public function saveBooking($booking)
    {
        $apiParameters = [
            "health_declaration_checked" => true,
            "bnum" => '0',
            'lang' => 'es',
            'passengers' => $this->buildPassengers($booking->passengers),
            'locale' => 'es',
            'booking_token' => $booking->booking_token,
            'session_id' => $booking->session_id,
            //TODO: BUILD BAGGAGE
            'baggage' => [
                (object) [
                  "combination" => [
                    "indices" => [
                      0,
                      1
                    ],
                    "category" => "hand_bag",
                    "conditions" => (object) [
                      "passenger_groups" => [
                        "adult",
                        "child"
                      ]
                    ],
                    "price" => (object) [
                      "currency" => "EUR",
                      "amount" => 0,
                      "base" => 0,
                      "service" => 0,
                      "service_flat" => 0,
                      "merchant" => 0
                    ]
                  ],
                  "passengers" => [
                    0,
                    1
                  ]
                ]
              ],
        ];

        dd($apiParameters);
        $response = $this->api->saveBooking($apiParameters);
        dd($response);
        return $response;
    }

    private function buildPassengers($passengers)
    {
        $passengers_formatted = [];

        foreach ($passengers as $passenger) {
            $passengers_formatted[] = (object) [
                "birthday" => $passenger->date_of_birth->format('Y-m-d'),
                "category" => $passenger->type,
                "cardno" => $passenger->card_id,
                "expiration" => $passenger->expiration_date->format('Y-m-d'),
                "email" => $passenger->email,
                "name" => $passenger->name,
                "surname" => $passenger->last_name,
                "nationality" => "CZ",
                "phone" => "+34$passenger->phone",
                "title" => "ms"
            ];
        }

        return $passengers_formatted;
    }


}
