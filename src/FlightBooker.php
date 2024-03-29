<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * High level interface over the Kiwi Booking API.
 */
class FlightBooker
{
  private FlightApi $api;

  public function __construct(FlightApi $api)
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

    $response = $this->api->checkFlight($apiParameters);
    $checkResult = new FlightCheckResult();

    foreach ($response['flights'] as $flight) {
      $checkResult->airlines[] = $flight['airline']['code'];

    }

    $checkResult->flightChecked = $response['flights_checked'];
    $checkResult->flightInvalid = $response['price_change'];
    $checkResult->priceChange = $response['flights_invalid'];
    $checkResult->price = $response['total'];
    $checkResult->session_id = $response['session_id'];
    $checkResult->booking_token = $response['booking_token'];
    $checkResult->baggage = $response['baggage'];
    $checkResult->document_need = $response['document_options']['document_need'] == 2;
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

    $bookingResult = new FlightBookingResult();
    $bookingResult->booking_id = $response['booking_id'];
    $bookingResult->transaction_id = $response['transaction_id'];
    $bookingResult->eur_payment_price = $response['eur_payment_price'];

    return $bookingResult;
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
