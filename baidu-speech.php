<?php

/*
tex	必填	合成的文本，使用UTF-8编码。小于2048个中文字或者英文数字。（文本在百度服务器内转换为GBK后，长度必须小于4096字节）
tok	必填	开放平台获取到的开发者access_token（见上面的“鉴权认证机制”段落）
cuid	必填	用户唯一标识，用来计算UV值。建议填写能区分用户的机器 MAC 地址或 IMEI 码，长度为60字符以内
ctp	必填	客户端类型选择，web端填写固定值1
lan	必填	固定值zh。语言选择,目前只有中英文混合模式，填写固定值zh
spd	选填	语速，取值0-15，默认为5中语速
pit	选填	音调，取值0-15，默认为5中语调
vol	选填	音量，取值0-15，默认为5中音量
per（基础音库）	选填	度小宇=1，度小美=0，度逍遥=3，度丫丫=4
per（精品音库）	选填	度博文=106，度小童=110，度小萌=111，度米朵=103，度小娇=5
aue	选填	3为mp3格式(默认)； 4为pcm-16k；5为pcm-8k；6为wav（内容同pcm-16k）; 注意aue=4或者6是语音识别要求的格式，但是音频内容不是语音识别要求的自然人发音，所以识别效果会受影响。
*/

$speed = isset($_GET['speed']) ? $_GET['speed'] : 5;
if(isset($_GET['text'])){
	require_once 'AipSpeech.php';//引入所需文件

	// 你的 APPID AK SK这些可以在你的控制台中查看
	define('APP_ID', '20298034');
	define('API_KEY', 'hEFySayVx0eN8EqtL0gx9AbX');
	define('SECRET_KEY', 'xS2fPV7G3Px4YRuub3x4XmtfRuGN2APa');

	$text = $_GET['text'];

	$client = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);//实例化
	$result = $client->synthesis($text, 'zh', 2, array(
			'vol' => 5,
			'per'=>4,
			'spd'=> (int)$speed,
	));
	echo $result;
	die;
	// 识别正确返回语音二进制 错误则返回json 参照下面错误码
	if(!is_array($result)){
		file_put_contents('audio.mp3', $result);
	}
}


?>
<!DOCTYPE html>
<html>
<head> 
<meta charset="utf-8"> 
<title>听写</title>
</head>
<body>
<audio controls autoplay xmuted id="audio">
	<source src="?speed=<?= $speed?>&text=<?= $_GET['_text']?>" type="audio/mpeg">
</audio>
<script language="javascript">
var _audio=document.getElementById('audio'),i=0;
function pp(){
	setInterval(function(){
		if(_audio.paused && i++%3==0)_audio.play();
		else _audio.pause();
		console.log(i,_audio.paused);
	}, 1000);
}
</script>
<input type="button" value="play" id="play" onclick="pp()">
<a href="baidu-speech.php?speed=1&_text=%E8%98%91%E8%8F%87%20%E5%8B%A4%E5%8A%B3%20%E8%B2%94%E8%B2%85%20%E6%91%87%E6%9B%B3%20%E5%85%8B%E9%9A%86%20%E5%97%AB%E5%9A%85%20%E8%AE%A4%E7%9C%9F%20%E7%A0%A5%E7%A0%BA%20%E6%8B%85%E5%BF%83%20%E7%AF%9D%E7%81%AB%20%E4%B8%A5%E8%82%83">test</a>
</body>
</html>