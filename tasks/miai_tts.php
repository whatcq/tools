<?php

/**
 * 让小米音箱说话
 * speech('email', 'pass', $msg);
 */

function get_sign()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://account.xiaomi.com/pass/serviceLogin?sid=micoapi&_json=true");
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, false);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cookie: deviceId=DD61C2E6F186DC6D; sdkVersion=iOS-3.2.7', 'User-Agent: MISoundBox/1.4.0 iosPassportSDK/iOS-3.2.7 iOS/11.2.5','Accept-Language: zh-cn','Connection: keep-alive'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = '&&&START&&&{"sid":"micoapi","serviceParam":"{\"checkSafePhone\":false}","desc":"登录验证失败","location":null,"captchaUrl":null,"callback":"https://api.mina.mi.com/sts","code":70016,"qs":"%3Fsid%3Dmicoapi%26_json%3Dtrue","_sign":"xkI9k6Y7vcHJBpsjJjsSqsog7cE="}';
    //$output = curl_exec($ch);
    curl_close($ch);
    preg_match('/_sign":"(.*?)"/', $output, $matches, PREG_OFFSET_CAPTURE);
    if (!isset($matches[1])) {
        return '';
    }

    return $matches[1][0];
}

function serviceLoginAuth2($user, $pass, $_sign)
{
    //这步有可能会验证验证码,一般不会
    $data = 'user=' . $user . '&_json=true&envKey=HoomE9%2Bc%2B1HTftu0OZ/FI81i4Nxfhw8CH/E8DTpaBRfW6%2BM7ZoFYp4%2BI6gUzFqGHlxkIpzX8lI4QUeIShJ9eM1Ki8vSevxbrV1NUxK7eVagToWUX67E5hCBfuAww4%2Bn8TxoYGub19mDijvg5%2BoiGvYMNxToMXpMiIo/q7Z5yzlY%3D&env=r4yzRg3Wc6FgZkaYl2Plpn8dU0Q8sNTLiLrmMe/J14QQ6fF6uhmK3KTLQdqJO9L8l2/7EGmU%2BTHLEtBz6pJ3Jw%3D%3D&sid=micoapi&_sign=' . urlencode(
            $_sign
        ) . '&callback=https%3A//api.mina.mi.com/sts&qs=%253Fsid%253Dmicoapi%2526_json%253Dtrue&pwd=' . $pass;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://account.xiaomi.com/pass/serviceLoginAuth2");
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array('Cookie: deviceId=XXXXXXXXXXXXXXXX; sdkVersion=iOS-3.2.7', 'content-type: application/x-www-form-urlencoded')
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    $outhead = curl_getinfo($ch);

    curl_close($ch);
    preg_match('/location":"(.*?)"/', $output, $matches, PREG_OFFSET_CAPTURE);
    if (!isset($matches[1])) {
        echo $output;

        return '';
    }
    $result['location'] = $matches[1][0];
    preg_match('/ssecurity":"(.*?)"/', $output, $matches, PREG_OFFSET_CAPTURE);
    $result['ssecurity'] = $matches[1][0];
    preg_match('/nonce":(.*?),/', $output, $matches, PREG_OFFSET_CAPTURE);
    $result['nonce'] = $matches[1][0];

    return $result;
}

function login_miai($url, $clientSign)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . "&clientSign=" . urlencode($clientSign));
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'User-Agent: MISoundBox/1.4.0 iosPassportSDK/iOS-3.2.7 iOS/11.2.5',
            'Accept-Language: zh-cn',
            'Connection: keep-alive',
        )
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    $outhead = curl_getinfo($ch);
    curl_close($ch);
    preg_match('/serviceToken=(.*?);/', $output, $matches, PREG_OFFSET_CAPTURE);
    if (!isset($matches[1])) {
        echo $output;

        return '';
    }
    $res['serviceToken'] = $matches[1][0];
    preg_match('/userId=(.*?);/', $output, $matches, PREG_OFFSET_CAPTURE);
    $res['userId'] = $matches[1][0];

    return $res;
}

function get_deviceId($userid, $serviceToken)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mina.mi.com/admin/v2/device_list?master=1&requestId=CdPhDBJMUwAhgxiUvOsKt0kwXThAvY");
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: userId={$userid};serviceToken={$serviceToken}"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);

    //echo output
    curl_close($ch);
    preg_match('/deviceID":"(.*?)"/', $output, $matches, PREG_OFFSET_CAPTURE);
    if (!isset($matches[1])) {
        echo $output;

        return '';
    }

    return $matches[1][0];
}

function serviceToken($nonce, $secrity)
{
    #逆向apk获取
    $str = "nonce={$nonce}&" . $secrity;
    $sha1 = sha1($str, true);

    return base64_encode($sha1);
}

function text_to_speech($cookie, $deviceId, $message)
{
    $url = "https://api.mina.mi.com/remote/ubus?deviceId=$deviceId&message=%7B%22text%22%3A%22" . urlencode(
            $message
        ) . "%22%7D&method=text_to_speech&path=mibrain&requestId=rb1gB2aATpRd7jfOpaT3pxp85ndZ7t";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLINFO_HEADER_OUT, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: {$cookie}"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    $output = curl_exec($ch);
    curl_close($ch);
    $resp = json_decode($output);

    return !$resp->code; // success code=0
}

function speech($user, $pass, $text)
{
    $cookieFile = __DIR__ . '/miai_cookie';
    $deviceIdFile = __DIR__ . '/miai_deviceId';

    if (
        ($cookie = @file_get_contents($cookieFile))
        && ($deviceId = @file_get_contents($deviceIdFile))
        && text_to_speech($cookie, $deviceId, $text)
    ) {
        return true;
    }

    $i = 3;
    while ($i--) {
        $_sign = get_sign();
        echo "sign:{$_sign}";
        if ($_sign == '') {
            continue;
        }

        $session = serviceLoginAuth2($user, $pass, $_sign);
        if (empty($session)) {
            continue;
        }
        print_r($session);

        $clientSign = serviceToken($session['nonce'], $session['ssecurity']);
        if ($clientSign == '') {
            continue;
        }

        $miai_session = login_miai($session['location'], $clientSign);
        if ($miai_session == '') {
            continue;
        }

        $deviceId = get_deviceId($miai_session['userId'], $miai_session['serviceToken']);
        if ($deviceId == '') {
            continue;
        }
        echo "deviceId:{$deviceId}";

        $cookie = "userId={$miai_session['userId']};serviceToken={$miai_session['serviceToken']}";
        file_put_contents($cookieFile, $cookie);
        file_put_contents($deviceIdFile, $deviceId);

        if (text_to_speech($cookie, $deviceId, $text)) {
            return true;
        }
    }

    return false;
}
