<?php

/**
 * 读取配置，执行curl请求，返回结果
 * @var $request string
 * @var $text    string
 */

$file = __DIR__ . "/../requests/$request.php";
isset($cacheFile) or $cacheFile = $file . '.json';
is_file($file) or die("$file not exists!");

// 文件配置从chrome复制出来的curl信息，以及预处理函数，结果处理函数
$setting = include $file;
$setting += parseCurl($setting['curl']); // 从curl中解析出header,data-body
print_r($setting);

// 预处理
isset($setting['prepare']) && $setting['prepare']($setting, $text);
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

file_put_contents($cacheFile, $result);

// 结果处理
return $setting['callback']($result);

function parseCurl($str)
{
    $items = explode("\n", $str);
    $url = substr(trim(array_shift($items)), 6, -3);
    $headers = [];
    $data = '';
    foreach ($items as $item) {
        $item = trim($item);
        if (false !== $p = strpos($item, "'")) {
            $value = substr($item, $p + 1, -3);
            switch (substr($item, 0, $p)) {
            case '-H ':
                // list($k, $v) = explode(': ', $value);
                // $headers[$k] = $v;
                $headers[] = $value;
                break;
            case '--data-raw ':
                $data = $value;
            }
        }
    }

    return compact('url', 'headers', 'data');
}
