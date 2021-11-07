<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CollectSolar extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $house = House::all()->first();
        if ($house) {
            if ($house->solar_type == 'enphase') {
                $collect = new CollectEnphase();
                $collect->__invoke($request);
            } elseif ($house->solar_type == 'fronius') {
                $collect = new CollectFronius();
                $collect->__invoke($request);
            }
        }
    }
}
