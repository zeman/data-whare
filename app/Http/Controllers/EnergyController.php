<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Charge;
use App\Models\Energy;
use App\Models\Log;
use Illuminate\Http\Request;

class EnergyController extends Controller
{
    public function latest()
    {
        // setup data structure
        $data = [
            'production_5min' => [],
            'production' => [],
            'consumption_5min' => [],
            'consumption' => [],
            'available_5min' => [],
            'available' => [],
            'charges' => [],
            'amps' => [],
            'battery' => "--",
            'battery_max' => "--",
            'charge_time' => "--",
            'log' => "",
            'log_type' => "",
            'message' => ""
        ];

        $hours = request()->query('hours', 1);

        // get last hour of energy data
        $energies = Energy::where('time', '>', time()-60*60*$hours)->get();
        foreach ($energies as $energy) {
            $data['production'][] = [$energy->time*1000, $energy->production];
            $data['consumption'][] = [$energy->time*1000, $energy->consumption];
            $data['available'][] = [$energy->time*1000, $energy->available];
        }

        // create last 5min averages
        $five_ago = time()-60*5;
        $five = [
            'production' => [],
            'consumption' => [],
            'available' => []
        ];
        foreach ($energies as $energy) {
            if ($energy->time > $five_ago) {
                $five['production'][] = $energy->production;
                $five['consumption'][] = $energy->consumption;
                $five['available'][] = $energy->available;
            }
        }
        if (count($five['production'])) {
            $data['production_5min'] = round(array_sum($five['production']) / count($five['production']));
            $data['consumption_5min'] = round(array_sum($five['consumption']) / count($five['consumption']));
            $data['available_5min'] = round(array_sum($five['available']) / count($five['available']));
        }

        // get last hour of charging and amps
        $charge_bands = [];
        $charges = Charge::where('time', '>', time()-60*60*$hours)->get();
        $started = false;
        $start = 0;
        foreach ($charges as $charge) {
            // work out charging plot bands
            if( $charge->action == 'start') {
                $start = $charge->time*1000;
                $started = true;
            }
            if ($charge->action == 'end') {
                if ($started = true) {
                    $charge_bands[] = [
                        'from' => $start,
                        'to' => $charge->time*1000,
                        'color' => 'rgba(255,255,255,0.1)',
                        'label' => [
                            'text' => 'charging',
                            'align' => 'left',
                            'x' => 5,
                            'style' => [
                                'color' => 'rgba(255,255,255,0.5)'
                            ]
                        ]
                    ];
                    $started = false;
                }
            }
            // add amps
            $data['amps'][] = [$charge->time*1000, $charge->amps];
        }
        // if there's no end then we're still charging
        if( $started ) {
            $charge_bands[] = [
                'from' => $start,
                'to' => time()*1000,
                'color' => 'rgba(255,255,255,0.1)',
                'label' => [
                    'text' => 'charging',
                    'align' => 'left',
                    'x' => 5,
                    'style' => [
                        'color' => 'rgba(255,255,255,0.5)'
                    ]
                ]
            ];
        }
        $data['charges'] = $charge_bands;

        // get car battery state
        $car = Car::all()->first();
        if ($car) {
            if ($car->charging) {
                $data['battery'] = $car->battery;
                $data['battery_max'] = $car->battery_max;
                $data['charge_time'] = $car->charge_time;
            }
        } else {
            $data['message'] = "Hi there! To get started add the Teslafi API token for your car in the <a href='/settings'>Settings.</a>";
        }

        // get latest log line
        $log = Log::orderBy('id', 'desc')->first();
        if ($log) {
            $data['log'] = date("H:i", $log->time) . " - " . $log->log;
            $data['log_type'] = $log->type;
        }

        return json_encode($data);
    }
}
