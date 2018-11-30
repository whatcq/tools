<?php
function getClientIp()
{
    if ($ip = getenv("HTTP_CLIENT_IP") && strcasecmp($ip, "127.0.0.1"));
    else if ($ip = getenv("HTTP_X_FORWARDED_FOR") && strcasecmp($ip, "127.0.0.1"));
    else if ($ip = getenv("REMOTE_ADDR", true) && strcasecmp($ip, "127.0.0.1"));
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "127.0.0.1"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "127.0.0.1";
    return ($ip);
}
$ip = getClientIp();
if($ip !== '127.0.0.1' && $ip !=='::1')die('403'.getClientIp());
/*
Author: Cqiu <gdaymate@126.com>
Created: 2010-7-26
Modified: 2013-7-26 16:13
要点：
- 可输入下拉框
- 带行号文本框
- 自动复制文本内容
- 自适应宽度
- 兼容ie,ff,chrome
*/
//如果ini没设默认的会报错
date_default_timezone_set('Asia/Chongqing');

// 去掉转义字符
function s_array(&$array) {
	return is_array($array) ? array_map('s_array', $array) : stripslashes($array);
}
if(function_exists('set_magic_quotes_runtime'))set_magic_quotes_runtime(0);
if(get_magic_quotes_gpc()) {
	$_REQUEST = s_array($_REQUEST);
}


$path = basename(__FILE__, '.php') . '/';
$this_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
$filename='test';
$source='&lt;?php
/*'.date('Y-m-d').'
*/
header("Content-type:text/html;charset=utf-8");
include \'common.func.php\';

var_dump(

);
';
if(isset($_REQUEST['filename'])) {
	$filename=$_REQUEST['filename'];
	$file=$this_dir.$path.$filename.'.php';
}

$act = isset($_REQUEST['act'])?$_REQUEST['act']:'';

if($act==='save_run') {
	$file = $path.$_REQUEST['filename'].'.php';
	$source = preg_replace('/((\$\w+)\s?=.*)#(\r?\n)/', "\$1var_dump(\$2);\$3", $_REQUEST['source']);
	file_put_contents($file, $source);
	header('location:'.$file);
	exit;
}

elseif($act==='open_it_with_editplus'){
	/*在服务器端执行命令，用editplus.exe打开文件;(不知为何不行了现在……)*/
//	$program='D:\\Program Files\\EditPlus 3\\editplus.exe';
//	if(!file_exists($file)) {
//		exit('File '.htmlspecialchars($file).' not found!');
//	}
//	if(!file_exists($program)) {
//		exit('program not found!');
//	}
//尝试1
//	$output = shell_exec('start "" "'.$program.'" "'.$file.'"');
//	$shell= new COM('Shell.Application') or die('启动COM失败！');
//尝试2
//	$a = $shell->Open("\"$program\" \"$file\"");
//	var_dump($a);
//尝试3
//	$a = $shell->ShellExecute($program,$file);
//尝试4
//	$a = $shell->ShellExecute('c:\windows\system32\cmd.exe','/c start "" "D:\Program Files\tools\putty.exe"');
//尝试5
//	$WshShell = new COM("WScript.Shell"); 
//	$a = $WshShell->Run("cmd /C start  \"$file\" ", 3, true); 
//	var_dump($a);
	echo '<body onload="document.getElementById(\'tt\').focus();"><input id="tt" type="text" value="',(strtr($file,'/','\\')),'" onfocus="this.select();window.clipboardData.setData(\'text\', this.value);" size="50" />已复制';
	exit;
}

if(isset($file)) {
	if(!file_exists($file)) {
		$msg = 'File not exists!';
	}else {
		$source=file_get_contents($file);
	}
}

// 切换输入框模式
if(isset($_COOKIE['textarea'])) {
	$textarea = $_COOKIE['textarea'];
}else {
	$textarea = 1;//默认值
}
if(isset($_GET['textarea'])) {//$_REQUEST variables_order:GetPostCookie会覆盖！
	setcookie('textarea', $_GET['textarea']) or print('set cookie failed!');
	$textarea = (boolean)$_GET['textarea'];
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>php playground</title>
<style type="text/css">
body,html{
	margin:0;
	padding:0;
	overflow:hidden;
}
</style>
<script type="text/javascript">
function $(str) {
	return document.getElementById(str);
}
</script>
<body scroll="no">
<table width="100%" height="100%">
	<tr>
		<td width="*">
<iframe width="100%" height="100%" src="about:blank" name="iframe"></iframe>
			</td>
		<td valign="top" width="600">
<iframe width="100%" height="60" src="about:blank" name="openfile"></iframe>
<form method="post" action="?act=save_run" target="iframe" style="display:inline;">

	<div>
	<div style="position:relative;">
		<span style="margin-left:200px;width:18px;overflow:hidden;">
			<select style="width:218px;margin-left:-200px;" onchange="eval('this.parentNode.nextSibling'+(!top.execScript?'.nextSibling':'')+'.value=this.value');">
				<?php
				foreach (glob("{$path}*.php") as $php_filename) {
					$php_filename = basename($php_filename, '.php');
					echo "<option value=\"$php_filename\"> $php_filename </option>\n";
				}
				?>
			</select>
		</span>
		<input type="text" name="filename" id="filename" value="<?php echo $filename;?>" 
			style="width:200px;position:absolute;left:0px;top:1px;" />

		<button onclick="openfile.location='?act=open_it_with_editplus&filename='+$('filename').value;" title="Open it with Editplus">Source</button>
		<button onclick="location='?filename='+$('filename').value;" title="Load this file=>">Load it</button>
		<button onclick="iframe.location='<?php echo $path;?>'+$('filename').value+'.php';" title="Run this file=>">Run</button>
        <span title="赋值语句后加上#会打印出结果">?</span>
		
		<?php if(isset($msg))echo $msg;?>
	</div>
	</div>

	<table width="100%" cellspacing="0">
		<tr>
<?php if($textarea):?>
			<td style="width:28px;"><textarea id="txt_ln" rows="40" cols="4" style="height:600px;font-family: Consolas,'Lucida Console',Monaco,'Courier New',Courier, monospace;background-color:#838383;color:#F3F3F3;border:none;text-align:right;overflow:hidden;scrolling:no;padding-right:0;font-size:16px;max-width:30px;" readonly="true"><?php echo implode("\n",range(1,31))."\n";?></textarea></td>
<?php endif;?>
			<td valign="top"><textarea name="source" id="source" rows="40" cols="80"  onscroll="show_ln()" wrap="off" style="width:600px;height:600px;font-family: Consolas,'Lucida Console',Monaco,'Courier New',Courier, monospace;font-size:16px;"><?php echo str_replace('</textarea>','&lt;/textarea>',$source);?></textarea></td>
		</tr>
	</table>
	<input type="submit" value="Run" style="width:80px;height:40px;">
	<a href="?textarea=1&filename=<?php echo $filename;?>">textarea</a> | <a href="?textarea=0&filename=<?php echo $filename;?>">richarea</a>
</form>
		</td>
	</tr>
</table>
<!-- 
(.)(.)
 )  ( 
卜orz么
 -->
<?php if($textarea):?>
<script language="javascript">
var i=32;
function show_ln()
{
   var txt_ln = $('txt_ln');
   var txt_main = $('source');
   txt_ln.scrollTop = txt_main.scrollTop;
   while(txt_ln.scrollTop != txt_main.scrollTop) 
   {
    txt_ln.value += (i++) + '\n';
    txt_ln.scrollTop = txt_main.scrollTop;
   }
   return;
}
</script>
<?php else:?>
<link rel="stylesheet" href="testbase/CodeMirror-2.33/lib/codemirror.css">
<script src="testbase/CodeMirror-2.33/lib/codemirror.js"></script>
<script src="testbase/CodeMirror-2.33/mode/xml/xml.js"></script>
<script src="testbase/CodeMirror-2.33/mode/javascript/javascript.js"></script>
<script src="testbase/CodeMirror-2.33/mode/css/css.js"></script>
<script src="testbase/CodeMirror-2.33/mode/clike/clike.js"></script>
<script src="testbase/CodeMirror-2.33/mode/php/php.js"></script>
<script>
	var editor = CodeMirror.fromTextArea($("source"), {
		lineNumbers: true,
		matchBrackets: true,
		mode: "application/x-httpd-php",
		indentUnit: 4,
		indentWithTabs: true,
		enterMode: "keep",
		tabMode: "shift"
	});
</script>
<style type="text/css">
.CodeMirror-scroll {
	height: 600px;
	width: 700px;
}
.codemirror,.codemirror pre{
	font-family: Consolas, 'Lucida Console',  Monaco, 'Courier New', Courier, monospace;
}
</style>
<?php endif;?>
</body>
</html>