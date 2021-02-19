<?php
$ip = $_SERVER['REMOTE_ADDR'];
if ($ip !== '127.0.0.1' && $ip !== '::1') {
    die('403-' . $ip);
}

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
function s_array(&$array)
{
    return is_array($array) ? array_map('s_array', $array) : stripslashes($array);
}
if (function_exists('set_magic_quotes_runtime')) {
    set_magic_quotes_runtime(0);
}

if (get_magic_quotes_gpc()) {
    $_REQUEST = s_array($_REQUEST);
}

$path = basename(__FILE__, '.php') . '/';
$this_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$filename = 'test';
$source = '&lt;?php
/*' . date('Y-m-d') . '
*/
header("Content-type:text/html;charset=utf-8");
include \'common.func.php\';

var_dump(

);
';
if (isset($_REQUEST['filename'])) {
    $filename = $_REQUEST['filename'];
    $file = $this_dir . $path . $filename . '.php';
}

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

if ($act === 'save_run') {
    //exit($act);
    $file = $path . $_REQUEST['filename'] . '.php';
    $source = preg_replace('/((\$\w+)\s?=.*)#(\r?\n)/', "\$1var_dump(\$2);\$3", $_REQUEST['source']);
    file_put_contents($file, $source);
    header('location:' . $file);
    exit;
} elseif ($act === 'open_it_with_editplus') {
    /*在服务器端执行命令，用editplus.exe打开文件;(不知为何不行了现在……)*/
//    $program='D:\\Program Files\\EditPlus 3\\editplus.exe';
    //    if(!file_exists($file)) {
    //        exit('File '.htmlspecialchars($file).' not found!');
    //    }
    //    if(!file_exists($program)) {
    //        exit('program not found!');
    //    }
    //尝试1
    //    $output = shell_exec('start "" "'.$program.'" "'.$file.'"');
    //    $shell= new COM('Shell.Application') or die('启动COM失败！');
    //尝试2
    //    $a = $shell->Open("\"$program\" \"$file\"");
    //    var_dump($a);
    //尝试3
    //    $a = $shell->ShellExecute($program,$file);
    //尝试4
    //    $a = $shell->ShellExecute('c:\windows\system32\cmd.exe','/c start "" "D:\Program Files\tools\putty.exe"');
    //尝试5
    //    $WshShell = new COM("WScript.Shell");
    //    $a = $WshShell->Run("cmd /C start  \"$file\" ", 3, true);
    //    var_dump($a);
    echo '<body onload="document.getElementById(\'tt\').focus();"><input id="tt" type="text" value="',
        (strtr($file, '/', '\\')), '" onfocus="this.select();typeof window.clipboardData===\'object\' ? window.clipboardData.setData(\'text\', this.value):document.execCommand(\'copy\');" size="50" />&lt;=复制了';
    exit;
}

if (isset($file)) {
    if (!file_exists($file)) {
        $msg = 'File not exists!';
    } else {
        $source = file_get_contents($file);
    }
}

// 切换输入框模式
if (isset($_COOKIE['textarea'])) {
    $textarea = $_COOKIE['textarea'];
} else {
    $textarea = 1; //默认值
}
if (isset($_GET['textarea'])) { //$_REQUEST variables_order:GetPostCookie会覆盖！
    setcookie('textarea', $_GET['textarea']) or print('set cookie failed!');
    $textarea = (boolean) $_GET['textarea'];
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
#txt_ln{
    height:600px;
    font-family: Consolas,'Lucida Console',Monaco,'Courier New',Courier, monospace;
    background-color:#ecf0f5;
    color:#c0bebe;
    border:none;
    text-align:right;
    overflow:hidden;
    scrolling:no;
    padding-right:0;
    font-size:16px;
    max-width:30px;
    padding-right: 4px;
}
#source{
    width:600px;
    height:600px;
    font-family: Consolas,'Lucida Console',Monaco,'Courier New',Courier, monospace;
    font-size:16px;
    tab-size: 4;
    color: #5021b0;
}
.area_0{
    height: 800px !important;
}
*:focus {outline: none;}
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
                    <div style="position:relative;display:inline-block;">
			<span>
				<select style="width:218px;height: 25px;" onchange="eval('this.parentNode.nextSibling'+(!top.execScript?'.nextSibling':'')+'.value=this.value');">
					<option></option>
					<?php
                    foreach (glob("{$path}*.php") as $php_filename) {
                        $php_filename = basename($php_filename, '.php');
                        echo "<option value=\"$php_filename\"> $php_filename </option>\n";
                    }
                    ?>
				</select>
			</span>
                        <input type="text" name="filename" id="filename" value="<?php echo $filename; ?>"
                               style="width: 200px;position: absolute;left: 2px;top: 1px;border: none;height: 23px;" />
                    </div>

                    <button onclick="openfile.location='?act=open_it_with_editplus&filename='+$('filename').value;return false;" title="Open it with Editplus">Source</button>
                    <button onclick="location='?filename='+$('filename').value;return false;" title="Load this file=>">Load it</button>
                    <button onclick="iframe.location='<?php echo $path; ?>'+$('filename').value+'.php';console.log($('filename').value);return false;" title="Run this file=>">Run</button>
                    <span title="- 赋值语句后加上#会打印出结果&#10;- Ctrl+j 复制当前行/选中文本">?</span>

                    <?php if (isset($msg)) {
                        echo $msg;
                    }
                    ?>
                </div>

                <table width="100%" cellspacing="0">
                    <tr>
                        <?php if ($textarea): ?>
                            <td style="width:28px;"><textarea id="txt_ln" rows="40" cols="4" wrap="off" readonly="true"><?php echo implode("\n", range(1, 31)) . "\n"; ?></textarea></td>
                        <?php endif; //@todo fix codemirror area height ?>
                        <td valign="top"><textarea name="source" id="source" <?=$textarea ? 'onscroll="show_ln()" rows="40" cols="80"' : 'class="area_0"'?> wrap="off"><?php echo str_replace('</textarea>', '&lt;/textarea>', $source); ?></textarea></td>
                    </tr>
                </table>
                <input type="submit" value="Run (Ctrl+S)" style="width:90px;height:40px;">
                <a href="?textarea=1&filename=<?php echo $filename; ?>">textarea</a> | <a href="?textarea=0&filename=<?php echo $filename; ?>">richarea</a>
            </form>
        </td>
    </tr>
</table>
<?php if ($textarea): ?>
<script>
// 防抖动函数
function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}
var i=32;
// fix for Chrome 85/86
var show_ln = debounce(function()
{
    var txt_ln = $('txt_ln');
    var txt_main = $('source');
    txt_ln.scrollTop = txt_main.scrollTop;
    while (txt_ln.scrollTop != txt_main.scrollTop) {
        txt_ln.value += (i++) + '\n';
        txt_ln.scrollTop = txt_main.scrollTop;
    }
    return;
}, 50);
// textarea indent+tab
function indent(tx) {
    tx.addEventListener("keydown", function (e) {
        if (e.key === 'Enter' || e.key === 'Tab' || (e.ctrlKey && e.key === 'j')) {
            e.preventDefault();
            var start = this.selectionStart
                , end = this.selectionEnd
                , txt = this.value
                , prefix = txt.substring(0, start)
                , suffix = txt.substring(end);
        } else return;
        if (e.key === 'Enter') {
            var breakPoint = txt.lastIndexOf('\n', start - 1)
                , prevLine = txt.substring(breakPoint + 1, start)
                , prevLineSpaces = prevLine.match(/^\s*/gi)[0];
            this.value = prefix + '\n' + prevLineSpaces + suffix;
            this.selectionStart = this.selectionEnd = start + prevLineSpaces.length + 1;
            return;
        }
        if (e.key === 'Tab') {
            if (end > start) {
                start = txt.lastIndexOf('\n', start - 1) + 1;
                var newSelection = txt.slice(start, end)
                    , indentedText = e.shiftKey
                    ? newSelection.replace(/(^|\n)\t/g, "$1")//unindent
                    : newSelection.replace(/^|\n/g, '$&\t')//indent
                    , replacementsCount = indentedText.length - newSelection.length;
                this.value = txt.substring(0, start) + indentedText + suffix;
                this.selectionStart = start + 1;
                this.selectionEnd = end + replacementsCount;
                return;
            }
            this.value = prefix + "\t" + suffix;
            this.selectionStart = this.selectionEnd = start + 1;
        }
        if (e.ctrlKey && e.key === 'j') {//duplicate line/selection
            if (end > start) {
                this.value = txt.substring(0, end) + txt.slice(start, end) + suffix;
                this.selectionStart = this.selectionEnd = end + end - start;
                return;
            }
            var breakPoint = txt.lastIndexOf('\n', start - 1)
                , breakPointNext = txt.indexOf('\n', start)
                , prevLine = txt.substring(breakPoint, breakPointNext);
            this.value = txt.substring(0, breakPoint) + prevLine + prevLine + txt.substring(breakPointNext);
            this.selectionStart = this.selectionEnd = start + prevLine.length;
        }
    });
}
indent($('source'));
</script>
<?php else: ?>
<link href="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/codemirror.min.css" rel="stylesheet">
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/codemirror.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/mode/xml/xml.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/mode/javascript/javascript.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/mode/css/css.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/mode/clike/clike.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/mode/php/php.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/addon/comment/comment.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/codemirror/5.54.0/keymap/sublime.min.js"></script>
<script>
var value = "// The bindings defined specifically in the Sublime Text mode\nvar bindings = {\n";
var map = CodeMirror.keyMap.sublime;
for (var key in map) {
    var val = map[key];
    if (key != "fallthrough" && val != "..." && (!/find/.test(val) || /findUnder/.test(val)))
        value += "  \"" + key + "\": \"" + val + "\",\n";
}
value += "  \"Ctrl-S\": function(){console.log(self.location)}\n";
value += "}\n\n// The implementation of joinLines\n";
value += CodeMirror.commands.joinLines.toString().replace(/^function\s*\(/, "function joinLines(").replace(/\n  /g, "\n") + "\n";
var editor = CodeMirror.fromTextArea($("source"), {
    value: value,
    lineNumbers: true,
    matchBrackets: true,
    mode: "php",
    indentUnit: 2,
    indentWithTabs: true,
    enterMode: "keep",
    keyMap: 'sublime',
    tabMode: "shift"
});
</script>
<style type="text/css">
.CodeMirror {
    border: 1px solid #eee;
    height: auto;
}
.CodeMirror-scroll {
    height: 600px;
    width: 700px;
}
.codemirror,.codemirror pre{
    font-family: Consolas, 'Lucida Console',  Monaco, 'Courier New', Courier, monospace;
    font-size: 12px;
}
</style>
<?php endif;?>
<script>
document.onkeydown = function (e) {
    if (e.key==='s' && e.ctrlKey) {
        document.forms[0].submit();
        return false;
    }
};
</script>
</body>
</html>