<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;

class CalculatorSceneB implements CalculatorContract
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
        $totalDay = $fixingStartTime->diffInDays($fixingEndTime); // getting total day between start time and end time
        $hour = $totalMin / 60; // min to hour conversion
        if ($hour >= 24) {
            $restOfTheHour = $hour - $totalDay * 24; // getting rest of the hour by excluding complete days

            $dayCost = $totalDay * 85 * 100; // for a complete day , maximum cost will be 85 euro
            if ($restOfTheHour * 15 * 100 > 8500) { //if without a complete day, rest of the hour costs more than 85 euro, then
                $hourlyCost = 85 * 100; // hourly cost will be 85 euro
            } else {
                $hourlyCost = $restOfTheHour * 15 * 100; // otherwise, 15 euro per hour will be calculated
            }

            $totalCostByTime = $dayCost + $hourlyCost; // Adding total day cost and rest of the hour cost
        } else {
            if ($hour * 15 * 100 > 85 * 100) {
                $totalCostByTime = 85 * 100; // if hourly cost is more than 85 euro , then it will be maximum 85 euro
            } else {
                $totalCostByTime = $hour * 15 * 100; // hourly cost is 15 euro per hour
            }
        }

        $perKiloCost = 50; // per kilometer costs 50 cents
        if ($distance > 50) {
            $cuttingFreeDistance = $distance - 50;   // first 50 kilometer is free of cost, so getting other rest of the distance
            $costByDistance = $cuttingFreeDistance * $perKiloCost;  // calculating cost by distance (rest of the distance excluding first 50 km)
        } else {
            $costByDistance = 0; // first 50 kilometer is free of cost
        }

        $totalCost = $totalCostByTime + $costByDistance;
        return new Result($totalCostByTime, new Distance($costByDistance));
    }

}
