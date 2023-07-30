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

echo file_put_contents($folder . date('YmdHi') . '.txt', file_get_contents('php://input'));
