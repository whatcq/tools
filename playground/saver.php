<?php

$ip = $_SERVER['REMOTE_ADDR'];
if ($ip !== '127.0.0.1' && $ip !== '::1') {
    die('403-' . $ip);
}

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$path = __DIR__ . '/';

if ($act === 'files') {
    $files = [];
    $ext = $_REQUEST['ext'] ?? 'php';
    foreach (glob("{$path}*.{$ext}") as $filename) {
        $files[] = basename($filename, '.' . $ext);
    }
    header('Content-Type: application/json');
    echo json_encode($files, 448);
    die;
}
if ($act === 'upload') {
    $uploaded = [];
    foreach ($_FILES as $file) {
        $uploaded[$file['name']] = move_uploaded_file($file['tmp_name'], $path . $file['name']);
    }
    die(json_encode($uploaded));
}

if ($act === 'save') {
    $file = $path . $_REQUEST['filename'];
    $c = file_put_contents($file, $_REQUEST['source']);
    die("$c");
}

die('nothing');
