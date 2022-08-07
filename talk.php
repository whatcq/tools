<?php
if (isset($_GET['talk'])) {
	$file = 'talk.log';
	$text = $_GET['talk'];
	// 这个分词只给出词+概率，不承包最后结果
	// param1:0-全部词 1-100%概念词
	// param2:1-debug
	$json = file_get_contents("http://api.pullword.com/get.php?source={$text}&param1=0&param2=1&json=1");
	$words = json_decode($json, 1);
	file_put_contents($file, "\n" . date('Y-m-d H:i:s') . ' ' . $text, FILE_APPEND);

	$responseText = '';
	foreach ($words as $word) {
		if ($word['p'] > 0.6) {
			$responseText .= ' ' . $word['t'];
		}
	}

	$vol = $_GET['vol'] ?? 9;
	$speed = $_GET['speed'] ?? 8;
	$per = $_GET['per'] ?? 3;

	die('<script>parent.response("bot", "' . $responseText . '")</script>');

	echo <<<EOF
<audio controls autoplay xmuted id="audio"><!--  -->
	<source src="https://tts.baidu.com/text2audio?tex={$responseText}&cuid=baike&lan=ZH&ie=utf-8&ctp=1&pdt=301&vol={$vol}&rate=32&per={$per}&spd={$speed}" type="audio/mpeg">
</audio>
EOF;
	die;
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>说话</title>
</head>
<style>
	.chat{display: block;clear:both;}
	.chat u{background: darkorange; border-radius: 3px; padding: 1px 4px; text-decoration: none;}
	.i-say{float:right;}
	.i-say u{background: lightgreen;float: right;}
</style>

<body style="/*display: flex;justify-content: center;align-items: center;*/">
	<div style="width: 700px;margin: 0 auto;">
		<div id="chatroom"></div>

		<form id="talk-form" target="talkFrame">
			<div>
				<textarea name="talk" id="input" rows="8" cols="80" style="width: 100%;font: 20px/24px Verdana;"></textarea>
			</div>
			<input type="submit">
			<input type="reset">
			<input type="button" value="清屏" onclick="$('chatroom').innerHTML=''">
		</form>
		<br>
		<audio controls autoplay xmuted id="speaker">
			<!--  -->
			<source src="" type="audio/mpeg">
		</audio>
		speed:<input type="number" name="speed" id="speed" value="8" min=0 max=15 width="20" />
		vol:<input type="number" name="vol" id="vol" value="9" min=0 max=15 width="20" />
		<select name="per" id="per">
			<option value="0">标准女音</option>
			<option value="1">标准男音</option>
			<option value="3" selected>斯文男音</option>
			<!--<option value="4">小萌萌</option>
				<option value="5">知性女音</option>
				<option value="6">老教授</option>
				<option value="8">葛平音</option>
				<option value="9">播音员</option>
				<option value="10">京腔</option>
				<option value="11">温柔大叔</option>
			-->
		</select>
		<iframe name="talkFrame" id="talkFrame" width=100% height="20" src="about:blank" title="audio-play"></iframe>
	</div>
</body>
<script>
	function $(str) {
		return document.getElementById(str);
	}
	var input = $('input');
	var chatroom = $('chatroom');
	var nick = 'cqiu'; //prompt("enter your name");

	window.onload = function() {
		$('input').focus();
	};

	// 防抖动函数
	const debounce = function(fn, delay) {
		let timer = null;
		const _debounce = function() {
			if (timer) clearTimeout(timer);
			timer = setTimeout(() => {
				console.log(+new Date)
				fn()
			}, delay);
		};
		return _debounce;
	};

	function chat(who, msg) {
		var div = document.createElement("div");
		div.className = 'chat';
		if(who==nick)div.className += ' i-say';
		div.innerHTML = ('<u>' + who + '</u>' + msg); //.trim();
		chatroom.append(div);
		input.scrollIntoView();
	}

	input.oninput = debounce(function() {
		var msg = input.value;
		if (!msg.trim()) return;
		chat(nick, msg);
		$('talk-form').submit();
		$('talkFrame').focus();
	}, 1000);

	function speak(msg) {
		var vol = $('vol').value, speed = $('speed').value, per = $('per').value;
		speaker.src = `https://tts.baidu.com/text2audio?tex=${msg}&cuid=baike&lan=ZH&ie=utf-8&ctp=1&pdt=301&vol=${vol}&rate=32&per=${per}&spd=${speed}`;
	}

	function response(who, msg) {
		chat(who, msg);
		speak(msg);
		input.value = '';
		input.focus();
	}
</script>

</html>