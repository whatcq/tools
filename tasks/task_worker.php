<?php

/**
 * 后台task处理进程/服务
 */
if (PHP_SAPI !== "cli") {
    die('CLI only!');
}
$messageFile = 'todo.msg';
file_exists($messageFile) or touch($messageFile);

function info($str)
{
    echo $data = date('[Y-m-d H:i:s] ') . $str . "\r\n";
    file_put_contents('task.log', $data, FILE_APPEND);
}

while (1) {
    $cmd = file_get_contents($messageFile);
    if ($cmd) {
        file_put_contents($messageFile, '');
        info($cmd);
        $result = `$cmd`;// >> task.log
        info($result);
    }
    sleep(1);
}
