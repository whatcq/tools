<?php

if(!isset($argv[1]))exit('0');

$dataFile = 'D:\mysoft\cqiu-note.txt';

//不能传递crlf，怎样都会有问题 array_shift($argv);
$msg = iconv('gb2312','utf-8', str_replace(['`r', '`n'], ["\r", "\n"], $argv[1]));

echo file_put_contents($dataFile, json_encode([
	'msg' => $msg,
	'time' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND)
? strlen($msg)
: $msg;