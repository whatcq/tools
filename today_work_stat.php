<?php
/*2016-12-07
# 保持编码和数据一致：gbk
modified: 2018/1/5
*/
header("Content-type:text/html;charset=gbk");
?>
<title>work统计</title>
<style type="text/css">
    html,body,canvas{
        margin:0;
        padding:0;
    }
    div{
        border-bottom: 1px solid #f4f4f4;
        padding: 2px 0;
        display: table;
        width: 700px;
    }
    span{
        padding: 1px 3px;
        display: table-cell;
        width: 100px;
    }
    .task{
        width: 200px;
    }
    .time{
        color: blue;
        width: 250px;
    }
    .min, .green{
        color: green;
    }
    .chao, .red{
        color: red;
    }
    .blue{
        color: blue;
    }
    .line-green, .line-red{
        display: <?= empty($_GET['detail'])?'none':$_GET['detail'];?>
    }
    .line-blue{
    }
</style>
<h3 name="top">today's works</h3>
<?php
$dir='D:\mysoft\fuer';
$stat = $statEstimate = [];
$date = isset($_GET['date'])?$_GET['date']:'';
$files = glob("$dir\\todayWorks_{$date}*.txt");
if(!$date){
  natcasesort($files);
}
foreach($files as $file) {
    $basename = basename($file);
    $month = str_replace(['todayWorks_', '.txt'], '', $basename);
    echo '<a name="m', $month, '" href="?date=', $month, '&detail=1">', $month, '</a><hr />';
    $d = file($file);
    $d[count($d)-1] .= '*=====*====';//最后一行没结束
    $timeOverStepTotal = 0;
    $timeEstimateTotal = 0;
    foreach($d as $l){
        $t = explode('*', $l);
        $timeEstimate = 0;
        if(count($t)==4){//任务有时没有带*20 8 5
            list($startTime, $task, $status, $endTime) = $t;
        }elseif(count($t)==5){
            list($startTime, $task, $timeEstimate, $status, $endTime) = $t;
        }else{
            //echo __FILE__.__LINE__.'<pre>';print_r($t);echo '</pre>';//exit;//
            continue;
        }
        //echo $et = ($endTime);
        //echo ' ';
        //echo $st = (explode(' ', $startTime.' 00:00:00')[1]);
        $timeEstimate = intval($timeEstimate);
        if($status[0]==='='){
            $timeSpent = gmstrftime("%H:%M", $timeEstimateTotal * 60);
            $timeOverStep = gmstrftime("%H:%M", $timeOverStepTotal * 60);
            $date = explode(' ',$startTime)[0];
            $statEstimate[$date] = number_format($timeEstimateTotal/60, 1);
            $stat[$date] = number_format($timeOverStepTotal/60, 1);
            $rate = round($timeEstimateTotal/max($timeOverStepTotal , 1), 1);
            $statRate[$date] = min($rate, 2);
            $timeOverStepTotal = 0;
            $timeEstimateTotal = 0;
            $chaoStyle = 'blue';
        }
        else {
            $timeEstimateTotal += $timeEstimate;
            $timeSpent = round((strtotime($endTime) - strtotime(explode(' ', $startTime.' 00:00:00')[1]))/60,0);
            $timeOverStep = $timeSpent - $timeEstimate;
            $timeOverStepTotal += $timeOverStep;
            $chaoStyle = $timeOverStep ? 'red' : 'green';
            $rate = '';
        }
        $statusClass = $status=='未完成' ? 'red' : 'green';
        echo<<<EOF
        <div class="line-$chaoStyle">
            <span class="time">$startTime</span>
            <span class="task">$task</span>
            <span class="$statusClass">$status</span>
            <span class="min">$timeEstimate</span>
            <span class="min2">$timeSpent</span>
            <span class="$chaoStyle">$timeOverStep</span>
            <span class="$chaoStyle">$rate</span>
        </div>
EOF;
    }
}
if(!$stat)die('没有数据！');
?>
<a name="chart">统计图</a>
<canvas id="myChart" width="800" height="300"></canvas>
<div id="dashboard" style="
    position: fixed;
    right: 10px;
    top: 10px;
    z-index: 20;
    vertical-align: text-top;
    margin-bottom: 2px;
    margin-bottom: -2px\9px;
    font-size: 12px;
    width: 200px;
    border: 1px solid #c6c6c6;
"></div>
<script src="http://cdn.bootcss.com/Chart.js/2.4.0/Chart.min.js"></script>
<script language="javascript">

var data = {
    labels: <?= json_encode(array_keys($stat))?>,
    datasets: [{
        label: '# 使用时间',
        data: <?= json_encode(array_values($statEstimate))?>,
        backgroundColor: '#6dce71',
        borderColor: '#68d26c',
        borderWidth: 1
    },{
        label: '# 延迟',
        data: <?= json_encode(array_values($stat))?>,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255,99,132,1)',
        borderWidth: 1
    },{
        type: 'line',
        label: '* 效用',
        data: <?= json_encode(array_values($statRate))?>,
        backgroundColor: 'rgba(49, 80, 224, 0.2)',
        borderColor: 'rgba(49, 80, 224, 0.5)',
        borderWidth: 1
    }]
},

options= {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero:true
            }
        }]
    }
}
;

// Any of the following formats may be used
var ctx = document.getElementById("myChart");
var myLineChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: options
});

var links = document.getElementsByTagName('a');
var anchors = [<?php if(isset($_GET['date']))echo "'<a href=\"?\">全部</a>', ";
?>'<a href="#top" style="margin-left: 100px;font-size: 20px;">&#8679;</a>'], n = links.length;
for(var i=0; i<n; i++){
    anchors.push('<li><a href="#'+links[i].name+'">'+links[i].innerText+'</a></li>');
}

document.getElementById('dashboard').innerHTML = anchors.join('');
</script>