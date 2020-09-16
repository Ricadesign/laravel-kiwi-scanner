<?php

namespace Ricadesign\LaravelKiwiScanner;

use App\RoundFlight;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class Api
{
    function getFlights(array $parameters)
    {
        $response = Http::get('https://api.skypicker.com/flights', array_merge($parameters, [
            'partner' => 'picky',
            'curr' => 'EUR',
            'locale' => 'es',
            'flight_type' => 'round',
            'max_stopovers' => 0, //Optional
            'nights_in_dst_from' => 0,
            'nights_in_dst_to' => 0,
            'date_from' => '16/09/2020',
            // 'date_to' => '16/09/2020',
            // 'return_from' => '16/09/2020',
            // 'return_to' => '16/09/2020'
        ]));
        $json = $response->json();
        $flights = [];
        foreach ($json['data'] as $key => $trip) {
            $flight = new RoundFlight();
            $flight->price = $trip['price'];
            $flight->routes = $trip['routes'];
            $flight->cityFrom = $trip['cityFrom'];
            $flight->cityTo = $trip['cityTo'];
            $flight->destinationTime = Carbon::createFromTimestamp($trip['dTime']);
            $flight->arrivalTime = Carbon::createFromTimestamp($trip['route'][1]['aTime']);
            // $flight-> $trip['quality'];
            $flights[] = $flight;
        }
        $flights = collect($flights);
        return $flights->sortBy('destinationTime');
    }
    public function getFlightsFromTo($from, $to)
    {
        return $this->getFlights([
            'fly_from' => $from,
            'fly_to' => $to
        ]);
    }
}
