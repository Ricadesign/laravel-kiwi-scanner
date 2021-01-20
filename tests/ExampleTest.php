<?php

namespace Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Ricadesign\LaravelKiwiScanner\FlightApi;
use Ricadesign\LaravelKiwiScanner\FlightSearcher;
use Ricadesign\LaravelKiwiScanner\FlightSearchQueryBuilder;
use Ricadesign\LaravelKiwiScanner\Model\FlightScheduleParameter;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    { 
        $queryBuilder = new  FlightSearchQueryBuilder( new FlightSearcher(new FlightApi));
        $queryBuilder->addOrigin('MAD')->setNightsInDestination(0);
        $date = new Carbon('22-01-2021 12:00:00');
        $scheduleParameter = new FlightScheduleParameter("10:10:00", "10:20:00", "22:00:00", "23:00:00");
        $queryBuilder->addDestination('PMI', $scheduleParameter);
        $queryBuilder->addDestination('ROM');
        $queryBuilder->setStartDate($date)->setEndDate($date);
        $flights = $queryBuilder->getFlights();
        dd($flights[0]);
        $this->assertTrue(true);
    }
}
