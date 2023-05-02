<?php
header('Access-Control-Allow-Origin: *');

foreach ($_POST as $pic) {
    echo
    file_put_contents('pic.txt', json_encode($pic + array('time' => $_SERVER['REQUEST_TIME'])) . "\r\n", FILE_APPEND)
        ? 1 : 0;
}
