<?php

namespace Ricadesign\LaravelKiwiScanner;

use Ricadesign\LaravelKiwiScanner\Model\FlightScheduleParameter;

/**
 * Information about a flight search query.
 */
class FlightSearchQuery
{
    public $origins;
    public $destinations;
    public $maxStopovers;
    public $nightsInDestinationFrom;
    public $nightsInDestinationTo;
    public $minimumMinutesInDestination;
    public $startDate;
    public $endDate;
    public $returnFrom;
    public $returnTo;
    public $numAdults;
    public $numChildren;
    public $numInfants;
    public $priceFrom;
    public $priceTo;
    public $onePerCity;
    public $groupBy;
    public $returnFromDifferentAirport;
    public $returnFromDifferentCity;

    const GROUP_BY_DAY = 'day';
}
