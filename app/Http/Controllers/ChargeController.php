<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Charge;
use App\Models\House;
use App\Models\Log;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function __invoke()
    {
        // get available power for the last 5min
        $energy_json = (new EnergyController)->latest();
        $energy = json_decode($energy_json, true);
        //check we have data
        if(empty($energy['consumption_5min'])) {
            return;
        }
        $available_5min = $energy['available_5min'];
        $production_5min = $energy['production_5min'];
        $consumption_5min = $energy['consumption_5min'];

        // get the car's current charge state
        $car = Car::all()->first();
        if (!$car) {
            return;
        }

        // check we have a house to work with
        // if not create one with defaults
        $house = House::all()->first();
        if (!$house) {
            $house = new House();
            $house->watts_start = 1000;
            $house->watts_below = 500;
            $house->watts_buffer = 5;
            $house->watts_stop = -1000;
            $house->amps_min = 1;
            $house->amps_max = 32;
            $house->save();
        }
        $watts_needed_to_start = $house->watts_start;
        $watts_below_production = $house->watts_below;
        $watts_percentage_buffer = $house->watts_buffer;
        $watts_needed_to_stop = $house->watts_stop;
        $amps_start = round($watts_needed_to_start / 200); // 200 watts per amp at 240v, need to add 120v setting

        // check starting amps is above/below min/max amps
        if ($amps_start < $house->amps_min) {
            $amps_start = $house->amps_min;
        } elseif ($amps_start > $house->amps_max) {
            $amps_start = $house->amps_max;
        }

        $debug = "";
        $log_type = "charge";

        if ($car->charging) {
            $debug .= "charging";
            if ($available_5min < $watts_needed_to_stop) {
                $debug .= " / no surplus, try to stop charging";
                $stop =CarController::stopCharging($car);
                if ($stop) {
                    $charge = new Charge();
                    $charge->time = time();
                    $charge->action = "end";
                    $charge->amps = 0;
                    $charge->save();
                    $car->charging = false;
                    $car->amps = 0;
                    $car->save();
                    $debug .= " / charging stopped";
                }
            } else {
                $debug .= " / still have enough spare juice";
                // work out how close to production we are
                // and what buffer we want before changing amps
                $watts_target = $production_5min - $watts_below_production;
                $debug .= " / watt target is " . $watts_target;
                $watt_difference_from_target = $consumption_5min / $watts_target;
                $debug .= " / watt difference from target is " . round($watt_difference_from_target, 2);

                if( $watt_difference_from_target < (1 - ($watts_percentage_buffer/100))) {
                    $amp_increase = 1;
                    // check if we're still a large percentage away
                    // double buffer and bump up amps
                    if ($watt_difference_from_target < 0.75) {
                        $debug .= " / double amps as we're more than 75% from target";
                        $amp_increase = 2;
                    }
                    $change_amps_to = $car->amps + $amp_increase;
                    if ($change_amps_to > $house->amps_max) {
                        $change_amps_to = $house->amps_max;
                        $debug .= " / amps at max";
                    }
                    if ($change_amps_to !== $car->amps) {
                        $amps = CarController::setAmps($car, $change_amps_to);
                        if ($amps) {
                            $debug .= " / amps increased to " . $change_amps_to;
                            $car->amps = $change_amps_to;
                            $car->save();
                        }
                    }
                } elseif ($watt_difference_from_target > (1 + ($watts_percentage_buffer/100))) {
                    $amp_decrease = 1;
                    // check if we're still a large percentage away
                    // double buffer and bump up amps
                    if ($watt_difference_from_target > 1.25) {
                        $amp_decrease = 2;
                    }
                    $change_amps_to = $car->amps - $amp_decrease;
                    if ($change_amps_to < $house->amps_min) {
                        $change_amps_to = $house->amps_min;
                        $debug .= " / amps at min";
                    }
                    if ($change_amps_to !== $car->amps) {
                        $amps = CarController::setAmps($car, $change_amps_to);
                        if ($amps) {
                            $debug .= " / amps decreased to " . $change_amps_to;
                            $car->amps = $change_amps_to;
                            $car->save();
                        }
                    }
                } else {
                    $debug .= " / consumption is within the target range of "
                        . $watts_target
                        . " and buffer of "
                        . $watts_percentage_buffer . "%";
                }

                // always save the current amps so our charts show the history
                $charge = new Charge();
                $charge->time = time();
                $charge->action = "amps";
                $charge->amps = $car->amps;
                $charge->save();

            }
            // the car is charging so lets get the current battery status as well
            CarController::getStatus($car);
        } else {
            $debug .= "not charging";
            if ($available_5min > $watts_needed_to_start) {
                $debug .= " / try start charging";
                $start = CarController::startCharging($car);
                if ($start['status'] == 'success') {
                    $debug .= " / car started charging";
                    $charge = new Charge();
                    $charge->time = time();
                    $charge->action = "start";
                    $charge->amps = $amps_start;
                    $charge->save();
                    $car->charging = true;
                    $car->save();
                    $amps = CarController::setAmps($car, $amps_start);
                    if ($amps['status'] == 'success') {
                        $debug .= " / amps set";
                        $car->amps = $amps_start;
                        $car->save();
                    } else {
                        $debug .= " / failed to set amps " . $start['message'];
                        $log_type = "message";
                    }
                } else {
                    $debug .= " / couldn't charge / " . $start['message'];
                    $log_type = "message";
                }
            } elseif ($production_5min == 0) {
                $debug .= " / sleepy time, the sun's on the other side of the planet";
            } else {
                $debug .= " / not enough spare juice, turn stuff off in the house!";
            }
        }

        echo $debug;
        $log = new Log();
        $log->time = time();
        $log->type = $log_type;
        $log->log = $debug;
        $log->save();

    }
}
