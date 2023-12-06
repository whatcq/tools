<?php

$botName = '搜';

$engineNames = [
    'm163'   => '网易同学',
    '163'    => '网易同学',
    'zhidao' => '知道同学',
    'baidu'  => '百度同学',
    'sogou'  => '搜狗同学',
    // 'sm' => '神马同学', // 请求被需要认证拦截 @todo
    'bing'   => '必应同学',
];
/* @var $text string */
if ($engine = array_search(mb_substr($text, 0, 4), $engineNames)) {
    $_SESSION['search_engine'] = $engine;
    $text = ltrim(str_replace($engineNames[$engine], '', $text), '?？,，.。 ');
} else {
    $engine = $_SESSION['search_engine'] ?? 'bing';
}

if (!$text) return '你要抓啊子？';
$keyword = $text; // 后面会用

$request = "search_$engine";
$botName = str_replace('同学', '', $engineNames[$engine]);
$cacheFile = __DIR__ . '/cache-' . $request . '.html';

$result = include 'request.php';
/* @var $content string */
if ('bing' === $engine) {
    $content = preg_replace('#<cite>(.*?)</cite>#i', '<a href="$1" target="bing">$1</a>', $content);
    $content = preg_replace('#<link rel="stylesheet" href="/.*?\.css" type="text/css"/>#i', '', $content);
}
$outHtml = '<link rel="stylesheet" href="../lib/base.css" />' . $content;

return $result;
