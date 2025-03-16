<?php

/**
 * 后台task处理进程/服务
 * 可以修改为redis publish/subscribe
 */
if (PHP_SAPI !== "cli") {
    die('CLI only!');
}

date_default_timezone_set('Asia/Chongqing');

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
        $result = `$cmd`;
        info($result);
    }
    sleep(1);
}
