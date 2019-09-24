<?php
/**
 * web监控php-fpm status数据
 * @author Cqiu
 * @date 2018-1-5
 */

if (isset($_REQUEST['data'])) {
    $url = 'http://syjp.txzkeji.com/status';

    $r = [
        'listen_queue'     => 0,
        //'max_listen_queue' => 0,
        //'listen_queue_len' => 0,
        'idle_processes'   => 0,
        'active_processes' => 0,
        //'total_processes' => 0,
        //'max_active_processes' => 0,
        //'max_children_reached' => 0
    ];
    $result = json_decode(file_get_contents($url . '?json'), 1);
    foreach ($result as $k => $v) {
        $k = strtr($k, ' ', '_');
        isset($r[$k]) && $r[$k] = $v;
    }

    echo json_encode($r);
    exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <link rel="icon" href="https://static.jianshukeji.com/highcharts/images/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* css 代码  */
    </style>
    <!-- <script src="https://img.hcharts.cn/jquery/jquery-1.8.3.min.js"></script> -->
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://img.hcharts.cn/highstock/highstock.js"></script>
    <script src="https://img.hcharts.cn/highcharts/modules/exporting.js"></script>
    <script src="https://img.hcharts.cn/highcharts-plugins/highcharts-zh_CN.js"></script>
</head>
<body>
<div id="container" style="min-width:400px;height:400px"></div>
<input type="button" id="on_off" value="start"/>
<script>
    var dataQueue = [];
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    // 填充初始值，才能有宽度
    var fillData = []
        , time = (new Date()).getTime()
        , i;
    for (i = -60; i <= 0; i += 1) {
        fillData.push([
            time + i * 1000,
            0
        ]);
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
                        if (!result) return;
                        var k = 0;
                        var time = (new Date()).getTime();
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
                        activeLastPointToolip(chart);
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

    var timer = null;

    $('#on_off').click(function () {
        var o = $(this);
        if (o.val() == 'stop') {
            o.val('start');
            clearInterval(timer);
        } else {
            o.val('stop');
            timer = setInterval(function () {
                $.getJSON('?data=' + Math.random(), function (result) {
                    dataQueue.push(result);
                });
            }, 1000);
        }
    });
</script>
</body>
</html>
