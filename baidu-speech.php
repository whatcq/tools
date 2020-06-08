<?php

if(isset($_GET['text'])){
	require_once 'AipSpeech.php';//引入所需文件

	// 你的 APPID AK SK这些可以在你的控制台中查看
	define('APP_ID', '20298034');
	define('API_KEY', 'hEFySayVx0eN8EqtL0gx9AbX');
	define('SECRET_KEY', 'xS2fPV7G3Px4YRuub3x4XmtfRuGN2APa');

	$text = $_GET['text'];

	$client = new AipSpeech(APP_ID, API_KEY, SECRET_KEY);//实例化
	$result = $client->synthesis('你好百度', 'zh', 2, array(
			'vol' => 5,
			'per'=>4,
			'tex'=>$text,
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
	<source src="?text=<?= $_GET['_text']?>" type="audio/mpeg">
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
</body>
</html>