<?php
/**
 * web监控php-fpm status数据
 * @author Cqiu
 * @date 2019-9-25
 */

if (isset($_REQUEST['data'])) {
    date_default_timezone_set("Asia/Chongqing");
    header("Content-Type: text/event-stream\n\n");

    $url = 'http://xxxx.com/status';

    $r = [
        'listen_queue' => 0,
        //'max_listen_queue' => 0,
        //'listen_queue_len' => 0,
        'idle_processes' => 0,
        'active_processes' => 0,
        //'total_processes' => 0,
        //'max_active_processes' => 0,
        //'max_children_reached' => 0
    ];

    // 注意：相对ajax方案，这个是单线程阻塞的！时间准确性还没有对上，目前有延迟问题。
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 2,
        ],
    ]);

    while (1) {
        $result = json_decode(file_get_contents($url . '?json', 0, $ctx), 1);
        foreach ($result as $k => $v) {
            $k = strtr($k, ' ', '_');
            isset($r[$k]) && $r[$k] = $v;
        }
        echo 'data: ', json_encode($r), "\n\n";

        ob_flush();
        flush();
        sleep(1);
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="https://tool.lu/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.bootcss.com/highstock/6.0.3/highstock.js"></script>
</head>
<body>
<div id="container" style="min-width:400px;height:400px"></div>
<script>
    var dataQueue = [];
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    // 填充初始值，才能有宽度
    var fillData = [], time = (new Date()).getTime();
    for (var i = -60; i <= 0; i += 1) {
        fillData.push([time + i * 1000, 0]);
    }

    function activeLastPointToolip(chart) {
        var points = chart.series[0].points;
        chart.tooltip.refresh(points[points.length - 1]);
    }

    var chart = Highcharts.chart('container', {
        chart: {
            type: 'spline',
            marginRight: 10,
            events: {
                load: function () {
                    var chart = this, series = this.series;
                    activeLastPointToolip(chart);

                    setInterval(function () {
                        var result = dataQueue.shift();
                        var time = (new Date()).getTime();
                        //如果没有数据，时间线会拉得很长，点不可看
                        if (!result) {
                            for (var i in series) {
                                series[i].addPoint([time, 0], true, true);
                            }
                            return;
                        }
                        var k = 0;
                        for (var i in result) {
                            if (i !== 'start_time') {
                                //console.log(i, result[i] * 1);
                                series[k].addPoint([
                                    time,
                                    result[i] * 1
                                ], true, true);//, true
                                k++;
                            }
                        }
                        //activeLastPointToolip(chart);
                    }, 1000);
                    /*
                    var series = this.series[0],
                        chart = this;
                    activeLastPointToolip(chart);
                    setInterval(function () {
                        var x = (new Date()).getTime(), // 当前时间
                            y = Math.random();          // 随机值
                        series.addPoint([x, y], true, true);
                        activeLastPointToolip(chart);
                    }, 1000);
                    */
                }
            }
        },
        title: {
            text: 'php-fpm status实时数据'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150
        },
        yAxis: {
            title: {
                text: null
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                    Highcharts.numberFormat(this.y, 2);
            }
        },
        series: [{
            "name": "listen_queue",
            "data": fillData
        }, {
            "name": "idle_processes",
            "data": fillData
        }, {
            "name": "active_processes",
            "data": fillData
        }]
    });

    var evtSource = new EventSource('?data');
    evtSource.onmessage = function (e) {
        dataQueue.push(JSON.parse(e.data));
    }
</script>
</body>
</html>
