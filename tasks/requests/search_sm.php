<?php

/**
 * @todo fix 校验
 */

$curlBash = <<<'CURL'
curl 'https://quark.sm.cn/s?q=%s' \
  -H 'authority: quark.sm.cn' \
  -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'cache-control: no-cache' \
  -H 'pragma: no-cache' \
  -H 'sec-ch-ua: "Chromium";v="116", "Not)A;Brand";v="24", "Microsoft Edge";v="116"' \
  -H 'sec-ch-ua-mobile: ?1' \
  -H 'sec-ch-ua-platform: "Android"' \
  -H 'sec-fetch-dest: document' \
  -H 'sec-fetch-mode: navigate' \
  -H 'sec-fetch-site: none' \
  -H 'sec-fetch-user: ?1' \
  -H 'upgrade-insecure-requests: 1' \
  -H 'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36 Edg/116.0.1938.76' \
  --compressed
CURL;

return [
    'curl' => $curlBash,
    'prepare' => function (&$setting, $text) {
        $setting['url'] = str_replace('%s', urlencode($text), $setting['url']);
    },
    'callback' => function ($content) {
        $content = str_replace("</a></div>", "</a></div>\n", $content);
        $text = strip_tags($content);
        $a = $b = [];
        foreach (explode("\n", $text) as $line) {
            $line = trim($line);
            if (!$line) continue;
            if (strpos($line, ',') || strpos($line, '，') || strpos($line, '、')) {
                $a[] = $line;
            } else {
                $b[] = $line;
            }
        }
        unset($a[0]); // head
        $result = &$a;

        return $result ? $result[array_rand($result)] : null;
    },
];
