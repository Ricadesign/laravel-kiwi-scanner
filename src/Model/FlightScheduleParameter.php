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
        $this->flightDepartureTimeFrom = $flightDepartureTimeFrom ? new Carbon($flightDepartureTimeFrom): null;
        $this->flightDepartureTimeTo = $flightDepartureTimeTo ? new Carbon($flightDepartureTimeTo): null;
        $this->flightReturnTimeFrom = $flightReturnTimeFrom ? new Carbon($flightReturnTimeFrom): null;
        $this->flightReturnTimeTo = $flightReturnTimeTo ? new Carbon($flightReturnTimeTo) : null;
    }
}
