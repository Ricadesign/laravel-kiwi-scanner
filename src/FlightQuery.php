<?php

namespace Ricadesign\LaravelKiwiScanner;

class FlightQuery
{
    public $origins;
    public $destinations;
    public $daysBetweenFlights;
    public $minimumMinutesInDestination;
    public $startDate;
    public $endDate;
    public $groupBy;

    const GROUP_BY_DAY = 'day';
}
