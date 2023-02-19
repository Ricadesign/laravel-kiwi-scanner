<?php

namespace Ricadesign\LaravelKiwiScanner;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Log;

/**
 * Thin layer to make requests HTTP to the Kiwi API.
 */
class FlightApi
{
    const TOKEN_AUTH = 'apikey';
    const TOKEN_PARAM_BOOKING = 'affily';

    private $apiToken;

    function __construct() {
        $this->apiToken = config('kiwi-scanner.partner');
    }

    /*****************
     * SEARCH API *
     *****************/
     const GET_FLIGHTS_ENDPOINT = 'https://api.tequila.kiwi.com/v2/search';
    const GETFLIGHTS_CACHE_TIMEOUT = 300; // seconds

    function getFlights($parameters)
    {
        // TODO: This seems unreliable, are the parameters always serialized the same way?
        //       (Also, the cache could be on a higher level of abstraction)
        $key = json_encode($parameters);
        return Cache::remember("getflights_" . $key, self::GETFLIGHTS_CACHE_TIMEOUT,
            function() use($parameters) {
            $response = Http::withHeaders([
                self::TOKEN_AUTH => $this->apiToken
            ])->get(self::GET_FLIGHTS_ENDPOINT, $parameters);
            $uri = $response->effectiveUri();
            return $response->json();
        });
    }

    public function getConcurrentFlights($parametersArr)
    {
        $client = new Client(['base_uri' => self::GET_FLIGHTS_ENDPOINT]);
        $headers = [self::TOKEN_AUTH => $this->apiToken];
        $promises = [];
        foreach ($parametersArr as $parameters) {
            $promises[] = $client->getAsync('', ['headers' => $headers, 'query' => $parameters]);
        }
        $responses = Promise\Utils::settle($promises)->wait();
        return array_map(function ($response){return json_decode($response['value']->getBody());}, $responses);
    }
    /***************
     * BOOKING API *
     ***************/

     const CHECK_FLIGHTS_ENDPOINT = 'https://api.tequila.kiwi.com/v2/booking/check_flights';

     function checkFlights($parameters)
     {
         $response = Http::withHeaders([
             self::TOKEN_AUTH => $this->apiToken
         ])->get(self::CHECK_FLIGHTS_ENDPOINT, $parameters);
         return $response->json();
     }

     const SAVE_BOOKING_ENDPOINT = 'https://api.tequila.kiwi.com/v2/booking/save_booking';

     public function saveBooking($parameters)
     {
        $response = Http::withHeaders([
            self::TOKEN_AUTH => $this->apiToken
        ])->get(self::CHECK_FLIGHTS_ENDPOINT, $parameters);
        return $response->json();
     }
}
