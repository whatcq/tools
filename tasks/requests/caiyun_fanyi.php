<?php

/**
 * 彩云翻译 token-refresh required!@todo
 * https://fanyi.caiyunapp.com/#/
 */
$curlBash = <<<'CURL'
curl 'https://api.interpreter.caiyunai.com/v1/translator' \
  -H 'authority: api.interpreter.caiyunai.com' \
  -H 'accept: application/json, text/plain, */*' \
  -H 'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6' \
  -H 'app-name: xy' \
  -H 'content-type: application/json;charset=UTF-8' \
  -H 'device-id: dd4d8ea09850a6a6d2cf486990405ba9' \
  -H 'origin: https://fanyi.caiyunapp.com' \
  -H 'os-type: web' \
  -H 'os-version;' \
  -H 'referer: https://fanyi.caiyunapp.com/' \
  -H 't-authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJicm93c2VyX2lkIjoiZGQ0ZDhlYTA5ODUwYTZhNmQyY2Y0ODY5OTA0MDViYTkiLCJpcF9hZGRyZXNzIjoiMTExLjEwLjI0Mi4yMjAiLCJ0b2tlbiI6InFnZW12NGpyMXkzOGp5cTZ2aHZpIiwidmVyc2lvbiI6MSwiZXhwIjoxNjg0Mjg5NjA0fQ.h6nXRDTtcv8At_JedVXViSyp3TqQScVJpyRZqZ8gduI' \
  -H 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.54' \
  -H 'x-authorization: token:qgemv4jr1y38jyq6vhvi' \
  --data-raw '{"source":["bookmarks","is","ready","ok",""],"trans_type":"auto2zh","request_id":"web_fanyi","media":"text","os_type":"web","dict":true,"cached":true,"replaced":true,"detect":true,"browser_id":"dd4d8ea09850a6a6d2cf486990405ba9"}' \
  --compressed
CURL;

$response = <<<'RESPONSE'
{"target":["5Yzz562+","5cvi","5LrT5nFU5nJ95YdT","5nJ95MPa",""],"rc":0,"confidence":0.8}
RESPONSE;

return [
    'curl'     => $curlBash,
    'prepare'  => function (&$setting, $text) {
        $data = json_decode($setting['data'], 1);
        $data['source'] = explode("\n", str_replace("\r", '', $text));
        $setting['data'] = json_encode($data);
    },
    'callback' => function ($response) {
        $data = json_decode($response, 1);
        if (!empty($data['target'])) {
            $results = [];
            foreach ($data['target'] as $line) {
                $results[] = base64_decode(str_rot13($line));
            }

            return implode("\n", $results);
        }

        return $response;
    },
];
