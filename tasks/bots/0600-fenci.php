<?php

$botName = '分词';

/** @var $text string */
// @see https://www.ownthink.com/docs/slu/#_13
$json = file_get_contents("https://api.ownthink.com/slu?spoken={$text}");
$words = json_decode($json, 1);

return implode(' ', $words['data']['词法分析']['中文分词']);

// 这个分词只给出词+概率，不承包最后结果
// param1:0-全部词 1-100%概念词
// param2:1-debug
$json = file_get_contents("http://api.pullword.com/get.php?source={$text}&param1=0&param2=1&json=1");
$words = json_decode($json, 1);

$keyword = '';
$score = 0;
$_words = [];
foreach ($words as $word) {
    if ($word['p'] > 0.6) {
        $_words[] = $word['t'];
    }
    if ($word['p'] > $score) {
        $score = $word['p'];
        $keyword = $word['t']; // 概率最大的关键词
    }
}

return implode(' ', array_unique($_words));
