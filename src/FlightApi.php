<?php

namespace Ricadesign\LaravelKiwiScanner;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FlightApi
{
    const FLIGHTS_ENDPOINT = 'https://api.skypicker.com/flights';
    const TOKEN_PARAM_NAME = 'partner';
    const CACHE_TIMEOUT = 300; // seconds

    private $apiToken;

    function __construct() {
        $this->apiToken = config('kiwi-scanner.partner');
    }

    function getFlights($parameters)
    {
        // TODO: This seems unreliable, are the parameters always serialized the same way?
        //       (Also, the cache could be on a higher level of abstraction)
        $key = json_encode($parameters);
        return Cache::remember($key, self::CACHE_TIMEOUT, function() use($parameters) {
            $parametersWithAuth = array_merge($parameters, [self::TOKEN_PARAM_NAME => $this->apiToken]);
            $response = Http::get(self::FLIGHTS_ENDPOINT, $parametersWithAuth);
            $json = $response->json();
            return $json;
        });
    }
}
