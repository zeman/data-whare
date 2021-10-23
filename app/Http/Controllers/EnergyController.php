<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use Illuminate\Http\Request;

class EnergyController extends Controller
{
    public function latest()
    {
        $energies = Energy::where('time', '>', time()-60*60*3)->get();

        $data = [
            'production' => [],
            'consumption' => []
        ];

        foreach ($energies as $energy) {
            $data['production'][] = [$energy->time*1000, $energy->production];
            $data['consumption'][] = [$energy->time*1000, $energy->consumption];
        }

        return json_encode($data);
    }
}
