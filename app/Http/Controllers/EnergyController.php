<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use Illuminate\Http\Request;

class EnergyController extends Controller
{
    public function latest()
    {
        $energies = Energy::where('time', '>', time()-60*60*1)->get();

        $data = [
            'production_5min' => [],
            'production' => [],
            'consumption_5min' => [],
            'consumption' => [],
            'available_5min' => [],
            'available' => []
        ];

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

        $data['production_5min'] = round(array_sum($five['production']) / count($five['production']));
        $data['consumption_5min'] = round(array_sum($five['consumption']) / count($five['consumption']));
        $data['available_5min'] = round(array_sum($five['available']) / count($five['available']));

        foreach ($energies as $energy) {
            $data['production'][] = [$energy->time*1000, $energy->production];
            $data['consumption'][] = [$energy->time*1000, $energy->consumption];
            $data['available'][] = [$energy->time*1000, $energy->available];
        }

        return json_encode($data);
    }
}
