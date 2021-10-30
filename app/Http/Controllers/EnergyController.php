<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Energy;
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

        // get last hour of charging
        $charge_bands = [];
        $charges = Charge::where('time', '>', time()-60*60*$hours)->get();
        $started = false;
        $start = 0;
        foreach ($charges as $charge) {
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

        return json_encode($data);
    }
}
