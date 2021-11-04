<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function __invoke()
    {
        // get available power for the last 5min
        $energy_json = (new EnergyController)->latest();
        $energy = json_decode($energy_json, true);
        $available_5min = $energy['available_5min'];
        $production_5min = $energy['production_5min'];
        $consumption_5min = $energy['consumption_5min'];

        // get the car's current charge state
        $car = Car::all()->first();
        if (!$car) {
            return;
        }

        // 1000 watt needed to start charging at 5amps
        $watts_needed_to_start = 1000;
        $watts_below_production = 250;
        $watts_percentage_buffer = 5;
        $watts_needed_to_stop = -1000;
        $amps_start = 5;
        $amps_lowest = 2;

        if ($car->charging) {
            echo "charging";
            if ($available_5min < $watts_needed_to_stop) {
                echo " / no surplus";
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
                }
            } else {
                echo " / still have enough juice";
                // work out how close to production we are
                // and what buffer we want before changing amps
                $watts_target = $production_5min - $watts_below_production;
                echo " / watt target is " . $watts_target;
                $watt_difference_from_target = $consumption_5min / $watts_target;
                echo " / watt difference from target is " . $watt_difference_from_target;

                if( $watt_difference_from_target < (1 - ($watts_percentage_buffer/100))) {
                    $amp_increase = 1;
                    // check if we're still a large percentage away
                    // double buffer and bump up amps
                    if ($watt_difference_from_target < 0.75) {
                        echo " / double amps as we're more than 75% from target";
                        $amp_increase = 2;
                    }
                    $change_amps_to = $car->amps + $amp_increase;
                    $amps = CarController::setAmps($car, $change_amps_to);
                    if ($amps) {
                        echo " / amps increased to " . $change_amps_to;
                        $charge = new Charge();
                        $charge->time = time();
                        $charge->action = "amps";
                        $charge->amps = $change_amps_to;
                        $charge->save();
                        $car->amps = $change_amps_to;
                        $car->save();
                    }
                } elseif ($watt_difference_from_target > (1 + ($watts_percentage_buffer/100))) {
                    if ($car->amps > $amps_lowest) {
                        $amp_decrease = 1;
                        // check if we're still a large percentage away
                        // double buffer and bump up amps
                        if ($watt_difference_from_target > (1 + (($watts_percentage_buffer/100) * 2))) {
                            $amp_decrease = 2;
                        }
                        $change_amps_to = $car->amps - $amp_decrease;
                        $amps = CarController::setAmps($car, $change_amps_to);
                        if ($amps) {
                            echo " / amps decreased to " . $change_amps_to;
                            $charge = new Charge();
                            $charge->time = time();
                            $charge->action = "amps";
                            $charge->amps = $change_amps_to;
                            $charge->save();
                            $car->amps = $change_amps_to;
                            $car->save();
                        }
                    } else {
                        echo " / already at lowest amps of " . $amps_lowest;
                        $charge = new Charge();
                        $charge->time = time();
                        $charge->action = "amps";
                        $charge->amps = $amps_lowest;
                        $charge->save();
                        $car->amps = $amps_lowest;
                        $car->save();
                    }
                } else {
                    $charge = new Charge();
                    $charge->time = time();
                    $charge->action = "amps";
                    $charge->amps = $car->amps;
                    $charge->save();
                    echo " / consumption is within the target range of "
                        . $watts_target
                        . " and buffer of "
                        . $watts_percentage_buffer . "%";
                }
            }
            // the car is charging so lets get the current battery status as well
            $status = CarController::getStatus($car);
        } else {
            echo "not charging";
            if ($available_5min > $watts_needed_to_start) {
                echo " / try start charging";
                $start = CarController::startCharging($car);
                if ($start) {
                     echo " / car started charging";
                    $charge = new Charge();
                    $charge->time = time();
                    $charge->action = "start";
                    $charge->amps = $amps_start;
                    $charge->save();
                    $car->charging = true;
                    $car->save();
                    $amps = CarController::setAmps($car, $amps_start);
                    if ($amps) {
                         echo " / amps set";
                        $car->amps = $amps_start;
                        $car->save();
                    } else {
                        echo " / failed to set amps";
                    }
                    CarController::getStatus($car);
                }
            } else {
                echo " / not enough spare juice, turn stuff off in the house!";
            }
        }
    }
}
