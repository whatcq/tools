<?php

$folder = 'data/';

if ($_GET['act'] ?? '' === 'list') {
    // get files from folder
    $files = scandir($folder);
    foreach ($files as $file) {
        echo $file . "\n";
    }
    die('');
}

date_default_timezone_set('Asia/Chongqing');
// print_r($_POST);
// print_r($_FILES);

$date = date('YmdHi');
if ($_FILES) {
    // foreach($_FILES as $k=>$v){
    //     print_r($v);
    // }
    file_put_contents($folder . $date . '.lrc', $_POST['lrc']);
    if (move_uploaded_file($_FILES['upfile']['tmp_name'], $folder . $date . $_FILES['upfile']['name'])) {
        echo 'success';
    }
    die;
}

echo file_put_contents($folder . $date . '.txt', file_get_contents('php://input'));
