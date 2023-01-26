<?php

$curlBash = <<<'CURL'
curl 'https://transmart.qq.com/api/imt' \
  -H 'authority: transmart.qq.com' \
  -H 'accept: application/json, text/plain, */*' \
  -H 'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'content-type: application/json' \
  -H 'cookie: RK=ZYoBmaQvGj; ptcz=65c6c6281732e05a86f8fb0815be19e54c20ed84be19e9cc4b5f0e3354ed9ac5; pgv_pvid=9747039477; o_cookie=252965255; tvfe_boss_uuid=3d958a9c26eae810; pac_uid=1_252965255; iip=0; client_key=browser-edge-chromium-108.0.1462-Windows%2010-81127c84-d989-4a30-8600-e9556e8d11b2-1673940067146' \
  -H 'origin: https://transmart.qq.com' \
  -H 'referer: https://transmart.qq.com/zh-CN/index' \
  -H 'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Microsoft Edge";v="108"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Windows"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: same-origin' \
  -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.54' \
  --data-raw '{"header":{"fn":"auto_translation","client_key":"browser-edge-chromium-108.0.1462-Windows 10-81127c84-d989-4a30-8600-e9556e8d11b2-1673940067146"},"type":"plain","model_category":"normal","source":{"lang":"en","text_list":["","SplDoublyLinkedList::add — Add/insert a new value at the specified index",""]},"target":{"lang":"zh"}}' \
  --compressed
CURL;

return [
    'curl'     => $curlBash,
    'prepare'  => function (&$setting, $text) {
        $requestData = json_decode($setting['data'], 1);
        $requestData['source']['text_list'][1] = $text;

        $setting['data'] = json_encode($requestData);
    },
    'callback' => function ($result) {
        return json_decode($result, 1);
    },
];
