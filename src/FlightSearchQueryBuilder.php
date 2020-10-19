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
    private $onePerCity;

    public function __construct($queryProcessor) {
        $this->queryProcessor = $queryProcessor;

        $this->origins = [];
        $this->destinations = [];
        $this->maxStopovers = null;
        $this->nightsInDestinationFrom = null;
        $this->nightsInDestinationTo = null;
        $this->minimumMinutesInDestination = null;
        $this->startDate = null;
        $this->endDate = null;
        $this->returnFrom = null;
        $this->returnTo = null;
        $this->departureTimeFrom = null;
        $this->departureTimeTo = null;
        $this->arrivalTimeFrom = null;
        $this->arrivalTimeTo = null;
        $this->returnDepartureTimeFrom = null;
        $this->returnDepartureTimeTo = null;
        $this->returnArrivalTimeFrom = null;
        $this->returnArrivalTimeTo = null;
        $this->numAdults = null;
        $this->numChildren = null;
        $this->numInfants = null;
        $this->priceFrom = null;
        $this->priceTo = null;
        $this->groupBy = null;
        $this->onePerCity = false;
    }

    function addOrigin($origin) {
        $this->origins[] = $origin;
        return $this;
    }

    function addDestination($destination) {
        $this->destinations[] = $destination;
        return $this;
    }

    function setDestinations($destinations) {
        $this->destinations = $destinations;
        return $this;
    }

    function setMaxStopovers($maxStopovers) {
        $this->maxStopovers = $maxStopovers;
        return $this;
    }

    function setNightsInDestinationFrom($nightsInDestinationFrom) {
        $this->nightsInDestinationFrom = $nightsInDestinationFrom;
        return $this;
    }

    function setNightsInDestinationTo($nightsInDestinationTo) {
        $this->nightsInDestinationTo = $nightsInDestinationTo;
        return $this;
    }

    function setNightsInDestination($num) {
        $this->setNightsInDestinationFrom($num);
        $this->setNightsInDestinationTo($num);
        return $this;
    }

    function setReturnFrom($returnFrom) {
        $this->returnFrom = $returnFrom;
        return $this;
    }

    function setReturnTo($returnTo) {
        $this->returnTo = $returnTo;
        return $this;
    }

    function setDepartureTimeFrom($departureTimeFrom) {
        $this->departureTimeFrom = $departureTimeFrom;
        return $this;
    }

    function setDepartureTimeTo($departureTimeTo) {
        $this->departureTimeTo = $departureTimeTo;
        return $this;
    }

    function setArrivalTimeFrom($arrivalTimeFrom) {
        $this->arrivalTimeFrom = $arrivalTimeFrom;
        return $this;
    }

    function setArrivalTimeTo($arrivalTimeTo) {
        $this->arrivalTimeTo = $arrivalTimeTo;
        return $this;
    }

    function setReturnDepartureTimeFrom($returnDepartureTimeFrom) {
        $this->returnDepartureTimeFrom = $returnDepartureTimeFrom;
        return $this;
    }

    function setReturnDepartureTimeTo($returnDepartureTimeTo) {
        $this->returnDepartureTimeTo = $returnDepartureTimeTo;
        return $this;
    }

    function setReturnArrivalTimeFrom($returnArrivalTimeFrom) {
        $this->returnArrivalTimeFrom = $returnArrivalTimeFrom;
        return $this;
    }

    function setReturnArrivalTimeTo($returnArrivalTimeTo) {
        $this->returnArrivalTimeTo = $returnArrivalTimeTo;
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

    function setNumChildren($numChildren) {
        $this->numChildren = $numChildren;
        return $this;
    }

    function setNumInfants($numInfants) {
        $this->numInfants = $numInfants;
        return $this;
    }

    function setPriceFrom($priceFrom) {
        $this->priceFrom = $priceFrom;
        return $this;
    }

    function setPriceTo($priceTo) {
        $this->priceTo = $priceTo;
        return $this;
    }

    function groupByDay() {
        $this->groupBy = FlightSearchQuery::GROUP_BY_DAY;
        return $this;
    }
    
    function setOnePerCity($onePerCity = true) {
        $this->onePerCity = $onePerCity;
        return $this;
    }

    function setReturnFromDifferentAirport($returnFromDifferentAirport = true) {
        $this->returnFromDifferentAirport = $returnFromDifferentAirport;
        return $this;
    }

    function setReturnFromDifferentCity($returnFromDifferentCity = true) {
        $this->returnFromDifferentCity = $returnFromDifferentCity;
        return $this;
    }

    function getFlights() {
        $query = new FlightSearchQuery();
        $query->origins = $this->origins;
        $query->destinations = $this->destinations;
        $query->maxStopovers = $this->maxStopovers;
        $query->nightsInDestinationFrom = $this->nightsInDestinationFrom;
        $query->nightsInDestinationTo = $this->nightsInDestinationTo;
        $query->returnFrom = $this->returnFrom;
        $query->returnTo = $this->returnTo;
        $query->minimumMinutesInDestination = $this->minimumMinutesInDestination;
        $query->startDate = $this->startDate;
        $query->endDate = $this->endDate;
        $query->departureTimeFrom = $this->departureTimeFrom;
        $query->departureTimeTo = $this->departureTimeTo;
        $query->arrivalTimeFrom = $this->arrivalTimeFrom;
        $query->arrivalTimeTo = $this->arrivalTimeTo;
        $query->returnDepartureTimeFrom = $this->returnDepartureTimeFrom;
        $query->returnDepartureTimeTo = $this->returnDepartureTimeTo;
        $query->returnArrivalTimeFrom = $this->returnArrivalTimeFrom;
        $query->returnArrivalTimeTo = $this->returnArrivalTimeTo;
        $query->numAdults = $this->numAdults;
        $query->numChildren = $this->numChildren;
        $query->numInfants = $this->numInfants;
        $query->priceFrom = $this->priceFrom;
        $query->priceTo = $this->priceTo;
        $query->onePerCity = $this->onePerCity;
        $query->returnFromDifferentAirport = $this->returnFromDifferentAirport;
        $query->returnFromDifferentCity = $this->returnFromDifferentCity;
        $query->groupBy = $this->groupBy;
        return $this->queryProcessor->getFlights($query);
    }
}
