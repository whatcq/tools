<?php

if (empty($_SESSION['mode']) || $_SESSION['mode'] !== '声律启蒙') {
    return;
}

$botName = '声律启蒙';

$d = file(__DIR__ . '/声律启蒙.txt');

while (1) {
    $i = mt_rand(26, 799);
    if (strlen($d[$i]) < 20) continue;
    if ($sentence = trim($d[$i])) return $sentence;
    if ($sentence = trim($d[$i - 6])) return $sentence;
}
