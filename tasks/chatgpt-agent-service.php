<?php

/**
 * ChatGPT Agent Service
 * - get request question from api|text|xiaoai
 * - send question to chatgpt agent by eventsource
 * - get response from chatgpt agent (ajax post)
 * @author Fuer Liu <gdaymate@126.com>
 * @date 2023-05-03
 */
$answerFile = 'answer.txt';

$act = $_REQUEST['act'] ?? '';

header('Access-Control-Allow-Origin: *');


$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$questionKey = 'question';
$answerKey = 'answer';
$sentKey = 'sent';

// send question to chatgpt agent by eventsource
if ('get_question' === $act) {
    // set_time_limit(0);
    date_default_timezone_set("Asia/Chongqing");
    header("Content-Type: text/event-stream\n\n");

    ob_flush();
    flush();
    $readTime = $i = 0;
    $pid = getmypid();
    while (1) {
        echo 'id: ', date('i:s'), "\n\n";
        if ($question = $redis->rpop($questionKey)) {
            echo 'data: ', $question, "\n\n";
            file_put_contents($answerFile, "\n=========$pid\n<$question>\n========", FILE_APPEND);
        } else {
            echo ": )\n\n"; // 表示注释, 保持连接不中断
        }
        ob_flush();
        flush();

        // 解决CPU占用率问题：忽快忽慢
        usleep(300000);
    }
    die;
}

// get response from chatgpt agent (ajax post)
if ('save_response' === $act) {
    $response = file_get_contents('php://input');
    $json = json_decode($response, 1);
    $sentPoint = intval($redis->get($sentKey)); // 已经发送的答案字符串位置
    if ($json['code'] != 200 || empty($json['resp_data']['answer'])) {
        die('empty');
    }

    function strposUntil($string, $chars = ['。', '；', "\n\n"], $offset = 0)
    {
        foreach ($chars as $char) {
            $pos = strpos($string, $char, $offset);
            if ($pos !== false) {
                return $pos + strlen($char);
            }
        }
        return 0;
    }

    $answer = $json['resp_data']['answer'];
    $p = strposUntil($answer, ['；', '。', "\n\n"], $sentPoint);
    if ($p - $sentPoint < 10) {
        $p = strposUntil($answer, ['；', '。', "\n\n"], $p);
    }
    $results = [];
    if ($p !== false) {
        ($answerSentence = trim(substr($answer, $sentPoint, $p - $sentPoint)))
        && $results[] = trim($answerSentence);
    }
    if ($json['resp_data']['status'] == 3) {
        $results[] = 'over';
        $p = 0;
    }
    if ($results) {
        $redis->lpush($answerKey, ...$results);
        $redis->set($sentKey, $p);
    }
    echo file_put_contents($answerFile, "\n$response", FILE_APPEND), 'saved';
}
