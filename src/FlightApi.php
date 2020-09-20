<?php

namespace Ricadesign\LaravelKiwiScanner;

use Illuminate\Support\Facades\Http;

class FlightApi
{
    function run($parameters)
    {
        $response = Http::get('https://api.skypicker.com/flights', $parameters);
        $json = $response->json();
        return $json;
    }
}
