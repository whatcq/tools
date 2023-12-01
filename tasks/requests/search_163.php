<?php


$curlBash = <<<'CURL'
curl 'https://www.163.com/search?keyword=%s' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Cookie: _ntes_nnid=0a30fb591a6974ddad21cc62bf5e8628,1630073203221; _ntes_nuid=0a30fb591a6974ddad21cc62bf5e8628; WM_TID=bAhxPvibAZtEERURABJr4T%2BWkggdfZM4; NTES_CMT_USER_INFO=12590643%7Cgdaymate%7Chttp%3A%2F%2Fcms-bucket.nosdn.127.net%2F2018%2F08%2F13%2F078ea9f65d954410b62a52ac773875a1.jpeg%7Cfalse%7CZ2RheW1hdGVAMTI2LmNvbQ%3D%3D; vinfo_n_f_l_n3=00f65d3a79a300df.1.7.1640609726436.1670765848162.1696517025721; WM_NI=IbJ8loQFCwL44MwH49JoJu3dcGgmCRczJAfJs0mLEDeXvLNhA%2Bt5kk68B0nkB97dKlAvc3SlbtMAw%2B0Fu89aA%2BTMNaK7nQ6h9ASu4Zr6YznwRr8P5NuwfnzGfFqtqz1wNmw%3D; WM_NIKE=9ca17ae2e6ffcda170e2e6ee89d53f9b908c8ad87fa2bc8eb7d14f979e8f87d163afb60084fc218a8f878ac72af0fea7c3b92a8bbe86d9ef65939a8d98cf40f893b9a9b7338ab18696b270a199a98ec25aa1a686a2e542b1f0a3adc850a788baadcd6483f5bcd6ea61f6e88ed1ed6182e7f985ca6883aaadd4d268a2ac83a2f621f49abbd9bc6b83f59caecd6b89e9a2b2b564fb9f9d96b752b2ea979bed7d92aef8b1b846b7a9aa8ff94da69abf95f472f3a9828ef637e2a3; P_INFO=gdaymate@126.com|1700489653|0|mail126|11&20|chq&1700106356&mail126#chq&null#10#0#0|&0|mail126|gdaymate@126.com; _ntes_origin_from=; _antanalysis_s_id=1701432694979; W_HPTEXTLINK=old' \
  -H 'Pragma: no-cache' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: none' \
  -H 'Sec-Fetch-User: ?1' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36 Edg/116.0.1938.76' \
  -H 'sec-ch-ua: "Chromium";v="116", "Not)A;Brand";v="24", "Microsoft Edge";v="116"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Windows"' \
  --compressed
CURL;

return [
    'curl' => $curlBash,
    'prepare' => function (&$setting, $text) {
        $setting['url'] = str_replace('%s', urlencode($text), $setting['url']);
    },
    'callback' => function ($content) {
        preg_match_all('#<h3><a [\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

        $result = $matches[0];
        array_walk($result, function (&$item) {$item = trim(strip_tags($item));});

        return $result ? $result[array_rand($result)] : null;
    },
];
