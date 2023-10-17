<?php

include 'include.php';
/**
 * 处理之前记录的chatGPT答案文件，使只保留答案。
 */
$file = '../tasks/answer.txt';
$file2 = $file . '.txt';

$prev = '';
foreach (file($file) as $line) {
    $content = trim($line);
    if (!$content) {
        continue;
    }

    if ($content[0] === '{') {
        $json = json_decode($content, 1);
        if (($json['code_msg'] ?? '') === "Success") {
            // chatgptproxy
            $line = json_encode($json['resp_data']['answer'], JSON_UNESCAPED_UNICODE) . "\n";
            file_put_contents($file2, $line, 8);
        } elseif (!empty($json['messages'][0])) {
            // poe
            $json = json_decode($json['messages'][0], 1);
            if (
                isset($json['payload']['data']['messageAdded']['state'])
                && "complete" == $json['payload']['data']['messageAdded']['state']
                && !$json['payload']['data']['messageAdded']['clientNonce']
            ) {
                $text = $json['payload']['data']['messageAdded']['text'];
                if ($text == $prev) continue;
                $prev = $text;
                $line = json_encode($text, JSON_UNESCAPED_UNICODE) . "\n";
                file_put_contents($file2, $line, 8);
            } else {
                file_put_contents($file2, $line, 8);
            }
        }
    } else {
        file_put_contents($file2, $line, 8);
    }
}
