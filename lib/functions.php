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
