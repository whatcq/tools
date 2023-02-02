<?php

function curl_post($url, $postFields = [], $headers = [], $timeout = 20, $file = 0)
{
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_NOBODY => false,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    );
    if ($postFields && $file == 0) {
        $options[CURLOPT_POSTFIELDS] = http_build_query($postFields);
    } else {
        $options[CURLOPT_POSTFIELDS] = $postFields;
    }
    curl_setopt_array($ch, $options);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $returnData = curl_exec($ch);
    if (curl_errno($ch)) {
        $returnData = curl_error($ch);
    }
    curl_close($ch);
    return $returnData;
}

function curl_get($url, $headers = [], $timeout = 3)
{
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_NOBODY => false,
        CURLOPT_POST => false,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    );
    curl_setopt_array($ch, $options);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $returnData = curl_exec($ch);
    if (curl_errno($ch)) {
        $returnData = curl_error($ch);
    }
    curl_close($ch);
    return $returnData;
}

// 简单demo，默认支持为GET请求
function multiRequest($reqs)
{
    $mh = curl_multi_init();
    $urlHandlers = [];
    $responses = [];
    // 初始化多个请求句柄为一个
    foreach ($reqs as $req) {
        $ch = curl_init();
        $url = $req['url'];
        $url .= strpos($url, '?') ? '&' : '?';
        $params = $req['params'];
        $url .= is_array($params) ? http_build_query($params) : $params;
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置数据通过字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $urlHandlers[] = $ch;
        curl_multi_add_handle($mh, $ch);
    }
    $active = null;
    // 检测操作的初始状态是否OK，CURLM_CALL_MULTI_PERFORM为常量值-1
    do {
        // 返回的$active是活跃连接的数量，$mrc是返回值，正常为0，异常为-1
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    // 如果还有活动的请求，同时操作状态OK，CURLM_OK为常量值0
    while ($active && $mrc == CURLM_OK) {
        // 持续查询状态并不利于处理任务，每50ms检查一次，此时释放CPU，降低机器负载
        usleep(50000);
        // 如果批处理句柄OK，重复检查操作状态直至OK。select返回值异常时为-1，正常为1（因为只有1个批处理句柄）
        if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
    // 获取返回结果
    foreach ($urlHandlers as $index => $ch) {
        $responses[$index] = curl_multi_getcontent($ch);
        // 移除单个curl句柄
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);

    return $responses;
}

function header2array($headers)
{
    if (is_string($headers)) {
        $headers = explode("\n", str_replace("\r", '', rtrim($headers)));
        if (false !== $p = strpos($headers[0], "'")) {
            foreach ($headers as &$item) {
                $item = substr($item, $p + 1, -3);
            }
        }
    }

    return $headers;
}

// 向上兼容php8
// source: Laravel Framework
// https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/Str.php
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
