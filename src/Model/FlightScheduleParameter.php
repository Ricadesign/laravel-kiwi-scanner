<?php

namespace Ricadesign\LaravelKiwiScanner\Model;

use Carbon\Carbon;

/**
 * Parameters for Query
 */
class FlightScheduleParameter
{
    public Carbon $flightDepartureTimeFrom;
    public Carbon $flightDepartureTimeTo;
    public Carbon $flightReturnTimeFrom;
    public Carbon $flightReturnTimeTo;
    public function __construct(string $flightDepartureTimeFrom = null, string $flightDepartureTimeTo = null, string $flightReturnTimeFrom = null, string $flightReturnTimeTo = null) {
        $this->flightDepartureTimeFrom =new Carbon($flightDepartureTimeFrom);
        $this->flightDepartureTimeTo =new Carbon($flightDepartureTimeTo);
        $this->flightReturnTimeFrom =new Carbon($flightReturnTimeFrom);
        $this->flightReturnTimeTo =new Carbon($flightReturnTimeTo);
    }
}
