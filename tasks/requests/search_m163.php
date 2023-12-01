<?php

$cacheFile = __DIR__ . '/cache-m163.json';

$curlBash = <<<'CURL'
curl 'https://gw.m.163.com/nc/api/v1/pc-wap/search?query=%s&size=20&from=wap&needPcUrl=true' \
  -H 'authority: gw.m.163.com' \
  -H 'accept: */*' \
  -H 'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'cache-control: no-cache' \
  -H 'origin: https://m.163.com' \
  -H 'pragma: no-cache' \
  -H 'referer: https://m.163.com/' \
  -H 'sec-ch-ua: "Chromium";v="116", "Not)A;Brand";v="24", "Microsoft Edge";v="116"' \
  -H 'sec-ch-ua-mobile: ?1' \
  -H 'sec-ch-ua-platform: "Android"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: same-site' \
  -H 'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36 Edg/116.0.1938.76' \
  --compressed
CURL;

return [
    'curl' => $curlBash,
    'prepare' => function (&$setting, $text) {
        $setting['url'] = str_replace('%s', urlencode($text), $setting['url']);
    },
    'callback' => function ($content) {
        $json = json_decode($content, true);

        return strip_tags($json['data']['result'][0]['title'] ?? '没搜到');
    },
];
