<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;

class CalculatorSceneA implements CalculatorContract
{
    /**
     * Calculate our rates.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @param int $distance
     *
     * @return \App\Contracts\Result
     */

    public function calculate(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        $fixingStartTime = clamp($start);
        $fixingEndTime = clamp($end);
        $totalMin = $fixingStartTime->diffInMinutes($fixingEndTime); // getting total min between start time and end time
        $hour = $totalMin / 60; //getting hour
        $perHourCost = 4 * 100; //per hour cost 4 pound
        $costByHour = $hour * $perHourCost; // total cost by time
        $perMileCost = 15;  // cost 15p per mile
        $costByDistance = $distance * $perMileCost;  // total cost by distance
        $totalCost = $costByHour + $costByDistance;  // adding total cost by adding cost by time and cost by distance


        return new Result($costByHour, new Distance($costByDistance));

    }

}
