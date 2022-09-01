<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Charge;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CarController extends Controller
{

    protected static string $teslafi = "https://www.teslafi.com/feed.php?token=";

    public static function startCharging(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token .
            '&command=charge_start&wake=50');
        $teslafi = $response->json();
        //print_r($teslafi);
        if (isset($teslafi['response']['result']) && $teslafi['response']['result'] == 1) {
            return ['status' => 'success', 'message' => ''];
        } else {
            $message = "";
            if (isset($teslafi['error'])) {
                $message = $teslafi['error'];
            } elseif (isset($teslafi['response']['reason'])) {
                $message = $teslafi['response']['reason'];
                if($message == "requested") {
                    $message = "start charge has already been requested, check if your charger is showing a red error.";
                } elseif ($message == "is_charging") {
                    $message = "car is a already charging manually, stop the charge so that DataWhare can automatically control the charging.";
                }
            } else{
                $message = "unknown error";
            }
            return ['status' => 'error', 'message' => $message];
        }
    }

    public static function stopCharging(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token .
            '&command=charge_stop&wake=50');
        $teslafi = $response->json();
        //print_r($teslafi);
        if ($teslafi['response']['result'] == 1) {
            //echo " / teslafi stopped charging";
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
        if (isset($teslafi['response']['result']) && $teslafi['response']['result'] == 1) {
            return ['status' => 'success', 'message' => ''];
        } else {
            return ['status' => 'error', 'message' => $teslafi['error']];
        }
    }

    public static function getStatus(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token);
        $teslafi = $response->json();
        //print_r($teslafi);
        if (isset($teslafi['usable_battery_level'])) {
            //echo " / usable battery is " . $teslafi['usable_battery_level'];
            $car->battery = $teslafi['usable_battery_level'];
        }
        if (isset($teslafi['charge_limit_soc'])) {
            //echo " / max battery is " . $teslafi['charge_limit_soc'];
            $car->battery_max = $teslafi['charge_limit_soc'];
        }
        if (isset($teslafi['time_to_full_charge'])) {
            //echo " / time to charge is " . $teslafi['time_to_full_charge'] . "h";
            $car->charge_time = $teslafi['time_to_full_charge'];
        }
        // check that we're still charging
        if ($car->charging) {
            if ($teslafi['charging_state'] == "Disconnected") {
                $charge = new Charge();
                $charge->time = time();
                $charge->action = "end";
                $charge->amps = 0;
                $charge->save();
                $car->charging = false;
                $car->amps = 0;
                $log = new Log();
                $log->time = time();
                $log->type = 'message';
                $log->log = 'Car is not connected to charger anymore.';
                $log->save();
            }
            if ($teslafi['charging_state'] == "Stopped") {
                $charge = new Charge();
                $charge->time = time();
                $charge->action = "end";
                $charge->amps = 0;
                $charge->save();
                $car->charging = false;
                $car->amps = 0;
                $log = new Log();
                $log->time = time();
                $log->type = 'message';
                $log->log = 'Car has stopped charging.';
                $log->save();
            }
        }
        $car->save();
        return false;
    }

}
