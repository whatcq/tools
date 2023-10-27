<?php

include 'include.php';

// 读取剪贴板的命令
$content = `powershell -command "Get-Clipboard"`;

// 编码转换
$encodings = array("ascii", "utf-8", "gb2312", "gbk", "big5");
$encoding = mb_detect_encoding($content, $encodings);
if ($encoding === 'gb2312') {
    $content = iconv('GB2312', 'UTF-8//IGNORE', $content);
}

// json format
if ($content[0] === '[' || $content[0] === '{') {
    $json = json_decode($content, true);
    if ($json) {
        echo json_encode($json, 448);
        die;
    }
}

// @todo 通过正则表达式，字符串分类器

// 整齐数据切分
// 按行/空格/分号切分
$pis = explode("\n", $content);
if (2 < $n = count($pis)) {
    // 大多数行都差不多长，就算整齐数据
    $avgLength = strlen(strtr($content, ["\r" => '', "\n" => ''])) / $n;
    $min = $avgLength * 0.9;
    $max = $avgLength * 1.1;
    $count = 0;
    foreach ($pis as &$line) {
        $line = trim($line);
        $l = strlen($line);
        if ($l > $min && $l < $max) {
            $count++;
        }
    }
    if ($count / $n > 0.7) {
        // 拼成单引号连接
        echo implode(", ", array_map(fn($item) => "'$item'", $pis));
        die;
    }
}

echo $content;
