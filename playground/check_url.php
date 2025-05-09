<?php
function checkUrl($url)
{
    // 初始化 cURL 会话
    $ch = curl_init($url);

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true); // 不获取响应体，只获取头部信息
    curl_setopt($ch, CURLOPT_HEADER, true); // 获取响应头
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向

    // 执行 cURL 会话
    curl_exec($ch);

    // 获取 HTTP 状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // 关闭 cURL 会话
    curl_close($ch);

    return $httpCode;
}


// 检查 URL 状态
$status = checkUrl($_REQUEST['url'] ?? '');
http_response_code($status);
