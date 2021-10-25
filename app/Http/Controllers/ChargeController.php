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
        $car = Car::where('id', 1)->first();
        if (!$car) {
            return;
        }

        // 1000 watt needed to start charging at 5amps
        $watts_needed = 500;
        $amps = 5;

        if ($car->charging) {
            echo "car charging";
            if ($available_5min < 0) {
                echo " / no surplus";
                $charge = new Charge();
                $charge->time = time();
                $charge->action = "end";
                $charge->value = 0;
                $charge->save();
                $car->charging = false;
                $car->save();
                CarController::stopCharging($car);
            } else {
                echo " / still have enough juice";
            }
        } else {
            echo "car not charging";
            if ($available_5min > $watts_needed) {
                echo "start charging";
                $charge = new Charge();
                $charge->time = time();
                $charge->action = "start";
                $charge->value = $amps;
                $charge->save();
                $car->charging = true;
                $car->save();
                CarController::startCharging($car);
            } else {
                echo " / not enough juice";
            }
        }
    }
}
