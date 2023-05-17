<?php

$curlBash = <<<'CURL'
curl 'https://translate.sogou.com/api/transpc/text/result' \
  -H 'Accept: application/json, text/plain, */*' \
  -H 'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/json;charset=UTF-8' \
  -H 'Cookie: SUID=DCF20A6F3622910A0000000061A88317; SUV=1638761904990009; ssuid=3891410566; LSTMV=317%2C185; LCLKINT=10926; IPLOC=CN5000; ABTEST=0|1673504477|v17; wuid=1673504477487; translate.sess=2798b8de-229f-4602-b516-a33c93cd05b9; SGINPUT_UPSCREEN=1673504479096; SNUID=664BB3D6B9BC48C800E73491BA07823E; FQV=b6f42fd8858a32d65fa103202610f005' \
  -H 'Origin: https://translate.sogou.com' \
  -H 'Referer: https://translate.sogou.com/text?keyword=&transfrom=auto&transto=zh-CHS&model=general' \
  -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.54' \
  --data-raw '{"from":"auto","to":"zh-CHS","text":"Singapore, city-state located at the southern tip of the Malay Peninsula, about 85 miles (137 kilometres) north of the Equator. \nIt consists of the diamond-shaped Singapore Island and some 60 small islets;\n","client":"pc","fr":"browser_pc","needQc":1,"s":"80fb7eccbf2101cf4dabd1ab419cb502","uuid":"da96140c-f50b-4f1f-bfa7-4e6cbc4ec5d9","exchange":false}' \
  --compressed
CURL;

return [
    'curl'     => $curlBash,
    'prepare'  => function (&$setting, $text) {
        $requestData = json_decode($setting['data'], 1);
        $requestData['text'] = $text;
        $requestData['s'] = md5($requestData['from'] . $requestData['to'] . $requestData['text'] . '109984457');

        $setting['data'] = json_encode($requestData);
    },
    'callback' => function ($result) {
        return json_decode($result, 1);
    },
];
