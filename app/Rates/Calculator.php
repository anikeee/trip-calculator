<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;

class Calculator implements CalculatorContract
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
    public function calculateSceneA(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        $fixingStartTime = clamp($start);
        $fixingEndTime = clamp($end);
        $totalMin = $fixingStartTime->diffInMinutes($fixingEndTime);
        $hour = $totalMin / 60;
        $perHourCost = 4 * 100;
        $costByHour = $hour * $perHourCost;
        $perMileCost = 15;
        $costByDistance = $distance * $perMileCost;
        $totalCost = $costByHour + $costByDistance;


        return new Result($totalCostByTime, new Distance($costByDistance));

//        return new Result(0, new Distance(0));
    }
    public function calculate(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        return new Result(0, new Distance(0));
    }
    public function calculateSceneB(Carbon $start, Carbon $end, int $distance): ResultContract
    {
        //sceneB
        $fixingStartTime = clamp($start);
        $fixingEndTime = clamp($end);
        $totalMin = $fixingStartTime->diffInMinutes($fixingEndTime);
        $totalDay = $fixingStartTime->diffInDays($fixingEndTime);
        $hour = $totalMin / 60;
        if ($hour >= 24) {
            $restOfTheHour = $hour - $totalDay * 24;

            $dayCost = $totalDay * 85 * 100;
            if ($restOfTheHour * 15 * 100 > 8500) {
                $hourlyCost = 85 * 100;
            } else {
                $hourlyCost = $restOfTheHour * 15 * 100;
            }

            $totalCostByTime = $dayCost + $hourlyCost;
        } else {
            if ($hour * 15 * 100 > 85 * 100) {
                $totalCostByTime = 85 * 100;
            } else {
                $totalCostByTime = $hour * 15 * 100;
            }
        }

        $perKiloCost = 50;
        if ($distance > 50) {
            $cuttingFreeDistance = $distance - 50;
            $costByDistance = $cuttingFreeDistance * $perKiloCost;
        } else {
            $costByDistance = 0;
        }

        $totalCost = $totalCostByTime + $costByDistance;
        return new Result($totalCostByTime, new Distance($costByDistance));

    }
}
