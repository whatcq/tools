<?php
header('Access-Control-Allow-Origin: *');
if (empty($_POST)) return;

echo file_put_contents(
    'pic.txt',
    json_encode($_POST + array('time' => $_SERVER['REQUEST_TIME']), JSON_UNESCAPED_UNICODE) . "\r\n",
    FILE_APPEND
) ? 1 : 0;
