<?php
if (!empty($_GET['talk'])) {
	$logFile = 'talk.log';
	$text = $_GET['talk'];

	file_put_contents($logFile, "\n" . date('Y-m-d H:i:s') . ' ' . $text, FILE_APPEND);

	session_start();
	$botName = 'bot';
	$outHtml = '';
	foreach (glob('bots/*.php') as $script) {
		// @todo return json with rich content
        // @todo 功能列表-使用说明-自我介绍
		if ($responseText = include $script) {
			file_put_contents($logFile, "\n" . $script . ': ' . $responseText, FILE_APPEND);
            $responseText = str_replace(["\n", "\r"], ['\\n', ''], addslashes($responseText));
            die('<script>parent.response("' . $botName . '", "' . $responseText . '")</script>' . $outHtml);
		}
	}
	die;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta charset="utf-8">
	<title>说话</title>
    <link rel="stylesheet" href="talk.css" />
</head>

<body>
	<div style="width: 700px;margin: 0 auto;">
		<div id="chatroom"></div>

		<form id="talk-form" target="talkFrame">
			<div>
				<textarea name="talk" id="input" rows="4" cols="80" style="width: 100%;font: 20px/24px Verdana;"></textarea>
			</div>
			<input type="submit">
			<input type="reset">
			<input type="button" value="清屏" onclick="$('chatroom').innerHTML=''">
			<input type="text" id="back_msg" size="70">
            <input type="checkbox" id="speak-out" title="说话开关" checked />
		</form>
		<div id="speaker-div">
		<audio controls autoplay xmuted id="speaker">
			<source src="" type="audio/mpeg">
		</audio>
		speed:<input type="number" name="speed" id="speed" value="8" min=0 max=15 width="20" />
		vol:<input type="number" name="vol" id="vol" value="9" min=0 max=15 width="20" />
		<select name="per" id="per">
			<option value="0">标准女音</option>
			<option value="1">标准男音</option>
			<option value="3" selected>斯文男音</option>
			<!--
			<option value="4">小萌萌</option>
			<option value="5">知性女音</option>
			<option value="6">老教授</option>
			<option value="8">葛平音</option>
			<option value="9">播音员</option>
			<option value="10">京腔</option>
			<option value="11">温柔大叔</option>
			-->
		</select>
        </div>
		<iframe name="talkFrame" id="talkFrame" width=100% height="420" src="about:blank" title="audio-play" style="border: 1px solid #bfbfbf;"></iframe>
	</div>
</body>
<script>
	function $(str) {
		return document.getElementById(str);
	}
	var input = $('input');
	var chatroom = $('chatroom');
	var speaker = $('speaker');
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
				fn()
			}, delay);
		};
		return _debounce;
	};

	function chat(who, msg) {
		var div = document.createElement("div");
		div.className = 'chat';
		if (who === nick) div.className += ' i-say';
		div.innerHTML = ('<u class="q-' + who + '">' + who + '</u>' + msg); //.trim();
		chatroom.append(div);
		input.scrollIntoView();
	}

	function speak(msg) {
	    if (!$('speak-out').checked) return;
		var vol = $('vol').value,
			speed = $('speed').value,
			per = $('per').value;
		speaker.src = `https://tts.baidu.com/text2audio?tex=${msg}&cuid=baike&lan=ZH&ie=utf-8&ctp=1&pdt=301&vol=${vol}&rate=32&per=${per}&spd=${speed}`;
	}

	var responseLength = 0;

	function response(who, msg) {
		chat(who, msg.replace(/\n/g, '<br>').replace('  ', '&nbsp;&nbsp'));
		speak(encodeURIComponent(msg.replace(/<[^>]+/g, '')));
		input.value = '';
		$('back_msg').value = '';
		$('back_msg').focus();
		responseLength = msg.length;
		// setTimeout(function() {
		// 	input.focus();
		// }, 2500 + msg.length * 100);
	}

	var form = $('talk-form');

	form.onsubmit = function() {
		var msg = input.value;
		if (!msg.trim()) return false;
		chat(nick, msg);
		return true;
	};

	input.oninput = debounce(function() {
		if (!form.onsubmit()) return;
		form.submit();
		$('talkFrame').focus();
	}, 1000);

	document.onkeydown = function(e) {
		if (e.key === 'Escape') {
			speaker.pause();
			$('talk-form').reset();
			return false;
		}
	};

	speaker.onended = speaker.onpause = debounce(function() {
		input.focus();
	}, 1500);
	// $('back_msg').oninput = debounce(function() {
	// 	input.focus();
	// }, 1500);

    var speakerDiv = $('speaker-div');
    $('speak-out').onclick = function () {
        if (this.checked) {
            speakerDiv.style.display = '';
        } else {
            speakerDiv.style.display = 'none';
        }
    };
</script>

</html>