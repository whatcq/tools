<?php

/**
 * 读取配置，执行curl请求，返回结果
 * @var $request string
 * @var $text    string
 */

$file = __DIR__ . "/../requests/$request.php";
isset($cacheFile) or $cacheFile = $file . '.json';
is_file($file) or die("$file not exists!");

function_exists('parseCurl') or include __DIR__ . '/../../lib/functions.php';

// 文件配置从chrome复制出来的curl信息，以及预处理函数，结果处理函数
$setting = include $file;
$setting += parseCurl($setting['curl']); // 从curl中解析出header,data-body

// 预处理
isset($setting['prepare']) && $setting['prepare']($setting, $text);
// print_r($setting);
if ($setting['data']) {
    $result = curl_post(
        $setting['url'],
        $setting['data'],
        $setting['headers'], // array_map(fn($k, $v) => "$k: $v", $setting['headers']),
        5,
        1
    );
} else {
    $result = curl_get(
        $setting['url'],
        $setting['headers'],
    );
}

$content = clearHtml($result);
file_put_contents($cacheFile, $result);

// 结果处理
return $setting['callback']($content);

