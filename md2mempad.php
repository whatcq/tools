<?php
/**
 * markdown转mempad
 * 一般markdown不会太大吧，所以没做逐行读取，撇脱
 * cqiu@2020-7-21
 */
define('SP', chr(0));

$file = 'D:\www\book\architect-awesome\architect.md';

$data = file_get_contents($file);
$data = preg_replace_callback(
    '/(?:^|\n)(#+) (.*)\r?\n/',
    function ($matches) {
        return SP . chr(strlen($matches[1])) . $matches[2] . SP;
    },
    $data);

$info['initialPage'] = 1;
$info['quickPagePath'] = '';
$header = 'MeMpAd.' . $info['initialPage'] . SP . $info['quickPagePath'];

echo file_put_contents("$file.lsf", $header . $data . SP), " ok\n";
