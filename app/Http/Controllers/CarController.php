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
        $response = Http::get(self::$teslafi . $car->teslafi_api_token . '&command=charge_start&wake=30');
        $teslafi = $response->json();
        //print_r($teslafi);
        if ($teslafi['response']['result'] == 1) {
            //echo "started charging";
            return true;
        }
        return false;
    }

    public static function stopCharging(Car $car)
    {
        $response = Http::get(self::$teslafi . $car->teslafi_api_token . '&command=charge_stop&wake=30');
        $teslafi = $response->json();
        //print_r($teslafi);
        if ($teslafi['response']['result'] == 1) {
            //echo "stopped charging";
            return true;
        }
        return false;
    }
}
