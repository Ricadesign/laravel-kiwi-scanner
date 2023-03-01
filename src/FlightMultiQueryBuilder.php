<?php

namespace Ricadesign\LaravelKiwiScanner;

use Ricadesign\LaravelKiwiScanner\Model\FlightScheduleParameter;
/**
 * Builder class for flight search queries.
 */
class FlightMultiQueryBuilder
{
    private FlightSearcher $queryProcessor;

    private $flights;

    public function __construct($queryProcessor) {
        $this->queryProcessor = $queryProcessor;
    }

    function addFlight(){
        $this->flights[] = (object)[
           "origins" => [],
           "destinations" => [],
           "maxStopovers" => null,
           "nightsInDestinationFrom" => null,
           "nightsInDestinationTo" => null,
           "minimumMinutesInDestination" => null,
           "startDate" => null,
           "endDate" => null,
           "returnFrom" => null,
           "returnTo" => null,
           "numAdults" => null,
           "numChildren" => null,
           "numInfants" => null,
           "priceFrom" => null,
           "priceTo" => null,
           "groupBy" => null,
           "onePerCity" => false,
           "onePerDate" => false
        ];
    }

    function addOrigin($index, $origin) {
        $this->flights[$index]->origins[] = $origin;
        return $this;
    }

    function addDestination($index, string $destination, FlightScheduleParameter $schedule = null) {
        $this->flights[$index]->destinations = [$destination, $schedule];
        return $this;
    }

    function setDestinations($index, array $destinations) {
        $this->flights[$index]->destinations = $destinations;
        return $this;
    }

    function setMaxStopovers($index, $maxStopovers) {
        $this->flights[$index]->maxStopovers = $maxStopovers;
        return $this;
    }

    function setNightsInDestinationFrom($index, $nightsInDestinationFrom) {
        $this->flights[$index]->nightsInDestinationFrom = $nightsInDestinationFrom;
        return $this;
    }

    function setNightsInDestinationTo($index, $nightsInDestinationTo) {
        $this->flights[$index]->nightsInDestinationTo = $nightsInDestinationTo;
        return $this;
    }

    function setNightsInDestination($index, $num) {
        $this->setNightsInDestinationFrom($index, $num);
        $this->setNightsInDestinationTo($index, $num);
        return $this;
    }

    function setReturnFrom($index, $returnFrom) {
        $this->flights[$index]->returnFrom = $returnFrom;
        return $this;
    }

    function setReturnTo($index, $returnTo) {
        $this->flights[$index]->returnTo = $returnTo;
        return $this;
    }

    function setMinimumMinutesInDestination($index, $minimumMinutesInDestination) {
        $this->flights[$index]->minimumMinutesInDestination = $minimumMinutesInDestination;
        return $this;
    }

    function setStartDate($index, $startDate) {
        $this->flights[$index]->startDate = $startDate;
        return $this;
    }

    function setEndDate($index, $endDate) {
        $this->flights[$index]->endDate = $endDate;
        return $this;
    }

    function setNumAdults($index, $numAdults) {
        $this->flights[$index]->numAdults = $numAdults;
        return $this;
    }

    function setNumChildren($index, $numChildren) {
        $this->flights[$index]->numChildren = $numChildren;
        return $this;
    }

    function setNumInfants($index, $numInfants) {
        $this->flights[$index]->numInfants = $numInfants;
        return $this;
    }

    function setPriceFrom($index, $priceFrom) {
        $this->flights[$index]->priceFrom = $priceFrom;
        return $this;
    }

    function setPriceTo($index, $priceTo) {
        $this->flights[$index]->priceTo = $priceTo;
        return $this;
    }

    function groupByDay($index) {
        $this->flights[$index]->groupBy = FlightSearchQuery::GROUP_BY_DAY;
        return $this;
    }
    
    function setOnePerCity($index, $onePerCity = true) {
        $this->flights[$index]->onePerCity = $onePerCity;
        return $this;
    }
    function setOnePerDate($index, $onePerDate = true) {
        $this->flights[$index]->onePerDate = $onePerDate;
        return $this;
    }

    function setReturnFromDifferentAirport($index, $returnFromDifferentAirport = true) {
        $this->flights[$index]->returnFromDifferentAirport = $returnFromDifferentAirport;
        return $this;
    }

    function setReturnFromDifferentCity($index, $returnFromDifferentCity = true) {
        $this->flights[$index]->returnFromDifferentCity = $returnFromDifferentCity;
        return $this;
    }

    function getFlights() {
        $queries = [];

        foreach ($this->flights as $flight) {
            $query = new FlightSearchQuery();
            $query->origins = $flight->origins;
            $query->destinations = $flight->destinations;
            $query->maxStopovers = $flight->maxStopovers;
            $query->nightsInDestinationFrom = $flight->nightsInDestinationFrom;
            $query->nightsInDestinationTo = $flight->nightsInDestinationTo;
            $query->returnFrom = $flight->returnFrom;
            $query->returnTo = $flight->returnTo;
            $query->minimumMinutesInDestination = $flight->minimumMinutesInDestination;
            $query->startDate = $flight->startDate;
            $query->endDate = $flight->endDate;
            $query->numAdults = $flight->numAdults;
            $query->numChildren = $flight->numChildren;
            $query->numInfants = $flight->numInfants;
            $query->priceFrom = $flight->priceFrom;
            $query->priceTo = $flight->priceTo;
            $query->onePerCity = $flight->onePerCity;
            $query->onePerDate = $flight->onePerDate;
            // $query->returnFromDifferentAirport = $flight->returnFromDifferentAirport;
            // $query->returnFromDifferentCity = $flight->returnFromDifferentCity;
            $query->groupBy = $flight->groupBy;

            $queries[] = $query;
        }

        return $this->queryProcessor->getFlightsMulti($queries);
    }
}
