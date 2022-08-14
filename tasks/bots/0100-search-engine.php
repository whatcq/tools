<?php

$botName = '搜';

require_once __DIR__ . '/../../lib/functions.php';

$urls = [
    'sm' => [
        'url' => 'https://quark.sm.cn/s?q=%s&from=smor&safe=1&snum=0',
        'fn' => function ($content) {
            // echo htmlspecialchars(substr($content, 500, 100));
            preg_match_all('#<h3>[\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

            $result = $matches[0];
            array_walk($result, fn (&$item) => $item = trim(strip_tags($item)));

            return $result ? $result[array_rand($result)] : 'no result';
        }
    ],
    '163' => [
        'url' => 'https://www.163.com/search?keyword=%s', //easy!
        'fn' => function ($content) {
            preg_match_all('#<h3>[\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

            $result = $matches[0];
            array_walk($result, fn (&$item) => $item = trim(strip_tags($item)));

            return $result ? $result[array_rand($result)] : 'no result';
        }
    ],
    'baidu' => [
        'url' => 'https://www.baidu.com/s?wd=%s&rsv_spt=1&rsv_iqid=0xd73f57d5000128b0&issp=1&f=3&rsv_bp=1&rsv_idx=2&ie=utf-8&tn=baiduhome_pg&rsv_enter=1&rsv_dl=ih_0&rsv_sug3=2&rsv_sug1=2&rsv_sug7=001&rsv_sug2=1&rsv_btype=i&rsp=0&rsv_sug9=es_2_1&rsv_sug4=1243448&rsv_sug=9',
        'header' => <<<HEADERS
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip
Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
Cache-Control: max-age=0
Connection: keep-alive
Cookie: __yjs_duid=1_83e6ae58b6e94e6d56769fdd27d2126f1628602311886; BIDUPSID=A2BD8EB1EDDCEB7B9621BF7EC46E0381; PSTM=1630207647; BAIDUID=A2BD8EB1EDDCEB7B9621BF7EC46E0381:SL=0:NR=10:FG=1; MCITY=-%3A; newlogin=1; BD_UPN=12314753; ZFY=b17m0uQBlpj6eqq:AgdqU2Aq5Pit8EptNKl0M4VxytJo:C; BAIDUID_BFESS=A2BD8EB1EDDCEB7B9621BF7EC46E0381:SL=0:NR=10:FG=1; B64_BOT=1; baikeVisitId=c0faf4d7-0bcf-4d90-9ac3-c3548f1bdc12; COOKIE_SESSION=833017_0_9_7_12_7_1_0_9_5_0_1_6041440_0_0_0_1659139385_0_1659969714%7C9%230_0_1659969714%7C1; H_WISE_SIDS=107318_110085_131861_179350_180636_188745_194520_194529_196426_204902_206123_208721_209568_209630_210321_211435_211986_212295_212797_212869_213037_213359_214803_215730_215957_216207_216840_216883_216943_218234_218359_218445_218453_218548_218593_218598_218619_219452_219667_219743_219862_219942_219946_220014_220072_220344_220384_220392_220663_220800_220866_221006_221016_221119_221121_221371_221409_221502_221642_221698_221795_221825_221904_221923_221963_221973_222254_222285_222336_222353_222389_222396_222417_222522_222541_222616_222618_222619_222625_222663_222779_222791_222882_222956_223343_223765_223776_223833_223891_224048; H_WISE_SIDS_BFESS=107318_110085_131861_179350_180636_188745_194520_194529_196426_204902_206123_208721_209568_209630_210321_211435_211986_212295_212797_212869_213037_213359_214803_215730_215957_216207_216840_216883_216943_218234_218359_218445_218453_218548_218593_218598_218619_219452_219667_219743_219862_219942_219946_220014_220072_220344_220384_220392_220663_220800_220866_221006_221016_221119_221121_221371_221409_221502_221642_221698_221795_221825_221904_221923_221963_221973_222254_222285_222336_222353_222389_222396_222417_222522_222541_222616_222618_222619_222625_222663_222779_222791_222882_222956_223343_223765_223776_223833_223891_224048; plus_lsv=e9e1d7eaf5c62da9; plus_cv=1::m:7.94e+147; Hm_lvt_12423ecbc0e2ca965d84259063d35238=1659995707; ai-studio-ticket=1CEC52D14A6047E79839C9A3F00E42F654F66BE4D08B4565A328704ACC0B74B1; jsdk-uuid=4c130d4b-2d46-4f40-8972-944c15c86ad7; BDUSS=RaSDNETXREWXhXRGxkQ0dEZS1Hb2tPUXZpUWRQcEZLYmdQdjg4YWliTUNVaHRqRUFBQUFBJCQAAAAAAAAAAAEAAADCsxsAY3FpdQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAALF82ICxfNiND; BDUSS_BFESS=RaSDNETXREWXhXRGxkQ0dEZS1Hb2tPUXZpUWRQcEZLYmdQdjg4YWliTUNVaHRqRUFBQUFBJCQAAAAAAAAAAAEAAADCsxsAY3FpdQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAALF82ICxfNiND; BD_HOME=1; H_PS_PSSID=36556_36753_36724_36413_36955_36950_36167_36918_36919_37129_37137_26350_36937; sug=3; sugstore=1; ORIGIN=0; bdime=0; BA_HECTOR=ak040l010124a00h2h26t8o71hf7i8l17; BDRCVFR[feWj1Vr5u3D]=I67x6TjHwwYf0; delPer=0; BD_CK_SAM=1; PSINO=6; H_PS_645EC=7708cz6ERTybRYJ0Ey0Zq8dkiOgHrTUvleWlCzL37bsEpssq9YY9S2D%2B%2BPMiipoiM3pr; BDSVRTM=278
Host: www.baidu.com
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.47
HEADERS,
        'fn' => function ($content) {
            $content = substr($content, strpos($content, '<div id="content_left"'));
            $content = ltrim(strip_tags($content));
            return substr($content, 0, strpos($content, "\n"));
        }
    ],
    'sogou' => [
        'url' => 'https://www.sogou.com/web?query=%s',
        'header' => <<<HEADERS
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip
Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
Cache-Control: max-age=0
Connection: keep-alive
Cookie: SUID=626C55773322910A00000000618760E8; SUV=1636262121148199; SMYUV=1636262249981327; ssuid=3715243536; usid=056A5577ED18A00A0000000061AC1073; SGINPUT_UPSCREEN=1650155291059; sg_client_ip=119.85.107.228; IPLOC=CN5000; ABTEST=6|1658019236|v17; browerV=3; osV=1; SNUID=B91B240F7177976655FDDA727271B45A; ld=Vyllllllll2AZCEulllllpacyDylllllNCqouZllll9lllllRklll5@@@@@@@@@@; sst0=13
Host: www.sogou.com
Referer: https://www.sogou.com/
sec-ch-ua: "Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Windows"
Sec-Fetch-Dest: document
Sec-Fetch-Mode: navigate
Sec-Fetch-Site: same-origin
Sec-Fetch-User: ?1
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.54
HEADERS,
        'fn' => function ($content) {
            // $text = preg_replace('#^[ \t\r]*\n#im', '', strip_tags($content));
            $text = strip_tags($content);

            $a = $b = [];
            foreach (explode("\n", $text) as $line) {
                $line = trim($line);
                if (!$line) continue;
                if (strpos($line, ',') || strpos($line, '、')) {
                    $a[] = $line;
                } else {
                    $b[] = $line;
                }
            }
            unset($a[0]); // 搜狗已为您找到约178,351条相关结果
            $result = &$a;

            return $result ? $result[array_rand($result)] : 'no result';
        }
    ],
    'zhidao' => [
        'url' => 'https://zhidao.baidu.com/search?lm=0&rn=10&pn=0&fr=search&word=%s',
        'header' => <<<HEADERS
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
Cache-Control: max-age=0
Connection: keep-alive
Cookie: __yjs_duid=1_83e6ae58b6e94e6d56769fdd27d2126f1628602311886; BIDUPSID=A2BD8EB1EDDCEB7B9621BF7EC46E0381; PSTM=1630207647; BAIDUID=A2BD8EB1EDDCEB7B9621BF7EC46E0381:SL=0:NR=10:FG=1; MCITY=-%3A; newlogin=1; ZFY=b17m0uQBlpj6eqq:AgdqU2Aq5Pit8EptNKl0M4VxytJo:C; BAIDUID_BFESS=A2BD8EB1EDDCEB7B9621BF7EC46E0381:SL=0:NR=10:FG=1; Hm_lvt_6859ce5aaf00fb00387e6434e4fcc925=1658547232,1659016134,1659146572,1659839063; H_WISE_SIDS=107318_110085_131861_179350_180636_188745_194520_194529_196426_204902_206123_208721_209568_209630_210321_211435_211986_212295_212797_212869_213037_213359_214803_215730_215957_216207_216840_216883_216943_218234_218359_218445_218453_218548_218593_218598_218619_219452_219667_219743_219862_219942_219946_220014_220072_220344_220384_220392_220663_220800_220866_221006_221016_221119_221121_221371_221409_221502_221642_221698_221795_221825_221904_221923_221963_221973_222254_222285_222336_222353_222389_222396_222417_222522_222541_222616_222618_222619_222625_222663_222779_222791_222882_222956_223343_223765_223776_223833_223891_224048; H_WISE_SIDS_BFESS=107318_110085_131861_179350_180636_188745_194520_194529_196426_204902_206123_208721_209568_209630_210321_211435_211986_212295_212797_212869_213037_213359_214803_215730_215957_216207_216840_216883_216943_218234_218359_218445_218453_218548_218593_218598_218619_219452_219667_219743_219862_219942_219946_220014_220072_220344_220384_220392_220663_220800_220866_221006_221016_221119_221121_221371_221409_221502_221642_221698_221795_221825_221904_221923_221963_221973_222254_222285_222336_222353_222389_222396_222417_222522_222541_222616_222618_222619_222625_222663_222779_222791_222882_222956_223343_223765_223776_223833_223891_224048; jsdk-uuid=4c130d4b-2d46-4f40-8972-944c15c86ad7; RT="z=1&dm=baidu.com&si=40itqqhyiy9&ss=l6p329d9&sl=a&tt=6v1&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ld=8t9g&ul=9un4&hd=9up6"; delPer=0; PSINO=7; H_PS_PSSID=36556_36753_36413_36955_36950_36918_36919_36570_37070_37129_37137_37055_26350; BA_HECTOR=a0al242104a5ah25a4agjri31hfdmiu16; ZD_ENTRY=empty; session_id=16603460101525082606392407869; Hm_lpvt_6859ce5aaf00fb00387e6434e4fcc925=1660346010
Host: zhidao.baidu.com
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.47
HEADERS,
        'fn' => function ($content) {

            // @todo decomporess
            // echo htmlspecialchars(substr($content, 500, 100));
            preg_match_all('#<h3>[\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

            $result = $matches[0];
            array_walk($result, fn (&$item) => $item = trim(strip_tags($item)));

            return $result; // ? $result[array_rand($result)] : 'no result';
        }
    ],
];

$botName = $engine = 'sogou';
$cacheFile = __DIR__ . '/cache-' . $engine . '.html';

$keyword = $text; // = '今天中午吃什么？' = '重庆天气'; //无住生心

// /*
$content = curl_get(
    sprintf($urls[$engine]['url'], urlencode($keyword)),
    header2array($urls[$engine]['header'] ?? '')
);
function clearHtml($content)
{
    return preg_replace(['#<style[\s\S\r]*?</style>#i', '#<script[\s\S\r]*?</script>#i', '#^[ \t\r]*\n#im'], '', $content);
}
in_array($engine, ['baidu', 'sogou']) && $content = clearHtml(gzdecode($content));
file_put_contents($cacheFile, $content);

/*/
$content = file_get_contents($cacheFile);
//*/


// echo '<pre>', print_r($urls[$engine]['fn']($content));
return $urls[$engine]['fn']($content);
