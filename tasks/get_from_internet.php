<?php

require_once __DIR__ . '/../lib/functions.php';


$urls = [
    'sm' => 'https://quark.sm.cn/s?q=%s&from=smor&safe=1&snum=0',
    '163' => [
        'url' => 'https://www.163.com/search?keyword=%s', //easy!
        'fn' => function ($content) {
            // echo htmlspecialchars(substr($content, 500, 100));
            preg_match_all('#<h3>[\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

            $result = $matches[0];
            array_walk($result, fn (&$item) => $item = trim(strip_tags($item)));

            return $result ? $result[array_rand($result)] : 'no result';
        }
    ],
    'sogou' => [
        'url' => 'https://www.sogou.com/web?query=%s&_asf=www.sogou.com&_ast=&w=01015002&p=40040108&ie=utf8&from=index-nologin&s_from=index&oq=&ri=0&sourceid=sugg&suguuid=&sut=0&sst0=1659998852417&lkt=0%2C0%2C0&sugsuv=1636262121148199&sugtime=1659998852417',
        'header' => <<<HEADERS
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate, br
Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
Cache-Control: max-age=0
Connection: keep-alive
Cookie: SUID=626C55773322910A00000000618760E8; SUV=1636262121148199; SMYUV=1636262249981327; ssuid=3715243536; usid=056A5577ED18A00A0000000061AC1073; SGINPUT_UPSCREEN=1650155291059; sg_client_ip=119.85.107.228; IPLOC=CN5000; ABTEST=6|1658019236|v17; browerV=3; osV=1; ariaDefaultTheme=undefined; SNUID=77B38CA7DBDE3F18427EE9EADBC54C01; sst0=417; ld=qkllllllll2AZCEulllllpaeu4clllllNCqouZllllwllllljllll5@@@@@@@@@@; LSTMV=922%2C232; LCLKINT=1645
Host: www.sogou.com
Referer: https://www.sogou.com/
Sec-Fetch-Dest: document
Sec-Fetch-Mode: navigate
Sec-Fetch-Site: same-origin
Sec-Fetch-User: ?1
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.47
sec-ch-ua: "Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Windows"
HEADERS,
        'fn' => function ($content) {
            // echo htmlspecialchars(substr($content, 500, 100));
            preg_match_all('#<h3>[\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

            $result = $matches[0];
            array_walk($result, fn (&$item) => $item = trim(strip_tags($item)));

            return $result ? $result[array_rand($result)] : 'no result';
        }
    ],
];

$engine = '163';
$cacheFile = 'cache-' . $engine . '.html';

$keyword = $text; //'重庆天气';//无住生心

// /*
$content = curl_get(
    sprintf($urls[$engine]['url'], urlencode($keyword)),
    header2array($urls[$engine]['header'] ?? '')
);
file_put_contents($cacheFile, $content);

//*/ $content = file_get_contents($cacheFile);

// echo '<pre>', print_r($urls[$engine]['fn']($content));
$responseText = $urls[$engine]['fn']($content);

die('<script>parent.response("' . $engine . '", "' . addslashes($responseText) . '")</script>');
