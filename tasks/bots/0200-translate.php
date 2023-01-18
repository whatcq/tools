<?php

$text = <<<'STR'
•SplDoublyLinkedList::getIteratorMode — Returns the mode of iteration
•SplDoublyLinkedList::isEmpty — Checks whether the doubly linked list is empty
•SplDoublyLinkedList::key — Return current node index
•SplDoublyLinkedList::next — Move to next entry
•SplDoublyLinkedList::offsetExists — Returns whether the requested $index exists
•SplDoublyLinkedList::offsetGet — Returns the value at the specified $index
STR;

$botName = '译';

$engineNames = [
    'youdao'          => '网易有道',
    'baidu_fanyi'     => '百度翻译',
    'sogou_translate' => '搜狗翻译',
    'bing_translate'  => '必应翻译',
    'qq_fanyi'        => '腾讯翻译',
    'qq_transmart'    => '腾讯交互',
];
function_exists('curl_post') or include '../../lib/functions.php';

if ($engine = array_search(mb_substr($text, 0, 4), $engineNames)) {
    $_SESSION['engine'] = $engine;
    $text = ltrim(str_replace($engineNames[$engine], '', $text), '?？,，.。 ');
} else {
    $engine = $_SESSION['engine'] ?? 'bing';
}
$request = $engine = 'qq_transmart';
$botName = $engineNames[$engine];
$cacheFile = __DIR__ . '/cache-' . $engine . '-trans.json';

$keyword = $text;
if (!$keyword) return '你要抓啊子？';

$result = include 'request.php';
print_r($result);
