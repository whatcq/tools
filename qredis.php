<?php
/**
 * Simple redis query tool
 * @author Cqiu 2020-6-5
 */

### commands data list
if (isset($_GET['redis_commands'])) {
	$data = file('lib/redis_commands.txt');
	foreach ($data as &$line) {
		$line = explode('|', rtrim($line));
	}
	die('var commands=' . json_encode($data));
}

### query
$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
if (isset($_REQUEST['q'])) {
	require 'lib/RedisClient.php';

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
	'slowlog get',
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
#result{min-height: 20px;max-height: 200px;padding: 10px; background: #f1f0f0; border-radius: 5px;overflow: auto;}
#commands-container{max-height: 300px;overflow: auto;border: 1px solid #eee;}
#commands-container li{display: none;color:#a3a3a3;}
.group{background: #597684; border-radius: 5px; font-size: 12px; color: #d5d5d5; padding: 0 3px; }
.command{color:blue;}
.isWrite{color:red;}
.args{color: green;font-size: 80%}
.summary{float:right;color:gray;min-width: 600px;display: inline-block;}
</style>
<script src="?redis_commands"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
function renderRedisCommands(){
	var container=$('#commands-container'), isWrite;
	for(var i in commands){
		isWrite = /(add|pop|push|set|move|incr)/.test(commands[i][1]) ? 'isWrite':'';
		container.append(`<li data-group='${commands[i][0]}' data-name='${commands[i][1]}'>
				<span class='group'>${commands[i][0]}</span>
                <span class='command ${isWrite}'>
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
		query(cmd);
	})
}
function query(cmd){
	$.get('?', {q:cmd}, function(result){
		$('#result').html(result);
	});
	return false;
}
window.onload = function() {
    $('#q').focus();
    renderRedisCommands();
	$('#form').submit(function(e){
		// e.preventDefault();
    	query($('#cmd').val());
    	return false;
    });
    $('#cmd').change(function(){
    	var v=$(this).val();
    	if(v.length>1 && v.indexOf(' ')<0){
    		$('#commands-container li').hide();
    		$('#commands-container li[data-name*="'+v+'"]').show();
    	}
    });
};
function filterGroup(group){
	$('#commands-container li').hide();
	$('#commands-container li[data-group='+group+']').show();
}
</script>
<div id="result"></div>
<select style="height:25px" onchange="filterGroup($(this).val())">
	<option value=''>All</option>
<optgroup label="...">
	<option value='generic'>Keys - 键</option>
	<option value='string'>Strings - 字符串</option>
	<option value='list'>Lists - 列表</option>
	<option value='hash'>Hashes - 哈希</option>
	<option value='set'>Sets - 集合</option>
	<option value='sorted_set'>Sorted Sets - 有序集合</option>
</optgroup>
<optgroup label="...">
	<option value='hyperloglog'>HyperLogLog</option>
	<option value='geo'>Geo</option>
	<option value='pubsub'>Pub/Sub - 发布/订阅</option>
	<option value='cluster'>Cluster - 集群</option>
	<option value='connection'>Connection - 连接</option>
	<option value='scripting'>Scripting - 脚本</option>
	<option value='server'>Server - 服务器</option>
	<option value='stream'>Streams - 流</option>
	<option value='transactions'>Transactions - 事务</option>
</optgroup>
</select>

<form style="display: inline-block;margin-bottom: 0;" id="form">
	<div style="position:relative;display: inline-block;">
		<span style="margin-left:200px;width:18px;overflow:hidden;">
			<select style="width:218px;margin-left:-200px;height: 25px;"
			 onchange="$('#cmd').val(this.value);$('#cmd').focus();">
			 <option value=""> </option>
<?php
foreach ($shows as $_show) {
	echo "<option value=\"$_show\"> $_show </option>\n";
}
?>
			</select>
		</span>
		<input type="text" name="q" id="cmd" value="<?php echo $q; ?>"
			style="width:200px;position:absolute;left:2px;top:2px;height: 21px;border:0;" />
	</div>
    <input type="submit" value="Go" />
</form>
<div id="commands-container"></div>
