<?php

$text = <<<'STR'
•SplDoublyLinkedList::getIteratorMode — Returns the mode of iteration
•SplDoublyLinkedList::isEmpty — Checks whether the doubly linked list is empty
•SplDoublyLinkedList::key — Return current node index;
•SplDoublyLinkedList::next — Move to next entry;
STR;

$botName = '译';

$engineNames = [
    'youdao'          => '网易有道', // 结果需处理base64+?? todo
    'caiyun_fanyi'    => '彩云小译', // refresh-token todo
    '360_fanyi'       => '360翻译',
    'baidu_fanyi'     => '百度翻译', // sign,header差点弄好 todo
    'sogou_translate' => '搜狗翻译',
    'bing_translate'  => '必应翻译', // js:sign,token todo
    'qq_fanyi'        => '腾讯翻译', // qtv,qtk todo
    'qq_transmart'    => '腾讯交互',
    // google_translate, volcengine...
];
function_exists('curl_post') or include __DIR__ . '/../../lib/functions.php';

if ($engine = array_search(mb_substr($text, 0, 4), $engineNames)) {
    $_SESSION['trans_engine'] = $engine;
    $text = ltrim(str_replace($engineNames[$engine], '', $text), '?？,，.。 ');
} else {
    $engine = $_SESSION['trans_engine'] ?? 'qq_transmart';
}
$request = 'caiyun_fanyi';//$engine; // 'baidu_fanyi';
$botName = $engineNames[$engine];
$cacheFile = __DIR__ . '/cache-' . $engine . '-trans.json';

$keyword = $text;
if (!$keyword) return '你要抓啊子？';

$result = include 'request.php';
print_r($result);
