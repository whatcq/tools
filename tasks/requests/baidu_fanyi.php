<?php

/**
 * 百度翻译，需要js：sign,Acs-Token @todo
 * http://fanyi.baidu.com/#en/zh/bookmarks%0Ais%0Aready
 */

$curlBash = <<<'CURL'
curl 'https://fanyi.baidu.com/v2transapi?from=en&to=zh' \
  -H 'Accept: */*' \
  -H 'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'Acs-Token: 1674029128306_1674032169144_gNF6xEf4eCcQZnLEAQDq4dA12UqNF/gQddV4G64w+VHdFqNXJWTuTGiPN2mIUfYwJeyEQqETIfTkSq1/gnEuDj6SsEPIZSLS4CFyhwsbR9CuWdJF3OTUwVWSKSTFuI+obFMF8U/pTXsadTGb9xin3/KX/1ep5zylS591GvD3HJiVdeKzknejwhy+ejvo7T95ta1fAjF38wgC76mr1TJbMA5KfxrBlSwpn/edFDULTwDsrtilM9QX0ZERhZubSqFiMLNxJB3UqpJPRMjkLnk+KEh9OM/RnO5W35d1HFF9hm/eqeypG+PKSMSn+ZdydbcEStFQlT7x2BnRpzGiG8POjQ==' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' \
  -H 'Cookie: __yjs_duid=1_0ad14114ac72bac4f2e471a4658622491638259182505; BIDUPSID=3B09B0A06ACB9D37E2407552A3BD40F7; PSTM=1638521778; FANYI_WORD_SWITCH=1; REALTIME_TRANS_SWITCH=1; HISTORY_SWITCH=1; SOUND_SPD_SWITCH=1; SOUND_PREFER_SWITCH=1; BAIDUID=3B09B0A06ACB9D37E2407552A3BD40F7:SL=0:NR=10:FG=1; APPGUIDE_10_0_2=1; newlogin=1; BDUSS=lZJWGxzWXJMS0FFeWZqWXJVSTRDbWt-aHp6THJ-WVNwQmQzSkdCRTZHWU5sc3hqRVFBQUFBJCQAAAAAAAAAAAEAAADCsxsAY3FpdQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0JpWMNCaVjQ; BDUSS_BFESS=lZJWGxzWXJMS0FFeWZqWXJVSTRDbWt-aHp6THJ-WVNwQmQzSkdCRTZHWU5sc3hqRVFBQUFBJCQAAAAAAAAAAAEAAADCsxsAY3FpdQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0JpWMNCaVjQ; X-baiduvr-auth-token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJlYjMxYTI5YjVhYmQ0YjM1YTYwOWU1ZDhjYWY3M2FjZCIsInZyLXN5c3RlbS1pZCI6InN5cy12ci1tdnAiLCJ2ci1hY2NvdW50LWlkIjoiZWIzMWEyOWI1YWJkNGIzNWE2MDllNWQ4Y2FmNzNhY2QiLCJ2ci11c2VyLWlkIjoiZWIzMWEyOWI1YWJkNGIzNWE2MDllNWQ4Y2FmNzNhY2QiLCJ2ci1hY2NvdW50LXR5cGUiOjEsInZyLXVzZXItdHlwZSI6MSwiaXNzIjoiQkFJRFUtVlIiLCJpYXQiOjE2NzE3NjAxNDQsImF1ZCI6ImN1c3RvbSIsImV4cCI6MTY3Njk0NDE0NH0.Jn1C7KBzhuXTNeX7trvfZ69FWM9h_AdAO52Z8swdmA0; vr-system-id=sys-vr-mvp; vr-account-id=eb31a29b5abd4b35a609e5d8caf73acd; vr-user-id=eb31a29b5abd4b35a609e5d8caf73acd; vr-account-type=1; vr-user-type=1; MCITY=-119%3A132%3A; BAIDUID_BFESS=3B09B0A06ACB9D37E2407552A3BD40F7:SL=0:NR=10:FG=1; delPer=0; PSINO=7; H_PS_PSSID=; ZFY=Ne28dhM0Wd2EHFEaYIAzWnN2TyKvOtQN7Y39:B4QP9RM:C; RT="z=1&dm=baidu.com&si=8129b63a-1afd-4dc0-89d2-13ee6b999f9c&ss=lczu0y8r&sl=3&tt=1kb&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ld=n6v&ul=1al1&hd=1am7"; ZD_ENTRY=bing; Hm_lvt_64ecd82404c51e03dc91cb9e8c025574=1672899361,1674032079; Hm_lpvt_64ecd82404c51e03dc91cb9e8c025574=1674032079' \
  -H 'Origin: https://fanyi.baidu.com' \
  -H 'Referer: https://fanyi.baidu.com/' \
  -H 'Sec-Fetch-Dest: empty' \
  -H 'Sec-Fetch-Mode: cors' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.54' \
  -H 'X-Requested-With: XMLHttpRequest' \
  -H 'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Microsoft Edge";v="108"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Windows"' \
  --data-raw 'from=en&to=zh&query=%E2%80%A2SplDoublyLinkedList%3A%3AgetIteratorMode+%E2%80%94+Returns+the+mode+of+iteration%0A%E2%80%A2SplDoublyLinkedList%3A%3AisEmpty+%E2%80%94+Checks+whether+the+doubly+linked+list+is+empty&transtype=realtime&simple_means_flag=3&sign=423351.169606&token=7acb52353395bc497009ed5618664eaf&domain=common' \
  --compressed
CURL;

return [
    'curl'     => $curlBash,
    'prepare'  => function (&$setting, $text) {
        parse_str($setting['data'], $requestData);
        $requestData['query'] = $text;
        $setting['data'] = http_build_query($requestData);

        return;
        print_r($requestData);
        die;
        $requestData = json_decode($setting['data'], 1);
        $requestData['text'] = $text;
        $requestData['s'] = md5($requestData['from'] . $requestData['to'] . $requestData['text'] . '109984457');

        $setting['data'] = json_encode($requestData);
    },
    'callback' => function ($result) {
        $data = json_decode($result, 1);
        if (empty($data['trans_result']['data'])) {
            return $data['errno'] . $data['errmsg'];
        }
        $fanyi = [];
        foreach ($data['trans_result']['data'] as $item) {
            $fanyi[] = $item['dst'];
        }

        return implode("\n", $fanyi);
    },
];
