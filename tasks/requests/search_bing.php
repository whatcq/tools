<?php


$curlBash = <<<'CURL'
curl 'https://cn.bing.com/search?q=%s' \
  -H 'authority: cn.bing.com' \
  -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'avail-dictionary: UvrqQwhG' \
  -H 'cache-control: no-cache' \
  -H 'cookie: MUID=26D0F498CA9C65570FF2E676CBD6646C; MUIDB=26D0F498CA9C65570FF2E676CBD6646C; _EDGE_V=1; SRCHD=AF=NOFORM; SRCHUID=V=2&GUID=8749679C9EFF49CE88362AA49DB6CF1C&dmnchg=1; _UR=QS=0&TQS=0; MMCASM=ID=9DDD840F2776412CA2DEAD3B879C5ADA; BCP=AD=1&AL=1&SM=1; ANIMIA=FRE=1; ANON=A=C03F609D66AE24C23B4D0D53FFFFFFFF&E=1cb3&W=4; NAP=V=1.9&E=1c59&C=j8PhgPc_lHkqtS_MxMtIeLPz1nnCkI4w_djOXUMuJeJ-eI1Ii-ObkA&W=4; PPLState=1; KievRPSSecAuth=FABiBBRaTOJILtFsMkpLVWSG6AN6C/svRwNmAAAEgAAACCqhky16HvUYIARvOhHCfAP0LGsq8vo/56SyMg0FhFDrvPJ8RioTfEe+apLx7U6uIQHCAZ3CA2ygsmlLJJjmr5F4nsLaKhExyNLJVH0w0RKksF5C2lcTiz9bepoId8HZAnij7Sf0DfygtJNX7zALNt/AxCMI3LVvVfW4QdXmLOtggQbACnJlUv6SlXRolpU9neBuj3tRfwWMyPYGzmKoPG1ZUqyqBe8PFblb//l5wMYcvaquoj0wi+1KVoEEw4NWO8Ojtwlzkdhkq4gtoyKZXEFtMYA4gC5foFcXqoP9x8vBDLc0oKE1U3hxwABpyXmfw/DAG5SeMya8teuckcbfPuyryqRm3vk5Yllu48uG9YCKU7malKpzVOJ0ve7X0pp/xw6BPFaLFVbMlhAI0yhMdMSlzWf9AUgHaepfqisB+8S1BG9+A9QsgPtnFkTfe2mMpUdngBrz2/Gd2mCzEpPGpkezAFSPnCu8U11qwiFPI/AYvziudSU7s3NfShZDpLrupvCK3EbHvOa5kpimgOliNwIV7vUQfHCQ7gteV9rU+PM9JJKJcuCf4zMyc1Dr88IuiRuTiKJMa59NGaPPYRfhF2pf3oFdfoPAcoAAQZyF11+bdwpja+hEJdz5EEYJKdiNe65ljmEEbiMV7caHUQtUEsOFOrsHClTlKI/3MN5xauasjAW0m0kP16fQWltC4Jas6ZDpDCttPKhTw0n2v9uz40kes3g1pxf01DpTpskKJumOlF1OaAHBYWVdr16Op9jV+f7sl+Znqa4O/Ko0059pGYzgGjLbFTpgvD7AgwshUb2KEXt2R60NsxXEYGkxExShpALz1MREUjIuYi6Tn4pWhaVoEIpp2BG9q+9mepM7MOlrGaQ/hmT61ujFf6nHNnKXvj86m0xs4l0wbvzaLtKLtvUwKfJahUYNplxLwg0dp/Y8nrooBAtG6k0V/nweQnONbzvPYge1+qEVdanKCiBT9nktlHkpZgRBn6L2zin+nk8P+k4atcsiaq/Es2jNxDQR2JhUukD7koeAUnwOz6bsHtDMRj0piCYGrIZ9WVFncJzXbgkHGaVbwEwJwKmW4+ZpuwfxcQdwtTY89EQYsfeOEobww48a5jG2SRGPwNns7DQUkGQibVA44Q2PWXyS8y0Cipmjy++xwb+rdeuZqT3an2o41k1feYBZvwgR0XyHTspxl/aUDI4eRL/NTG0IE22B+Z+grFEEaLVhNPTBd9wV79pFRmQG5t3NkG9dS1voOjexYd7UNc3apf7yDg4qJW9le95g6eAMH0G6lO512OxyYwo4bRLRVaN7PxfMuBqVc45U8/E9X6BUGlLOaI0JHGQ+bg2j57xQgQ2LVGwyuD7g6FzcyGnSqmg897c886VTpK067xEr47uCKq6O4yCeL42QbsrwESHzBpyX0zEUAK6wkObEZNLkxfJ83QmtuygvtC5J; _tarLang=default=zh-Hans; _TTSS_IN=hist=WyJmciIsInpoLUhhbnMiLCJlbiIsImF1dG8tZGV0ZWN0Il0=&isADRU=0; _TTSS_OUT=hist=WyJlbiIsInpoLUhhbnMiXQ==; _HPVN=CS=eyJQbiI6eyJDbiI6MzQsIlN0IjoyLCJRcyI6MCwiUHJvZCI6IlAifSwiU2MiOnsiQ24iOjM0LCJTdCI6MCwiUXMiOjAsIlByb2QiOiJIIn0sIlF6Ijp7IkNuIjozNCwiU3QiOjAsIlFzIjowLCJQcm9kIjoiVCJ9LCJBcCI6dHJ1ZSwiTXV0ZSI6dHJ1ZSwiTGFkIjoiMjAyMy0xMC0xNFQwMDowMDowMFoiLCJJb3RkIjowLCJHd2IiOjAsIkRmdCI6bnVsbCwiTXZzIjowLCJGbHQiOjAsIkltcCI6OTF9; ABDEF=V=13&ABDV=13&MRB=0&MRNB=1699453006627; USRLOC=HS=1&ELOC=LAT=29.589685440063477|LON=106.2973403930664|N=%E6%B2%99%E5%9D%AA%E5%9D%9D%E5%8C%BA%EF%BC%8C%E9%87%8D%E5%BA%86%E5%B8%82|ELT=6|&BLOCK=TS=231130150920; _EDGE_S=SID=03EF60C55A9D6C612EE6731F5BFB6D07; SNRHOP=I=&TS=; WLS=C=6b2b74ecb580fd2a&N=Alan; _U=1SIyVDjmZFhQT4G3AAk9DNgG5oUpOHHnZFxxhFEe0ngUh_hseLWCm1CseHF7cxnVKhJg-PTUyxMkAx_oWADQGGx1-R6h8FtcCha9FNYz_sTY5QF6AEmcomj_pRK-PTMH-LWggJHFwf5OqoRBZ36tAQPWTJhXmLqQAFlsGOYErf5Gi5hxskBE1qR4hK3iBAp_4JzkxX2TheVSv9geqefd9ww; SRCHUSR=DOB=20230409&T=1701430801000&POEX=W; _RwBf=r=0&ilt=1&ihpd=0&ispd=0&rc=4265&rb=4265&gb=0&rg=0&pc=4265&mtu=0&rbb=0.0&g=0&cid=&clo=0&v=1&l=2023-12-01T08:00:00.0000000Z&lft=0001-01-01T00:00:00.0000000&aof=0&o=0&p=MULTIGENREWARDSCMACQ202205&c=ML2357&t=5066&s=2022-12-07T01:22:15.6335551+00:00&ts=2023-12-01T11:40:03.3058375+00:00&rwred=0&wls=0&wlb=0&lka=0&lkt=0&TH=&mta=0&dci=3&e=-sCSlVHr65U5WyPKpad7yteBbbTibkzWeMKo75aeZDFajN94GwxJQV3giIPZEk9fjiedNXgcMBdqCnKW1Yq6CzLPkmDtGa_MfpDnHrz4-mI&A=&aad=0; _Rwho=u=d; _SS=SID=03EF60C55A9D6C612EE6731F5BFB6D07&R=4265&RB=4265&GB=0&RG=0&RP=4265; ipv6=hit=1701434404093&t=4; SRCHHPGUSR=SRCHLANG=zh-Hans&PV=10.0.0&BRW=S&BRH=M&CW=1080&CH=805&SCW=1164&SCH=3005&PRVCW=738&PRVCH=805&DPR=1.3&UTC=480&DM=0&HV=1701430803&WTS=63827795423&BZA=0&EXLTT=31&PR=1.25&IG=0470BCBD988F445B9E866A141CAF1E7E&THEME=-1&VCW=1463&VCH=805&CIBV=1.1342.1' \
  -H 'pragma: no-cache' \
  -H 'sec-ch-ua: "Chromium";v="116", "Not)A;Brand";v="24", "Microsoft Edge";v="116"' \
  -H 'sec-ch-ua-arch: "x86"' \
  -H 'sec-ch-ua-bitness: "64"' \
  -H 'sec-ch-ua-full-version: "116.0.1938.76"' \
  -H 'sec-ch-ua-full-version-list: "Chromium";v="116.0.5845.180", "Not)A;Brand";v="24.0.0.0", "Microsoft Edge";v="116.0.1938.76"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-model: ""' \
  -H 'sec-ch-ua-platform: "Windows"' \
  -H 'sec-ch-ua-platform-version: "10.0.0"' \
  -H 'sec-fetch-dest: document' \
  -H 'sec-fetch-mode: navigate' \
  -H 'sec-fetch-site: none' \
  -H 'sec-fetch-user: ?1' \
  -H 'sec-ms-gec: 9F303A9DD33079D2292885B344F6543BFFE58C84BCEBD7F69368535E67FB3011' \
  -H 'sec-ms-gec-version: 1-116.0.1938.76' \
  -H 'upgrade-insecure-requests: 1' \
  -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36 Edg/116.0.1938.76' \
  -H 'x-edge-shopping-flag: 1' \
  --compressed
CURL;

return [
    'curl' => $curlBash,
    'prepare' => function (&$setting, $text) {
        $setting['url'] = str_replace('%s', urlencode($text), $setting['url']);
    },
    'callback' => function ($content) {
        preg_match('#<ol id="b_results" class="">[\s\S\r]*?<p\b.*?>(.*?)</p>[\s\S\r]*?</li>#i', $content, $matches);
        $firstP = str_replace('<span class="algoSlug_icon" data-priority="2">网页</span>', '', $matches[1]);
        $firstP = preg_replace('#<span class="news_dt">.*?</span>#i', '', $firstP);
        return preg_replace(['#^\d+年\d+月\d+日#', '/\d{4}\-\d{1,2}\-\d{1,2}/', '/&#?\w{4,5};/i', '# 展开$#'], '', strip_tags($firstP));
    },
];
