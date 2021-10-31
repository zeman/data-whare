<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class HouseController extends Controller
{

    public static function index()
    {
        // see if we have a car to work with
        $car =Car::all()->first();
        $car_name = "";
        $teslafi_api_token = "";
        if ($car) {
            $car_name = $car->name;
            $teslafi_api_token = $car->teslafi_api_token;
        }
        $data = [
            "status" => "view",
            "car_name"=> $car_name,
            "teslafi_api_token" => $teslafi_api_token

        ];
        return view('settings', ["data"=>$data]);

    }

    public static function store()
    {
        $car_name = request('car_name');
        $teslafi_api_token = request('teslafi_api_token');

        // see if we have a car to work with
        $car =Car::all()->first();

        if ($car) {
            if ($car_name) {
                $car->name = $car_name;
            }
            if ($teslafi_api_token) {
                $car->teslafi_api_token = $teslafi_api_token;
            }
            $car->save();
        } else {
            $car = new Car();
            if ($car_name) {
                $car->name = $car_name;
            }
            if ($teslafi_api_token) {
                $car->teslafi_api_token = $teslafi_api_token;
            }
            $car->charging = false;
            $car->amps = 0;
            $car->soc = 0;
            $car->save();
        }

        $data = [
            "status" => "saved",
            "car_name"=> $car->name,
            "teslafi_api_token" => $car->teslafi_api_token
        ];

        return view('settings', ["data"=>$data]);

    }
}
