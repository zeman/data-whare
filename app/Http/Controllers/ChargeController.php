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

        // get the car's current charge state
        $car = Car::all()->first();
        if (!$car) {
            return;
        }

        // 1000 watt needed to start charging at 5amps
        $watts_needed = 1000;
        $amps = 5;

        if ($car->charging) {
            echo "charging";
            if ($available_5min < 0) {
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
                //todo: check if should increase amps
                if ($available_5min > $watts_needed) {
                    $change_amps_to = $car->amps + 1;
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
                } else {
                    if ($car->amps > 2) {
                        $change_amps_to = $car->amps - 1;
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
                        echo " / already at 2 amps";
                    }
                }
            }
        } else {
            echo "not charging";
            if ($available_5min > $watts_needed) {
                echo " / try start charging";
                $start = CarController::startCharging($car);
                if ($start) {
                     echo " / car started charging";
                    $charge = new Charge();
                    $charge->time = time();
                    $charge->action = "start";
                    $charge->amps = $amps;
                    $charge->save();
                    $car->charging = true;
                    $car->save();
                    $amps = CarController::setAmps($car, 5);
                    if ($amps) {
                        $car->amps = 5;
                        $car->save();
                    }
                }
            } else {
                echo " / not enough juice";
            }
        }
    }
}
