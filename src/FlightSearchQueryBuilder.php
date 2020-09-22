<?php

namespace Ricadesign\LaravelKiwiScanner;

/**
 * Builder class for flight search queries.
 */
class FlightSearchQueryBuilder
{
    private $queryProcessor;

    private $origins;
    private $destinations;
    private $daysBetweenFlights;
    private $minimumMinutesInDestination;
    private $startDate;
    private $endDate;
    private $numAdults;
    private $groupBy;

    public function __construct($queryProcessor) {
        $this->queryProcessor = $queryProcessor;

        $this->origins = [];
        $this->destinations = [];
        $this->daysBetweenFlights = null;
        $this->minimumMinutesInDestination = null;
        $this->startDate = null;
        $this->endDate = null;
        $this->numAdults = null;
        $this->groupBy = null;
    }

    function addOrigin($origin) {
        $this->origins[] = $origin;
        return $this;
    }

    function addDestination($destination) {
        $this->destinations[] = $destination;
        return $this;
    }

    function setDaysBetweenFlights($daysBetweenFlights) {
        $this->daysBetweenFlights = $daysBetweenFlights;
        return $this;
    }

    function setMinimumMinutesInDestination($minimumMinutesInDestination) {
        $this->minimumMinutesInDestination = $minimumMinutesInDestination;
        return $this;
    }

    function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    function setNumAdults($numAdults) {
        $this->numAdults = $numAdults;
        return $this;
    }

    function groupByDay() {
        $this->groupBy = FlightSearchQuery::GROUP_BY_DAY;
        return $this;
    }

    function getFlights() {
        $query = new FlightSearchQuery();
        $query->origins =  $this->origins;
        $query->destinations =  $this->destinations;
        $query->daysBetweenFlights =  $this->daysBetweenFlights;
        $query->minimumMinutesInDestination =  $this->minimumMinutesInDestination;
        $query->startDate =  $this->startDate;
        $query->endDate =  $this->endDate;
        $query->numAdults =  $this->numAdults;
        $query->groupBy =  $this->groupBy;
        return $this->queryProcessor->getFlights($query);
    }
}
