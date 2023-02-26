<?php

namespace Ricadesign\LaravelKiwiScanner;

class FlightCheckResult
{
    /** if true, the check was successful, otherwise need to try again ("please wait") */
    public $flightChecked;
    /** if true, the flight was cancelled */
    public $flightInvalid;
    /** Verified price for the flight */
    public $price;

    public $session_id;
    public $booking_token;

    public $document_need;


}
