<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CarController extends Controller
{

    protected static string $teslafi = "https://www.teslafi.com/feed.php?token=";

    public static function startCharging(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token .
            '&command=charge_start&wake=30');
        $teslafi = $response->json();
        //print_r($teslafi);
        if ($teslafi['response']['result'] == 1) {
            echo " / teslafi started charging";
            return true;
        } else {
            // error
            echo " / teslafi failed to start charging";
            echo " / " . $teslafi['error'];
            //print_r($teslafi);
        }
        return false;
    }

    public static function stopCharging(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token .
            '&command=charge_stop&wake=30');
        $teslafi = $response->json();
        //print_r($teslafi);
        if ($teslafi['response']['result'] == 1) {
            echo " / teslafi stopped charging";
            return true;
        }
        return false;
    }

    public static function setAmps(Car $car, $amps)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token .
            '&command=set_charging_amps&charging_amps=' . $amps . '&wake=30');
        $teslafi = $response->json();
        //print_r($teslafi);
        if ($teslafi['response']['result'] == 1) {
            echo " / teslafi amps set";
            return true;
        } else {
            echo " / failed to set the amps";
            echo " / " . $teslafi['error'];
        }
        return false;
    }

    public static function getStatus(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token);
        $teslafi = $response->json();
        //print_r($teslafi);
        if (isset($teslafi['usable_battery_level'])) {
            echo " / usable battery is " . $teslafi['usable_battery_level'];
            $car->battery = $teslafi['usable_battery_level'];
        }
        if (isset($teslafi['charge_limit_soc'])) {
            echo " / max battery is " . $teslafi['charge_limit_soc'];
            $car->battery_max = $teslafi['charge_limit_soc'];
        }
        if (isset($teslafi['time_to_full_charge'])) {
            echo " / time to charge is " . $teslafi['time_to_full_charge'] . "h";
            $car->charge_time = $teslafi['time_to_full_charge'];
        }
        $car->save();
        return false;
    }

}
