<?php

/**
 * @todo fix 异常访问请求
 */

$curlBash = <<<'CURL'
curl 'https://sogou.com/web?query=%s' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Cookie: SUID=763F6A0E1F49910A000000006437F614; cuid=AAFnLjcxRAAAAAqHS1OXWQEANgg=; SUV=1681389075959163; ssuid=3688688195; usid=4D396A0E9F13A00A0000000064843CE4; SGINPUT_UPSCREEN=1693697034802; sw_uuid=8505765888; IPLOC=CN5000; ABTEST=3|1701435403|v17; SNUID=826334505E58552C134318C65EBFBFE9; sst0=564' \
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
        $result = [];
        $t = str_replace("\n\n", "", strip_tags($content));
        foreach (explode("\n", $t) as $i => $line) {
            $line = trim($line);
            if ($i > 4 && substr($line, -3) === '...') {
                $result[] = preg_replace('#^\d+年\d+月\d+日- *#', '', $line);
            }
        }

        return $result ? $result[array_rand($result)] : null;
    },
];
