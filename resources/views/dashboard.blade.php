<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Data Whare</title>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <style>
            body {
                background-color: #2e3136;
            }
            #chart {
                height: 400px;
                width: 100%;
            }
        </style>
    </head>
    <body>
    <div id="chart"></div>
    <script>
        let chart; // global
        /**
         * Request data from the server, add it to the graph and set a timeout to request again
         */
        async function requestData() {
            const result = await fetch('http://localhost/api/energy');
            if (result.ok) {
                const data = await result.json();
                chart.series[0].setData(data.production);
                chart.series[1].setData(data.consumption);
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
                    tickPixelInterval: 150,
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
    </script>
    </body>
</html>
