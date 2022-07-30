<?php
if(isset($_GET['talk'])){
	$file = 'talk.log';
	$text = $_GET['talk'];
	// 这个分词只给出词+概率，不承包最后结果
	// param1:0-全部词 1-100%概念词
	// param2:1-debug
	$json = file_get_contents("http://api.pullword.com/get.php?source={$text}&param1=0&param2=1&json=1");
	$words = json_decode($json, 1);
	file_put_contents($file, "\n".date('Y-m-d H:i:s').' '.$text, FILE_APPEND);

	$responseText = '';
	foreach($words as $word){
		if($word['p'] > 0.6){
			$responseText .= ' ' . $word['t'];
		}
	}

	$vol = $_GET['vol'] ?? 9;
	$speed = $_GET['speed'] ?? 8;
	$per = $_GET['per'] ?? 3;
	echo <<<EOF
<audio controls autoplay xmuted id="audio"><!--  -->
	<source src="https://tts.baidu.com/text2audio?tex={$responseText}&cuid=baike&lan=ZH&ie=utf-8&ctp=1&pdt=301&vol={$vol}&rate=32&per={$per}&spd={$speed}" type="audio/mpeg">
</audio>
EOF;
	die;

	die('<script>parent.getResult('.$json.')</script>');
}

/*
https://www.cnblogs.com/HGNET/p/16304126.html

*/
if(isset($_GET['text'])){
	$vol = $_GET['vol'] ?? 9;
	$speed = $_GET['speed'] ?? 8;
	$per = $_GET['per'] ?? 3;
	echo <<<EOF
<audio controls autoplay xmuted id="audio"><!--  -->
	<source src="https://tts.baidu.com/text2audio?tex={$_GET['text']}&cuid=baike&lan=ZH&ie=utf-8&ctp=1&pdt=301&vol={$vol}&rate=32&per={$per}&spd={$speed}" type="audio/mpeg">
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
<body style="display: flex;justify-content: center;align-items: center;" onload="document.getElementById('input').focus()">
<blockquote style="width: 700px;margin: 20px 150px;">

<form id="talk-form" target="talkFrame">
<div>
	<textarea name="talk" id="input" rows="8" cols="80"
	style="width: 100%;font: 20px/24px Verdana;"></textarea>
</div>
	<input type="submit">
</form>

<iframe name="talkFrame" id="talkFrame" width=100% src="about:blank" title="audio-play"></iframe>
<hr>
<script>
	function $(str) {
        return document.getElementById(str);
    }
	// 防抖动函数
	const debounce = function (fn, delay) {
	  let timer = null;
	  const _debounce = function () {
	    if (timer) clearTimeout(timer);
	    timer = setTimeout(()=>{
	    	console.log(+new Date)
	      fn()
	    }, delay);
	  };
	  return _debounce;
	};

	$('input').oninput = debounce(function(){
		$('talk-form').submit();
		$('talkFrame').focus();
	}, 1000);
</script>

<form target="audioFrame">
	<div>
	<textarea name="text" rows="8" cols="80">人生天地间，忽如远行客</textarea>
	</div>
	speed:<input type="number" name="speed" value="8" min=0 max=15 width="20" />
	vol:<input type="number" name="vol" value="9" min=0 max=15 width="20" />
	<select name="per">
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
	<input type="submit">
</form>

<iframe name="audioFrame" width=100% src="about:blank" title="audio-play"></iframe>

</blockquote>
</body>
</html>
