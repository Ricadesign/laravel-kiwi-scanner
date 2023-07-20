<?php

namespace Ricadesign\LaravelKiwiScanner\Model;

/**
 * Information about a round flight (i.e. both journey and return).
 */
class Passenger
{
    public $id;
    public $birth_date;
    public $category;
    public $document_expiry;
    public $document_number;
    public $first_name;
    public $is_contact_passenger;
    public $last_name;
    public $middle_name;
    public $nationality;
    public $title;

    public function __construct(string $document_number, string $document_expiry, ) {
        $this->document_number = $document_number;
        $this->document_expiry = $document_expiry;
    }
}
