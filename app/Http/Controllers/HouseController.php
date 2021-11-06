<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\House;
use Illuminate\Http\Request;

class HouseController extends Controller
{

    public static function index()
    {
        // see if we have a house
        $house = House::all()->first();
        if (!$house) {
            $house = new House();
            $house->watts_start = 1000;
            $house->watts_below = 500;
            $house->watts_buffer = 5;
            $house->watts_stop = -1000;
            $house->save();
        }
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
            "teslafi_api_token" => $teslafi_api_token,
            "watts_start" => $house->watts_start,
            "watts_below" => $house->watts_below,
            "watts_buffer" => $house->watts_buffer,
            "watts_stop" => $house->watts_stop
        ];
        return view('settings', ["data"=>$data]);

    }

    public static function store()
    {
        $car_name = request('car_name', '');
        $teslafi_api_token = request('teslafi_api_token', '');

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
            $car->save();
        }

        // update house
        $house = House::all()->first();

        if ($house) {
            $house->watts_start = request('watts_start');
            $house->watts_below = request('watts_below');
            $house->watts_buffer = request('watts_buffer');
            $house->watts_stop = request('watts_stop');
            $house->save();
        }

        $data = [
            "status" => "saved",
            "car_name"=> $car->name,
            "teslafi_api_token" => $car->teslafi_api_token,
            "watts_start" => $house->watts_start,
            "watts_below" => $house->watts_below,
            "watts_buffer" => $house->watts_buffer,
            "watts_stop" => $house->watts_stop
        ];

        return view('settings', ["data"=>$data]);

    }
}
