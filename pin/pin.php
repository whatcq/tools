<?php
header('Access-Control-Allow-Origin: *');
echo 
file_put_contents('pic.txt', json_encode($_POST+array('time'=>$_SERVER['REQUEST_TIME']))."\r\n", FILE_APPEND)
?1:0;