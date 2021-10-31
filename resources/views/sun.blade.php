<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Data Whare</title>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    </head>
    <body>
    @include('nav')
    <div class="stats">
        <div class="object">
            <span class="desktop">Average production last 5min</span>
            <span class="mobile">Avg prod 5min</span>
            <div id="production_5min" class="stat" style="color:#ef8a62"></div>
            watts
        </div>
        <div class="object">
            <span class="desktop">Average consumption last 5min</span>
            <span class="mobile">Avg<br>use 5min</span>
            <div id="consumption_5min" class="stat" style="color:#67a9cf"></div>
            watts
        </div>
        <div class="object">
            <span class="desktop">Average surplus last 5min</span>
            <span class="mobile">Avg surplus 5min</span>
            <div id="available_5min" class="stat" style="color:#77539b"></div>
            watts
        </div>
    </div>
    <div class="object">
        <button class="btn hours" value="24">24</button>
        <button class="btn hours" value="12">12</button>
        <button class="btn hours" value="6">6</button>
        <button class="btn hours" value="3">3</button>
        <button class="btn hours btn-active" value="1">1</button>
        hours
        <div id="chart"></div>
    </div>
    <script>
        let hours = 1;
        let chart; // global
        let production_5min = document.getElementById('production_5min');
        let consumption_5min = document.getElementById('consumption_5min');
        let available_5min = document.getElementById('available_5min');

        function moveChart() {
            chart.xAxis[0].update({max:Date.now(),min:Date.now()-1000*60*60*hours});
        }
        setInterval(moveChart, 250);

        /**
         * Request data from the server, add it to the graph and set a timeout to request again
         */
        async function requestData() {
            const result = await fetch('/api/energy?hours=' + hours);
            if (result.ok) {
                const data = await result.json();
                chart.series[0].setData(data.production);
                chart.series[1].setData(data.consumption);
                chart.xAxis[0].update({plotBands:data.charges});
                production_5min.innerHTML = data.production_5min;
                consumption_5min.innerHTML = data.consumption_5min;
                available_5min.innerHTML = data.available_5min;
                setTimeout(requestData, 10000);
            }
        }
        Highcharts.setOptions({
            time: {
                useUTC: false
            }
        });
        window.addEventListener('load', function () {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'chart',
                    defaultSeriesType: 'spline',
                    backgroundColor: 'rgba(0,0,0,0)',
                    events: {
                        load: requestData
                    }
                },
                title: {
                    text: undefined
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    type: 'datetime',
                    tickWidth: 0,
                    lineWidth: 0,
                    labels: {
                        style: {
                            color: 'rgba(255,255,255,0.5)'
                        }
                    }
                },
                yAxis: {
                    minPadding: 0.2,
                    maxPadding: 0.2,
                    gridLineColor: 'rgba(255,255,255,0.1)',
                    title: {
                        text: undefined
                    },
                    labels: {
                        style: {
                            color: 'rgba(255,255,255,0.5)'
                        }
                    }
                },
                legend: {
                    itemStyle: {"color":"rgba(255,255,255,0.5)"}
                },
                plotOptions: {
                    column: {
                        borderWidth: 0,
                    },
                    spline: {
                        lineWidth: 4,
                    },
                    series: {
                        marker: {
                            enabled: false,
                            symbol: 'circle'
                        }
                    }
                },
                series: [{
                    name: 'Production',
                    data: [],
                    color: '#ef8a62'
                },{
                    name: 'Consumption',
                    data: [],
                    color: '#67a9cf'
                }]
            });
        });

        // buttons
        let hour_buttons = document.getElementsByClassName('hours');
        for (let hour_button of hour_buttons) {
            hour_button.addEventListener('click',hourClick);
        }
        function hourClick(e) {
            hours = e.target.value;
            requestData();
            for (let hour_button of hour_buttons) {
                hour_button.classList.remove('btn-active');
            }
            e.target.classList.add('btn-active');
        }
    </script>
    </body>
</html>
