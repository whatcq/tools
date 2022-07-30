<?php
/*
chatroom base on eventsource (chrome)
Author: cqiu
Date: 2019/11/28
*/
$file = 'tmp_msg.txt';
touch($file);

//=======for test
isset($_REQUEST['reset']) && die(file_put_contents($file, ''));
isset($_REQUEST['test']) && die(file_get_contents($file, false, null, 11));

//=======save message
if (isset($_REQUEST['post'])) {
	parse_str(file_get_contents("php://input"), $data);
	file_put_contents($file, "\n" . json_encode([
			'from' => htmlspecialchars($data['user']),
			//'to' => $data['to'],
			'msg' => htmlspecialchars($data['msg']),
			], JSON_UNESCAPED_UNICODE), FILE_APPEND);
	header("content:application/json;chartset=uft-8");
	exit(json_encode([
			'status' => 1
			]));
}

//=========pull message
if (isset($_REQUEST['data'])) {
	date_default_timezone_set("Asia/Chongqing");
	header("Content-Type: text/event-stream\n\n");

	ob_flush();
	flush();
	$offset = 1;
	while (1) {
		echo 'id: ', date('i:s'), "\n\n";
		if ($result = file_get_contents($file, false, null, $offset)) {
			echo 'data: ', $result, "\n\n";
			$offset += strlen($result) + 1;
		}else {
			echo ": )\n\n"; // 表示注释, 保持连接不中断
		}
		ob_flush();
		flush();
		//sleep(1);//问题：忽快忽慢
		usleep(500000);
	}
}

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Chatroom</title>
<script>
window.onload=function(){
	var nick = prompt("enter your name");
	var input = document.getElementById('input');
	var bt = document.getElementById('bt');

	var chat=new window.EventSource("?data");
	chat.onmessage =function(e){
		var msg = JSON.parse(e.data);
		var div = document.createElement("div");
		div.innerHTML = ('<u>' + msg.from + '</u>:' + msg.msg)//.trim();
		document.body.insertBefore(div, input);
		input.scrollIntoView();
	}

	bt.onclick = function(){
		var msg = "user="+ nick + "&to=all&msg="+input.value;
		var xhr= new XMLHttpRequest();
		xhr.open("POST",'?post');
		xhr.setRequestHeader("Content-type",'text/plain;charset=utf8');
		xhr.send(msg);
		input.value=""
	}
};
</script>

<input id='input' style="width:70%" onkeydown="if(event.keyCode == 13){bt.click()}"/>
<input type='button' value="send" label="send" id="bt"/>