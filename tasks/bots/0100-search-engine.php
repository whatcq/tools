<?php

$botName = '搜';

require_once __DIR__ . '/../../lib/functions.php';

$urls = [
    'sm' => [
        'url' => 'https://quark.sm.cn/s?q=%s&from=smor&safe=1&by=submit&snum=0',
        'header' => <<<HEADERS
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
accept-encoding: gzip
accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
cookie: sm_diu=94463918377ae9dbcd01540306f8101f%7C%7C11eef1ee73e005ee0f%7C1659966719; sm_uuid=66fa6a973a09f00e6f24e12befb29f69%7C%7C%7C1659966787; __itrace_wid=8a245aae-8297-4e15-a368-ae00d1d6cbfc; sm_ruid=f69c2a151e5e575806ed3af33baceaf0%7C%7C%7C1659970745; lsmap2=1a03M14U07S1CE1Nn0Ps0Tx0Wk0Yw0900; isg=BDQ0YRXX6pZgiH5aa_gQO176BfKmDVj39HPGYc6UD7_nOdaD9B3qhzqwuTFEwZBP
sec-ch-ua: "Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Windows"
sec-fetch-dest: document
sec-fetch-mode: navigate
sec-fetch-site: none
sec-fetch-user: ?1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.54
HEADERS,
        'fn' => function ($content) {
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
        }
    ],
    '163' => [
        'url' => 'https://www.163.com/search?keyword=%s', //easy!
        'fn' => function ($content) {
            preg_match_all('#<h3>[\s\S\n]*?</h3>#im', $content, $matches, PREG_PATTERN_ORDER, 30000);

            $result = $matches[0];
            array_walk($result, fn (&$item) => $item = trim(strip_tags($item)));

            return $result ? $result[array_rand($result)] : null;
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
            unset($a[0]); // 搜狗已为您找到约178,351条相关结果
            $result = &$a;

            return $result ? $result[array_rand($result)] : null;
        }
    ],
    'bing' => [
        'url' => 'https://cn.bing.com/search?q=%s',
        'header' => <<<HEADERS
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
accept-encoding: gzip
accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
cookie: _EDGE_V=1; MUID=3F7FD7CCFB276E3321A3C743FA096FDE; MUIDB=3F7FD7CCFB276E3321A3C743FA096FDE; SRCHD=AF=ANAB01; SRCHUID=V=2&GUID=1377E3EAF0854759B1D31477B4851C6C&dmnchg=1; MUIDV=NU=1; PPLState=1; ANON=A=C03F609D66AE24C23B4D0D53FFFFFFFF&E=1a0a&W=1; KievRPSSecAuth=FABaBBRaTOJILtFsMkpLVWSG6AN6C/svRwNmAAAEgAAACNZrOOqn7K3MGATnbpJekwteSA2ZivzyxsZzPiZWSLVEgRp1IagJO+A1GeV8RdRDkGD0hfYol8ur0IyBrUOc0gBpFjX/K5TtKXrGWzHsBmd3qyCMly0ytYt8MSFO22hZ4CAWtVHEVuHy4BO1egPkAmbl067hoOJrHxoioEL/YPQQ7Fre32tOnqDZvbw3R9lUrH4zZl1SzuK/ZOTm+iZ9kYd4C7OefN+QbsWPDJtbMBlgMEIAclNEgmWugyMP96pp0ofKlIsFwEQ2GwpWVfqTLfYiZc/OT6k37lNqdyAuBLyUEeyzCCsuZMDbepNvVKlz+iBxarWPLU9c+abLP6RHwIzJBD8PiVbPHo5XluMIzoA4VrxKxREbmgNzLfj+rsST7ZqKyOyPE+7I6PtzdsFVu7xyVLiQ70HM8o4u1buQAHu27I9gj2ms2+8w0HFijHR5wbb7nF2t3v7e50GzaqGzBtXWKf9ftqvCLvW2+nnmjf58Y867LnVXjYFPTRmynU0JdWd0MruWFebIgzweMtPyu51X3sHUSsjFhKB6zNnzwOkPuGZsXxFeZ4RCBbRLTyj5mr+RAjrk9DnIZEPKHo7UDnQkVI6eDUEavrmKdTzCT2X+zzEKtJHUCVetNxPFuWL2DtDoz+KQYUlZnxELCgG2P1FAYjIANOD/T7VxHerJ48SwN3h7mNEQTKn4cyp0cNf1d8vigHvTZQxQagd4IzAgjKQDPI7KYjlMQlKD3Ka70P4kzjPyb8j5Lub1s2dtCkKOxVEGjPhedC0vdgsr6lahyWbNfUeJlknZ9lXsdd7lCoGiAJprGSqRhXSS111ihxUB/FY3fbsazYGrq0oxJJjYIGCSNuhEMie7Jf+TRZ62zM9zM0jwUfLECvbMQbViM2CIkzDJkF4cO7mgJZQSzS3c0AN3VhVM/FnPiz7uXS93Ivih3t9BqSV4qvrY4efvOc5mLI8dg8+/6vfJKzevZxcAfxKuo5d5uLlw6updw01BT9YhXfKX/WnHz6SIA7kPYIAI3DP5IotHdCyhqoR26l4Szse9RyACY/H5I5Jmaqf4Vxtk3HFNvBsHGfrkI9uPp1NfhTiO9dJYLUxMbu3055tticwmJphpIWDrFHbE3ILattjxvyMYCy9DDJEoPHuUiI7eFPQ8VTUCzT66/wwWBb5GpR2qcs+Cay+3rqBhPtXMjvsZrSAx5bMsAsDfQY2P9YBQd73I5ffEKr6iJIsXT19hD+ol+tMbopbVCrze1Hjs/1ZSNjsy4s1YogKRClXQELwEBR9yrc23p93uPdDiZXJsWrTme2Uy7pnsubK5doLfrbQgzVNmxEt7gJcLnY0PTksUh4LE24Vbz3RoAiLwJTuredm0wxcV/8R8e0MZGZQ2zG1qDETPydK9JKt2V0bk1UR2a5iLFADgsk/J6CIyZlNQ3YeEAg4KFqNIng==; _RwBf=ilt=3&ihpd=0&ispd=2&rc=0&rb=0&gb=0&rg=0&pc=0&mtu=0&rbb=0&g=0&cid=&v=3&l=2022-01-07T08:00:00.0000000Z&lft=00010101&aof=0&o=2&p=&c=&t=0&s=0001-01-01T00:00:00.0000000+00:00&ts=2022-01-08T00:46:46.4055336+00:00&rwred=0; imgv=lodlg=1&gts=20210810&flts=20220716; _ITAB=STAB=TR; _UR=QS=0&TQS=0; _tarLang=default=zh-Hans; _TTSS_IN=hist=WyJlcyIsInpoLUhhbnMiLCJlbiIsImF1dG8tZGV0ZWN0Il0=; _TTSS_OUT=hist=WyJlbiIsInpoLUhhbnMiXQ==; _EDGE_S=SID=0786DCCD9F8262CE24D1CD329E50632B; WLS=C=6b2b74ecb580fd2a&N=Alan; _SS=SID=0786DCCD9F8262CE24D1CD329E50632B; ZHCHATSTRONGATTRACT=TRUE; ZHCHATWEAKATTRACT=TRUE; SUID=A; _FP=hta=on; SRCHUSR=DOB=20210810&T=1660446588000&POEX=W; ipv6=hit=1660450191314&t=4; ENSEARCH=BENVER=0; USRLOC=HS=1&BLOCK=TS=220814031116; _U=1b4ahsYpiHRYpqgCw0BGlHnFnJnqq2GeFlWT0XAPfl4VlgaUVoPic-Zd4cqWTgcYxqT-YBHB5vk6bojkQpHLdbQYTqLjCyhlUWZ2n9uGdKuJz6_QARlBnrsd4WAGWZyp6zr7PwnQexSBm4qsaIYfRMfOJ_Eltnx-7WcVEZjG2YDtZ2o4NVQsC_R297KmMdugV2TcxL7VZ3tNJW69RIflhdg; SNRHOP=I=&TS=; _HPVN=CS=eyJQbiI6eyJDbiI6NiwiU3QiOjAsIlFzIjowLCJQcm9kIjoiUCJ9LCJTYyI6eyJDbiI6NiwiU3QiOjAsIlFzIjowLCJQcm9kIjoiSCJ9LCJReiI6eyJDbiI6NiwiU3QiOjAsIlFzIjowLCJQcm9kIjoiVCJ9LCJBcCI6dHJ1ZSwiTXV0ZSI6dHJ1ZSwiTGFkIjoiMjAyMi0wOC0xNFQwMDowMDowMFoiLCJJb3RkIjowLCJHd2IiOjAsIkRmdCI6bnVsbCwiTXZzIjowLCJGbHQiOjAsIkltcCI6MjR9; SRCHHPGUSR=SRCHLANG=zh-Hans&BZA=0&BRW=HTP&BRH=M&CW=974&CH=807&SW=1920&SH=1080&DPR=1.25&UTC=480&DM=0&EXLTT=8&HV=1660448770&PV=10.0.0
sec-ch-ua: "Chromium";v="104", " Not A;Brand";v="99", "Microsoft Edge";v="104"
sec-ch-ua-arch: "x86"
sec-ch-ua-bitness: "64"
sec-ch-ua-full-version: "104.0.1293.54"
sec-ch-ua-full-version-list: "Chromium";v="104.0.5112.81", " Not A;Brand";v="99.0.0.0", "Microsoft Edge";v="104.0.1293.54"
sec-ch-ua-mobile: ?0
sec-ch-ua-model: ""
sec-ch-ua-platform: "Windows"
sec-ch-ua-platform-version: "10.0.0"
sec-fetch-dest: document
sec-fetch-mode: navigate
sec-fetch-site: none
sec-fetch-user: ?1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.81 Safari/537.36 Edg/104.0.1293.54
x-edge-shopping-flag: 1
HEADERS,
        'fn' => function ($content) {
            preg_match('#<ol id="b_results" class="">[\s\S\r]*?<p\b.*?>(.*?)</p>[\s\S\r]*?</li>#i', $content, $matches);
            return preg_replace(['/\d{4}\-\d{1,2}\-\d{1,2}/', '/&#?\w{4,5};/i'], '', strip_tags($matches[1]));
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
            $text = strip_tags($content);
            $a = $b = [];
            foreach (explode("\n", $text) as $line) {
                $line = trim($line);
                if (!$line) continue;
                if (strpos($line, '答：') === 0) {
                    $a[] = substr($line, strlen('答：'));
                } else {
                    $b[] = $line;
                }
            }
            unset($a[0]); // 搜狗已为您找到约178,351条相关结果
            $result = &$a;

            return $result ? $result[array_rand($result)] : null;
        }
    ],
];

$engineNames = [
    '163' => '网易同学',
    'zhidao' => '知道同学',
    'baidu' => '百度同学',
    'sogou' => '搜狗同学',
    'sm' => '神马同学',
    'bing' => '必应同学'
];
if ($engine = array_search(mb_substr($text, 0, 4), $engineNames)) {
    $text = ltrim(str_replace($engineNames[$engine], '', $text), ',，.。 ');
} else {
    $engine = 'bing';
}

$botName = $engine;
$cacheFile = __DIR__ . '/cache-' . $engine . '.html';

$keyword = $text; // = '今天中午吃什么？' = '重庆天气'; //无住生心
if (!$keyword) return '你要抓啊子？';

// /*
$content = curl_get(
    sprintf($urls[$engine]['url'], urlencode($keyword)),
    header2array($urls[$engine]['header'] ?? '')
);
in_array($engine, ['baidu', 'sogou', 'sm', 'bing']) && $content = gzdecode($content);
'zhidao' == $engine && $content = str_replace(
    '<meta http-equiv="content-type" content="text/html;charset=gb2312" />',
    '<meta http-equiv="content-type" content="text/html;charset=utf-8" />',
    iconv('GB2312', 'UTF-8//IGNORE', $content)
);

function clearHtml($content)
{
    return preg_replace([
        '#<style[\s\S\r]*?</style>#i',
        '#<script[\s\S\r]*?</script>#i',
        // '#<div style="display:none">[\s\S\r]*?</div>#im',
        '#^[ \t\r]*\n#im'
    ], '', $content);
}
$content = clearHtml($content);
file_put_contents($cacheFile, $content);

/*/
$content = file_get_contents($cacheFile);
//*/

$outHtml = '<style>ul{display: inline-flex;}</style>' . $content;

return $urls[$engine]['fn']($content);
