<?php

$ip = $_SERVER['REMOTE_ADDR'];
if ($ip !== '127.0.0.1' && $ip !== '::1') {
    die('403-' . $ip);
}

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$path = __DIR__ . '/';

if ($act === 'save') {
    $file = $path . $_REQUEST['filename'];
    $c = file_put_contents($file, $_REQUEST['source']);
    die("$c");
}

die('nothing');
