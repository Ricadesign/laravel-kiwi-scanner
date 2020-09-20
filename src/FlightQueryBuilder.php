<?php

namespace Ricadesign\LaravelKiwiScanner;

class FlightQueryBuilder
{
    private $queryProcessor;

    private $origins;
    private $destinations;
    private $daysBetweenFlights;
    private $minimumMinutesInDestination;
    private $startDate;
    private $endDate;
    private $groupBy;

    public function __construct($queryProcessor) {
        $this->queryProcessor = $queryProcessor;

        $this->origins = [];
        $this->destinations = [];
        $this->daysBetweenFlights = null;
        $this->minimumMinutesInDestination = null;
        $this->startDate = null;
        $this->endDate = null;
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

    function groupByDay() {
        $this->groupBy = 'day';
        return $this;
    }

    function getFlights() {
        return $this->queryProcessor->getFlights([
            'origins' => $this->origins,
            'destinations' => $this->destinations,
            'daysBetweenFlights' => $this->daysBetweenFlights,
            'minimumMinutesInDestination' => $this->minimumMinutesInDestination,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'groupBy' => $this->groupBy,
        ]);
    }
}
