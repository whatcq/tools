<?php

### commands data list
if (isset($_GET['redis_commands'])) {
	$data = file('lib/redis_commands.txt');
	foreach ($data as &$line) {
		$line = explode('|', rtrim($line));
	}
	die('var commands=' . json_encode($data));
}

### query
require 'lib/RedisClient.php';

$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : null;
if (isset($_REQUEST['q'])) {
	$client = new RedisClient('127.0.0.1', 6379);
	echo '<pre>';
	$res = $client->exec($q);
	if (is_array($res)) {
		print_r($res);
	} else {
		echo $res;
	}
	echo '</pre>';
	die;
}

### html page
$shows = [
	'info',
	'dbsize',
	'slowlog',
	'lastsave',
	'time',
	'client list',
	'monitor',
];
?>
<link href='//redis.io/images/favicon.png' rel='shortcut icon'>
<title>Redis Quick Query</title>
<style type="text/css">
a{display: inline-block; padding: 2px 5px;background: #c6dfff;border-radius: 3px;}
label{clear:left; width: 130px;display: inline-block;color: gray;font-style: italic;}
tr:nth-child(odd){background-color: #f2f2f2;}
tr:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
tr:hover,li:hover{background: #c3e9cb;}
pre{margin:0;}
i{font-size:60%;color:gray;}
#commands-container{max-height: 300px;overflow: auto;border: 1px solid #eee;}
#commands-container li{display: none;}
.command{color:blue;}
.args{color: green;}
.summary{float:right;color:gray;}
</style>
<script src="?redis_commands"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
function renderRedisCommands(){
	var container=$('#commands-container');
	for(var i in commands){
		container.append(`<li data-group='${commands[i][0]}' data-name='${commands[i][1]}'>
                <span class='command'>
                  ${commands[i][1]}
                  <span class='args'>${commands[i][2]}</span>
                </span>
                <span class='summary'>${commands[i][3]}</span>
            </li>`);
	}
	container.find('li').click(function(){
		var o=$(this)
		,cmd=o.attr('data-name')
		,args=o.find('.args').text();
		if (args){
			_args = prompt(cmd, args);
			if(!_args)return;
			cmd += ' '+_args;
		}
		$('#cmd').val(cmd);
		$.get('?', {q:cmd}, function(result){
			$('#result').html(result);
		})
	})
}
window.onload = function() {
    $('#q').focus();
    renderRedisCommands();
};
function filterGroup(group){
	$('#commands-container li').hide();
	$('#commands-container li[data-group='+group+']').show();
}
</script>
<select style="height:25px" onchange="filterGroup($(this).val())">
	<option value=''>All</option>
	<option value='cluster'>Cluster</option>
	<option value='connection'>Connection</option>
	<option value='geo'>Geo</option>
	<option value='hash'>Hashes</option>
	<option value='hyperloglog'>HyperLogLog</option>
	<option value='generic'>Keys</option>
	<option value='list'>Lists</option>
	<option value='pubsub'>Pub/Sub</option>
	<option value='scripting'>Scripting</option>
	<option value='server'>Server</option>
	<option value='set'>Sets</option>
	<option value='sorted_set'>Sorted Sets</option>
	<option value='stream'>Streams</option>
	<option value='string'>Strings</option>
	<option value='transactions'>Transactions</option>
</select>

<form style="display: inline-block;margin-bottom: 0;">
	<div style="position:relative;">
		<span style="margin-left:200px;width:18px;overflow:hidden;">
			<select style="width:218px;margin-left:-200px;height: 25px;"
			 onchange="$('q').value=this.value;$('q').focus();$('show').value=''">
			</select>
		</span>
		<input type="text" name="q" id="cmd" value="<?php echo $q; ?>"
			style="width:200px;position:absolute;left:2px;top:2px;height: 21px;border:0;" />
		<select name="show" id="show" style="height: 25px;" onchange="this.form.submit()">
			<option value="">-- show --</option>
<?php
foreach ($shows as $_show) {
	echo "<option value=\"$_show\"", ($show === $_show ? ' selected' : ''), "> $_show </option>\n";
}
?>
		</select>
		<input type="submit" value="Go" />
	</div>
</form>
<div id="commands-container"></div>
<div id="result"></div>
