<?php
/**
 * 目标：一个 记录执行过程以便重复执行 的小工具
 * 思路：
 * 文件夹，or 函数库
 * 依次匹配输入，符合则执行程序
 * 如果没有匹配，则进入默认程序：录入脚本
 * @Cqiu 2020-12-31
 */
$messageFile = 'todo.msg';
function todo($cmd)
{
    global $messageFile;
    file_put_contents($messageFile, $cmd);
}

class ScriptResult
{
    public $isFinal;
    public $content;

    public function __construct($isFanal)
    {
        $this->isFinal = $isFanal;
    }

    public function done()
    {
        echo $this->content;
    }
}

$output = '';
if ($input = $_REQUEST['input'] ?? null) {
    foreach (glob('cqiu_ants/*.php') as $script) {
        echo '<li>', $script;
        $result = include $script;
        if ($result instanceof ScriptResult) {
            $result->done();
            if ($result->isFinal) break;
        } else {
            echo $result;
        }
    }
}

?>
<title>Done!</title>
<form action="?set">
    <textarea name="input" id="input" cols="200" rows="20" placeholder="输入" style="width:100%"><?= $output ?></textarea><br>
    <input type="submit" value="Go">
</form>
