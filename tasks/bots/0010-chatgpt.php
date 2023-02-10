<?php

$botName = 'AI';
$token = '';
if (!$token) return;

/** @var $text string */
$postFields = [
    "prompt" => $text,
    "temperature" => 0.9,
    "top_p" => 1,
    "model" => "text-davinci-003",
    "max_tokens" => 1024,
    "frequency_penalty" => 0,
    "presence_penalty" => 0.6,
    "stop" => [
        " Human:",
        " AI:"
    ],
];
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
];
$result = curl_post('https://api.openai.com/v1/completions', json_encode($postFields), $headers, 25, 1);
file_put_contents('chatgpt.log', "\n" . $text . "\n" . $result, FILE_APPEND);
if ($res = json_decode($result, 1)) {
    return $res['choices'][0]['text']
        ? trim($res['choices'][0]['text'])
        : '我不知道？';
}

return;
