<?php

/**
 * c/java等代码转成php
 * @param $s
 * @return array|string|string[]|null
 */
function c2php($s)
{
    $s = str_replace(['void ', 'int '], '', $s);
    $s = preg_replace_callback('/[_a-z][_\w]*\b(?!\()/m', function ($matches) {
        $item = $matches[0];
        if (in_array($item, ['function', 'if', 'else', 'while', 'for', 'foreach', 'return', 'true', 'false', 'break',])) {
            return $item;
        }

        return '$' . $item;
    }, $s);

    return $s;
}

/**
 * 翻译
 * @param array $lines
 * @return array|false
 */
function translate(array $lines)
{
    include_once 'functions.php';
    if (preg_match('/[^\x00-\x7F]/', $lines[0])) {
        $from = 'chinese_simplified';
        $to = 'english';
    } else {
        // is ascii
        $from = 'english';
        $to = 'chinese_simplified';
    }
    $content = curl_post(
        'https://api.translate.zvo.cn/translate.json?v=2.4.2.20230719',
        [
            'from' => $from,
            'to'   => $to,
            'text' => json_encode($lines, JSON_UNESCAPED_UNICODE),
        ],
        ['Content-Type: application/x-www-form-urlencoded'],
        5
    );
    $result = json_decode($content, true);
    // echo '<xmp>', $content, '</xmp>';
    if (!empty($result['result'])) {
        return array_combine($lines, $result['text']);
    }

    return false;
}
