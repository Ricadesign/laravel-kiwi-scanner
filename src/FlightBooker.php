<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * High level interface over the Kiwi Booking API.
 */
class FlightBooker
{
  private $api;

  public function __construct($api)
  {
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
      'passengers' => $booking->passengers,
      'locale' => 'es',
      'booking_token' => $booking->booking_token,
      'session_id' => $booking->session_id,
      'baggage' => $booking->baggage
    ];

    $response = $this->api->saveBooking($apiParameters);
    return $response;
  }

  public function confirmPayment($booking)
  {
    $apiParameters = [
      'booking_id' => $booking->booking_id,
      'transaction_id' => $booking->transaction_id,
    ];

    $response = $this->api->confirmPayment($apiParameters);
    return $response;
  }
  
}
