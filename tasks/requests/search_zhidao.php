<?php


$curlBash = <<<'CURL'
curl 'https://zhidao.baidu.com/search?lm=0&rn=10&pn=0&fr=search&dyTabStr=null&word=%s' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Cookie: BIDUPSID=A2BD8EB1EDDCEB7B9621BF7EC46E0381; PSTM=1630207647; BAIDUID=A2BD8EB1EDDCEB7B9621BF7EC46E0381:SL=0:NR=10:FG=1; BDUSS=RPUVBKcEd2Z0xMMmxoeVVUQ3NmTzVnZlRCRkF0NGJnd1pMbGtld2ttQ0czeFZsSVFBQUFBJCQAAAAAAAAAAAEAAADCsxsAY3FpdQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIZS7mSGUu5kR2; BDUSS_BFESS=RPUVBKcEd2Z0xMMmxoeVVUQ3NmTzVnZlRCRkF0NGJnd1pMbGtld2ttQ0czeFZsSVFBQUFBJCQAAAAAAAAAAAEAAADCsxsAY3FpdQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIZS7mSGUu5kR2; BAIDUID_BFESS=A2BD8EB1EDDCEB7B9621BF7EC46E0381:SL=0:NR=10:FG=1; ZFY=F:AnbIUH5zPIFS1DL02o38NR8:AzGDe8Rh33yL:ACHdBrA:C; MCITY=-132%3A; H_PS_PSSID=39634_39648_39668_39664_39676_39679_39712_39779_39791_39788_39703_39793_39682; H_WISE_SIDS=110085_265881_275733_269051_278414_259642_281190_280650_281865_281891_281679_275095_282170_282112_282564_282632_282848_253022_282402_283354_251972_283364_281704_283727_283445_282887_256223_283867_283782_283896_284006_281642_283945_273243_284281_276711_284276_284263_281182_284143_276929_283932_284452_284603_265986_284615_284690_282605_284718_284794_284852_284876_284881_284891_283796_284974_282485_284810_285220_284836_285289_285336_285371_282425_285449_283902_285647_282467_285873_285869_281696_283016_277936_285992_286069_256083_285938_286172_278919_281810_282177; H_WISE_SIDS_BFESS=110085_265881_275733_269051_278414_259642_281190_280650_281865_281891_281679_275095_282170_282112_282564_282632_282848_253022_282402_283354_251972_283364_281704_283727_283445_282887_256223_283867_283782_283896_284006_281642_283945_273243_284281_276711_284276_284263_281182_284143_276929_283932_284452_284603_265986_284615_284690_282605_284718_284794_284852_284876_284881_284891_283796_284974_282485_284810_285220_284836_285289_285336_285371_282425_285449_283902_285647_282467_285873_285869_281696_283016_277936_285992_286069_256083_285938_286172_278919_281810_282177; BA_HECTOR=00210581uma9212l212k01aj1imjhk11q; BDORZ=AE84CDB3A529C0F8A2B9DCDD1D18B695; rsv_i=95e1ZfCZ+8Gxgzf7nMfFl5RkB4ksEqCXNuHbXvI7qo6RTT/mHREprX06xsgyvR10g2Yj/JMRMaGp0p0zZtSIj6mQc+Pmzq4; SE_LAUNCH=5%3A28357181_0%3A28357181_14%3A28357182; BDPASSGATE=IlPT2AEptyoA_yiU4SDs3lIN8eDEUsCD34OtVkZi3ECGh67BmhH84rJ4E68LK7y84STT-pasarNdijHCSGVqa_M-dPsAllpMaj8cxvqOucKBPsx2y_YZCb4jKUE2sA8PbRt09OMH0gVNSywtfQC6hAY1fe7673IhbAjzsn4Zgafl_l7RBVrMz8ypY76tO-rAN2CRy1zEdF2AGVqZLwfrLTTtjUIxP8wv70aRatY6C3L5miI8EeWjZvYtCYnnLi-83Bin3OyLxKSl2yU5q-2YTEVFjCi1y3HVNFsEDLiJi_c7J4PWNq7rMTjuHqcmjbPbLe6NQOuOcr9E2DQ_ypA80Z-8Qs0HEoXiPSQ5RNGGiBrZPZUymW3rBgLA-mYKPv4n42NSTxxqO5hzmFaAreCmoyrSBuTaw1RoM0wOCpePu8gwfn63GYXVt7i9m9BTu8e94HqfE5XiEaH8SIhKuuKaJ0zdPM7J4iI16UCAoizSjSj91fj6RMR7lTgn5-RKUpue6xuPNo3XK3O_eLZfwrXivKDY9WGGtF35xU3MZb3pTxgDZnEcmqV4CmfjyomDgpNVPeWdvI4n_VDDnCdMwtyQjCBWgegh1NJKOtD0JPITwcSHd7Iy3Z9Vg0__JhoRWFjLtPgVIrTMKGjRBTf9X7lRgrJA_F4mI74V91XYGS2tQE_D35T4EcdOxV-ZnoErEKbaMVrcg3Q0Nja0k4AsP2qin7cLEOEjEi2Zu5S-GXH9AfzscvhGkl588bkwCWj_WA4-87IYhq-CA5_StcH-DA3bi-gVi-r8ce2WUam-Omvk8_; PSINO=6; ZD_ENTRY=empty; session_id=170143969423615839616581723104; Hm_lvt_16bc67e4f6394c05d03992ea0a0e9123=1701439695; Hm_lpvt_16bc67e4f6394c05d03992ea0a0e9123=1701439695; Hm_lvt_6859ce5aaf00fb00387e6434e4fcc925=1699328265,1699692556,1701006037,1701439712; Hm_lpvt_6859ce5aaf00fb00387e6434e4fcc925=1701439712' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://zhidao.baidu.com/' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: same-origin' \
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

        return $result ? preg_replace('#温馨提示.*#', '', $result[array_rand($result)]) : null;
    },
];