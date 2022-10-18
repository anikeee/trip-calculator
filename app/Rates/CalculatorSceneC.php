<?php

namespace App\Rates;

use App\Contracts\Calculator as CalculatorContract;
use App\Contracts\Result as ResultContract;
use Carbon\Carbon;

class CalculatorSceneC implements CalculatorContract
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

        $count = 0; // this variable is used to count hours , after 24 hours , count variable will be assigned to a new value zero
        $day = 1; // this variable is used to count day because I have calculated cost of each day individually in totalCost array

        $fixingStartTime = clamp($start);
        $fixingEndTime = clamp($end);

        $CostByTimeInWeekend = [];  // variable used for adding cost in weekend
        $costWithin7amTo7pm = []; // variable used for adding cost within 7am-7pm in workdays
        $costOutside7amTo7pm = []; // variable used for adding cost outside 7am-7pm in workdays
        $costWithin9pmTo6am = []; // variable used for adding cost within 9pm-6am in workdays

        $totalCostByTimeIndividualDay = []; // total cost by time each day will be stored in this array where key is 'day number'

        //a for-loop is used to iterate over time hourly STARTS
        for ($date = $fixingStartTime->copy(); $date->lte($fixingEndTime); $date->addHour()) {
            if ($date == $fixingEndTime) {
                break; // making sure program is out of loop if date is equal to end date
            }

            //initializing each day cost by time variable to zero in every array required to calculate cost by time STARTS
            if ($day == 1) {
                $CostByTimeInWeekend[$day] = $CostByTimeInWeekend[$day] ?? 0;  //if value is not set yet then making it to zero
                $costWithin7amTo7pm[$day] = $costWithin7amTo7pm[$day] ?? 0; //if value is not set yet then making it to zero
                $costOutside7amTo7pm[$day] = $costOutside7amTo7pm[$day] ?? 0; //if value is not set yet then making it to zero
                $costWithin9pmTo6am[$day] = $costWithin9pmTo6am[$day] ?? 0; //if value is not set yet then making it to zero
                $totalCostByTimeIndividualDay[$day] = $totalCostByTimeIndividualDay[$day] ?? 0; //if value is not set yet then making it to zero
            }
            //initializing each day cost by time variable to zero in every array required to calculate cost by time ENDS

            if ($totalCostByTimeIndividualDay[$day] <= 3900) { // 3900p max per day
                if ($date->isWeekend()) {
                    $CostByTimeInWeekend[$day] = $CostByTimeInWeekend[$day] + 200;  //200p per hour in weekends
                    $count++; //counting hour
                } elseif ($date->format('H') >= 7 && $date->format('H') < 19) {
                    $costWithin7amTo7pm[$day] = $costWithin7amTo7pm[$day] + 665; //665p per hour in between 7am-7pm
                    $count++; //counting hour
                } elseif ($date->format('H') >= 21 && $date->format('H') < 6) {
                    if ($costWithin9pmTo6am[$day] <= 1200) { // 1200p max between 9pm-6am in workdays
                        $costWithin9pmTo6am[$day] = $costWithin9pmTo6am[$day] + 400; //400p per hour outside the time of 7am-7pm
                        $count++; //counting hour
                    } else {
                        $costWithin9pmTo6am[$day] = 1200; // 1200p max between 9pm-6am in workdays
                        $count++; //counting hour
                    }
                } else {
                    $costOutside7amTo7pm[$day] = $costOutside7amTo7pm[$day] + 400;
                    $count++; //counting hour
                }
                if ($CostByTimeInWeekend[$day] + $costWithin7amTo7pm[$day] + $costOutside7amTo7pm[$day] + $costWithin9pmTo6am[$day] > 3900) {
                    $totalCostByTimeIndividualDay[$day] = 3900;
                } else {
                    //adding all cost by time from every category
                    $totalCostByTimeIndividualDay[$day] = $CostByTimeInWeekend[$day] + $costWithin7amTo7pm[$day] + $costOutside7amTo7pm[$day] + $costWithin9pmTo6am[$day];

                }
            } else {
                $totalCostByTimeIndividualDay[$day] = 3900; // per day cost max 3900p
                $fixingStartTime->addDay(1); // going to next day for calculation
                $day++; //next day after maximum value is already 3900p
                $CostByTimeInWeekend[$day] = $CostByTimeInWeekend[$day] ?? 0;  //if value is not set yet then making it to zero
                $costWithin7amTo7pm[$day] = $costWithin7amTo7pm[$day] ?? 0; //if value is not set yet then making it to zero
                $costOutside7amTo7pm[$day] = $costOutside7amTo7pm[$day] ?? 0; //if value is not set yet then making it to zero
                $costWithin9pmTo6am[$day] = $costWithin9pmTo6am[$day] ?? 0; //if value is not set yet then making it to zero
                $totalCostByTimeIndividualDay[$day] = $totalCostByTimeIndividualDay[$day] ?? 0; //if value is not set yet then making it to zero
            }

            if ($count == 24) {
                $day++; // day value incremented for next day data calculation
                $CostByTimeInWeekend[$day] = 0; // initializing next key (day)
                $costWithin7amTo7pm[$day] = 0; // initializing next key (day)
                $costOutside7amTo7pm[$day] = 0; // initializing next key (day)
                $costWithin9pmTo6am[$day] = 0; // initializing next key (day)
                $totalCostByTimeIndividualDay[$day] = 0; // initializing next key (day)

                $count = 0; // After a day, counting hours will be starting from zero again
            }

        }
        //a for-loop is used to iterate over time hourly ENDS

        $CostByTime = 0; // variable of total cost by time each day included
        foreach ($totalCostByTimeIndividualDay as $key => $cost) {
            $CostByTime += $cost; // adding each day cost over time
        }
        if ($distance > 100) {
            $cuttingLowPriceDistance = $distance - 100; // getting rest of the distance by substracting first 100 mile
            $costByDistance = 100 + $cuttingLowPriceDistance * 20; // first 100 mile will cost 100p then rest is 20p
        } else {
            $costByDistance = $distance * 1; // first 100 mile will cost 1p per mile
        }
        $totalCost = $CostByTime + $costByDistance; //if total cost by time and distance is required

        return new Result($CostByTime, new Distance($costByDistance));
    }

}
