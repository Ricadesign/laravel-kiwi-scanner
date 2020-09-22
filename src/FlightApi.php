<?php

namespace Ricadesign\LaravelKiwiScanner;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Thin layer to make requests HTTP to the Kiwi API.
 * The API Token (aka. affiliate / partner ID) is added automatically.
 */
class FlightApi
{
    const TOKEN_PARAM_SEARCH = 'partner';
    const TOKEN_PARAM_BOOKING = 'affily';

    private $apiToken;

    function __construct() {
        $this->apiToken = config('kiwi-scanner.partner');
    }

    /*****************
     * LOCATIONS API *
     *****************/

    const GET_FLIGHTS_ENDPOINT = 'https://api.skypicker.com/flights';
    const GETFLIGHTS_CACHE_TIMEOUT = 300; // seconds

    function getFlights($parameters)
    {
        // TODO: This seems unreliable, are the parameters always serialized the same way?
        //       (Also, the cache could be on a higher level of abstraction)
        $key = json_encode($parameters);
        return Cache::remember("getflights_" . $key, self::GETFLIGHTS_CACHE_TIMEOUT,
            function() use($parameters) {
            $parametersWithAuth = array_merge($parameters,
                [self::TOKEN_PARAM_SEARCH => $this->apiToken]);
            $response = Http::get(self::GET_FLIGHTS_ENDPOINT, $parametersWithAuth);
            return $response->json();
        });
    }

    /***************
     * BOOKING API *
     ***************/

    const CHECK_FLIGHTS_ENDPOINT = 'https://booking-api.skypicker.com/api/v0.1/check_flights';

    function checkFlights($parameters)
    {
        $parametersWithAuth = array_merge($parameters,
            [self::TOKEN_PARAM_BOOKING => $this->apiToken]);
        $response = Http::get(self::CHECK_FLIGHTS_ENDPOINT, $parametersWithAuth);
        return $response->json();
    }
}
