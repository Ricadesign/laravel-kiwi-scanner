<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Information about a flight search query.
 */
class FlightSearchQuery
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
