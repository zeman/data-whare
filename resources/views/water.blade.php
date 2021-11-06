<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DataWhare</title>
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    </head>
    <body>
    @include('nav', ['nav' => 'water'])
    <div class="object">
        <h2>Water</h2>
        <p>Coming soon.</p>
        <p>Monitor soil moisture using Davis Weatherlink and irrigate garden zones via Rainmachine.</p>
    </div>
    </body>
</html>
