<?php

/**
 * 360翻译 缺点：没有换行，可以hack:strtr($text, "\n", '*');
 */
$curlBash = <<<'CURL'
curl 'https://fanyi.so.com/index/search?eng=1&validate=&ignore_trans=0&query=%s' \
  -X 'POST' \
  -H 'authority: fanyi.so.com' \
  -H 'accept: application/json, text/plain, */*' \
  -H 'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'content-length: 0' \
  -H 'cookie: QiHooGUID=B35FFC9859D866C4478D82E10BFDA720.1638346323635; so_huid=11jEFykWs0SrsCS0p2KKitH41RhbVzLLXS9kZG8llPsns%3D; __huid=11jEFykWs0SrsCS0p2KKitH41RhbVzLLXS9kZG8llPsns%3D; __guid=15484592.4065955259164763600.1674093406886.97; recext=4HfmOKNnOrCHDXCET9IE1Q.10b8.1674093407024; __asc=3b77ea3b20aab2c88133b39c7f4f15a4.10aa.1674093407301; __asc2=dce14470abe4effb7cf8b0a8d9befc8271f80b68bcb6.11c1.1674093407299; so_md=q06f1332167409340732829eeba151b98426cd60319a008.6; __md=oo6f13322fe7ba38f417649eeba151b98426cd60319a008.7; Q_UDID=f11c29c9-d05c-713c-f43b-0b62a2c12941; gtHuid=1; count=2' \
  -H 'origin: https://fanyi.so.com' \
  -H 'pro: fanyi' \
  -H 'referer: https://fanyi.so.com/' \
  -H 'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Microsoft Edge";v="108"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Windows"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: same-origin' \
  -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.54' \
  --compressed
CURL;

return [
    'curl'     => $curlBash,
    'prepare'  => function (&$setting, $text) {

        $setting['url'] = sprintf($setting['url'], urlencode($text));
    },
    'callback' => function ($result) {
        $data = json_decode($result, 1);
        if (empty($data['error']) && !empty($data['data']['fanyi'])) {
            return $data['data']['fanyi'];
        }

        return $data['msg'];
    },
];
