<?php

/**
 * fix 百度安全验证: 不用https和cookie认证 （请求几次又不行了！）
 * 问题：复制浏览器的curl,所有headers按道理都是一样的，在命令行执行和php-curl都会遇到 百度安全验证，不管有没有用cookie；但在浏览器里或phpstorm里http请求是ok的
 * 网页版：800kb
 * 手机版：2.3mb
 * 手机版精简版：80kb（禁用javascript）
 */

$curlBash = <<<'CURL'
curl 'http://www.baidu.com/s?wd=%s' \
  -H 'Host: www.baidu.com' \
  -H 'Connection: keep-alive' \
  -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.94 Safari/537.36' \
  -H 'Referer: http://www.baidu.com' \
CURL;

return [
    'curl' => $curlBash,
    'prepare' => function (&$setting, $text) {
        $setting['url'] = str_replace('%s', urlencode($text), $setting['url']);
    },
    'callback' => function ($content) {
        // return $content;
        $result = [];
        $t = str_replace("\n\n", "", strip_tags($content));
        foreach (explode("\n", $t) as $i => $line) {
            $line = trim($line);
            // 第65行才是搜索内容
            if ($i > 65 && (substr($line, -3) === '...' || strpos($line, '，'))&& mb_strlen($line) > 35) {
                // 更多后面有个特殊字符，可能是css样式字符
                $line = str_replace(['&#xe66a展开播放', "更多\u{e734}"], '', $line);
                $result[] = preg_replace('#^\d+年\d+月\d+日 *#', '', $line);
            }
        }

        return current($result);
    },
];
