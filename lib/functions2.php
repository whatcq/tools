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
    $map = [];
    foreach (array_chunk($lines, 100) as $chunk) {
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
        $map = array_merge($map, array_combine($lines, $result['text'] ?? $lines));
    }

    return $map;
}

function cache_get($key)
{
    $file = 'cache/' . $key;
    if (file_exists($file)) {
        return unserialize(file_get_contents($file));
    }

    return null;
}

function cache_set($key, $value)
{
    $file = 'cache/' . $key;
    if (is_null($value)) {
        return false;
    }
    is_dir('cache') or mkdir('cache', 0777, true);

    return file_put_contents($file, serialize($value));
}

function cache_getOrSet($key, $value)
{
    $cachedValue = cache_get($key);
    if ($cachedValue !== null) {
        return $cachedValue;
    }
    echo 'cache miss: ' . $key . PHP_EOL;
    cache_set($key, $value);

    return $value;
}
