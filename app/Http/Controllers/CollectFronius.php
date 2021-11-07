<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use App\Models\House;
use App\Models\Log;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CollectFronius extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __invoke(Request $request)
    {
        // need to use a static IP for fronius inverters
        $house = House::all()->first();

        if (!$house) {
            return;
        }

        if( $house->solar_ip == "") {
            return;
        }

        $response = Http::get('http://' . $house->solar_ip . '/solar_api/v1/GetPowerFlowRealtimeData.fcgi');
        $fronius = $response->json();

        // Get and format values
        $time = strtotime($fronius['Head']['Timestamp']); // unix timestamp
        // check for null production when invert sleeps overnight
        if (empty($fronius['Body']['Data']['Site']['P_PV'])) {
            $production = 0;
        } else {
            $production = round($fronius['Body']['Data']['Site']['P_PV']); // watts

        }
        // Don't allow negative production value
        if ($production < 0) {
            $production = 0;
        }
        $consumption = $production + round($fronius['Body']['Data']['Site']['P_Grid']); // watts
        $available = $production - $consumption; // watts

        $energy = new Energy;
        $energy->time = $time;
        $energy->production = $production;
        $energy->consumption = $consumption;
        $energy->available = $available;
        $energy->save();

        // Format
        /*
        {
           "Body" : {
              "Data" : {
                 "Inverters" : {
                    "1" : {
                       "DT" : 75,
                       "E_Day" : 33120,
                       "E_Total" : 2551900,
                       "E_Year" : 2551905.75,
                       "P" : 636
                    }
                 },
                 "Site" : {
                    "E_Day" : 33120,
                    "E_Total" : 2551900,
                    "E_Year" : 2551905.75,
                    "Meter_Location" : "grid",
                    "Mode" : "meter",
                    "P_Akku" : null,
                    "P_Grid" : 1509.3900000000001,
                    "P_Load" : -2145.3900000000003,
                    "P_PV" : 636,
                    "rel_Autonomy" : 29.644959657684609,
                    "rel_SelfConsumption" : 100
                 },
                 "Version" : "12"
              }
           },
           "Head" : {
              "RequestArguments" : {},
              "Status" : {
                 "Code" : 0,
                 "Reason" : "",
                 "UserMessage" : ""
              },
              "Timestamp" : "2021-11-07T18:50:16+13:00"
           }
    }*/


    }
}
