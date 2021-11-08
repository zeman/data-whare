<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DataWhare</title>
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    </head>
    <body>
    @include('nav', ['nav' => 'settings'])
    @if ($data['status'] === "saved")
        <div class="form__saved">Settings saved</div>
    @endif
    <div class="object">
        <form class="form" method="post" action="/settings">
            @csrf
            <div class="form__group">
                <h2>Car</h2>
            </div>
            <div class="form__group">
                <label>Name</label>
                <input name="car_name" type="text" value="{{ $data['car_name'] }}">
            </div>
            <div class="form__group">
                <label>Teslafi API Token</label>
                <p>You can find your token on the <a href="https://teslafi.com/api.php" target="_blank">Teslafi API page.</a> Also make sure you have following commands enabled:<br>
                    Resume Polling, Wake Vehicle, Start charging, Stop charging, Set Charging Amps.</p>
                <input name="teslafi_api_token" type="text" value="{{ $data['teslafi_api_token'] }}">
            </div>
            <div class="form__group">
                <label>Amps Minimum</label>
                <p>Minimum charge rate in amps.</p>
                <input name="amp_min" type="text" value="{{ $data['amp_min'] }}">
            </div>
            <div class="form__group">
                <label>Amps Maximum</label>
                <p>Maximum charge rate in amps. Adjust this to match your wall charger's maximum amps.</p>
                <input name="amp_max" type="text" value="{{ $data['amp_max'] }}">
            </div>
            <div class="form__group">
                <h2>Solar System</h2>
            </div>
            <div class="form__group">
                <label>Inverter</label>
                <select name="solar_type">
                    <option value="enphase" @if ($data['solar_type'] == 'enphase') selected @endif>Enphase</option>
                    <option value="fronius" @if ($data['solar_type'] == 'fronius') selected @endif>Fronius</option>
                </select>
            </div>
            <div class="form__group">
                <label>Inverter IP address</label>
                <p>You'll want to allocate your inverter a static IP via your internet router so it doesn't change on reboot.<br>
                Static IP is optional for Enphase Envoy.</p>
                <input name="solar_ip" type="text" value="{{ $data['solar_ip'] }}">
            </div>
            <div class="form__group">
                <h2>Solar Usage Behaviour</h2>
            </div>
            <div class="form__group">
                <label>Watts start</label>
                <p>How many spare solar watts needed before charging starts.</p>
                <input name="watts_start" type="text" value="{{ $data['watts_start'] }}">
            </div>
            <div class="form__group">
                <label>Watts below</label>
                <p>While charging try and keep overall consumption this many watts below solar production.</p>
                <input name="watts_below" type="text" value="{{ $data['watts_below'] }}">
            </div>
            <div class="form__group">
                <label>Watts buffer</label>
                <p>While charging don't adjust the charge rate (amps) if within this percentage of the target watts.</p>
                <input name="watts_buffer" type="text" value="{{ $data['watts_buffer'] }}" style="width:40px"> %
            </div>
            <div class="form__group">
                <label>Watts stop</label>
                <p>Stop charging when the average spare watts passes.</p>
                <input name="watts_stop" type="text" value="{{ $data['watts_stop'] }}">
            </div>
            <button type="submit">SAVE</button>
        </form>
    </div>
    </body>
</html>
