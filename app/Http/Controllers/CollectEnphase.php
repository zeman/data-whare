<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CollectEnphase extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Get Enphase data from the Envoy on the local network
        $response = Http::get('http://192.168.1.45/production.json');
        $enphase = $response->json();

        // Get and format values
        $time = $enphase['production'][1]['readingTime']; // unix timestamp
        $production = round($enphase['production'][1]['wNow']); // watts
        $consumption = round($enphase['consumption'][0]['wNow']); // watts
        $available = $production - $consumption; // watts

        // Don't allow negative production value
        if ($production < 0) {
            $production = 0;
        }

        $energy = new Energy;
        $energy->time = $time;
        $energy->production = $production;
        $energy->consumption = $consumption;
        $energy->available = $available;
        $energy->save();

        // Format
        /*
        Array
        (
            [production] => Array
            (
                [0] => Array
                (
                    [type] => inverters
                    [activeCount] => 15
                    [readingTime] => 1634947406
                    [wNow] => 946
                    [whLifetime] => 3518
                )
                [1] => Array
                (
                    [type] => eim
                    [activeCount] => 1
                    [measurementType] => production
                    [readingTime] => 1634947589
                    [wNow] => 1292.235
                    [whLifetime] => 3441.176
                    [varhLeadLifetime] => 0
                    [varhLagLifetime] => 4882.546
                    [vahLifetime] => 7613.886
                    [rmsCurrent] => 5.654
                    [rmsVoltage] => 236.001
                    [reactPwr] => 222.661
                    [apprntPwr] => 1338.249
                    [pwrFactor] => 0.97
                    [whToday] => 2844.176
                    [whLastSevenDays] => 3370.176
                    [vahToday] => 5323.886
                    [varhLeadToday] => 0
                    [varhLagToday] => 3048.546
                )

        )

    [consumption] => Array
    (
        [0] => Array
        (
            [type] => eim
            [activeCount] => 1
                    [measurementType] => total-consumption
                    [readingTime] => 1634947589
                    [wNow] => 5698.263
                    [whLifetime] => 73308.86
                    [varhLeadLifetime] => 18153.741
                    [varhLagLifetime] => 4882.736
                    [vahLifetime] => 75012.982
                    [rmsCurrent] => 24.667
                    [rmsVoltage] => 236.048
                    [reactPwr] => -1054.396
                    [apprntPwr] => 5822.639
                    [pwrFactor] => 0.98
                    [whToday] => 49255.86
                    [whLastSevenDays] => 73050.86
                    [vahToday] => 49884.982
                    [varhLeadToday] => 11050.741
                    [varhLagToday] => 3048.736
                )

            [1] => Array
    (
        [type] => eim
        [activeCount] => 1
                    [measurementType] => net-consumption
                    [readingTime] => 1634947589
                    [wNow] => 4406.029
                    [whLifetime] => 70146.998
                    [varhLeadLifetime] => 18153.741
                    [varhLagLifetime] => 0.19
                    [vahLifetime] => 75012.982
                    [rmsCurrent] => 19.013
                    [rmsVoltage] => 236.095
                    [reactPwr] => -831.735
                    [apprntPwr] => 4485.49
                    [pwrFactor] => 0.98
                    [whToday] => 0
                    [whLastSevenDays] => 0
                    [vahToday] => 0
                    [varhLeadToday] => 0
                    [varhLagToday] => 0
                )

        )

    [storage] => Array
    (
        [0] => Array
        (
            [type] => acb
            [activeCount] => 0
                    [readingTime] => 0
                    [wNow] => 0
                    [whNow] => 0
                    [state] => idle
                )

        )

    )*/


    }
}
