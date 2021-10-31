<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Data Whare</title>
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    </head>
    <body>
    @include('nav')

        @if ($data['status'] === "saved")
            <div class="form__saved">Settings saved</div>
        @endif
        <div class="object">
            <form class="form" method="post" action="/settings">
                @csrf
                <h2>Car</h2>
                <div class="form__group">
                    <label>Name</label>
                    <input name="car_name" type="text" value="{{ $data['car_name'] }}">
                </div>
                <div class="form__group">
                    <label>Teslafi API Token</label>
                    <input name="teslafi_api_token" type="text" value="{{ $data['teslafi_api_token'] }}">
                </div>
                <button type="submit">SAVE</button>
            </form>
        </div>
    </body>
</html>
