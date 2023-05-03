<?php

/**
 * ChatGPT Agent Service
 * - get request question from api|text|xiaoai
 * - send question to chatgpt agent by eventsource
 * - get response from chatgpt agent (ajax post)
 * @author Fuer Liu <gdaymate@126.com>
 */

$questionFile = 'question.txt';
$answerFile = 'answer.txt';
$act = $_REQUEST['act'] ?? '';

header('Access-Control-Allow-Origin: *');

// send question to chatgpt agent by eventsource
if ('get_question' === $act) {
    set_time_limit(0);
    date_default_timezone_set("Asia/Chongqing");
    header("Content-Type: text/event-stream\n\n");

    ob_flush();
    flush();
    $readTime = $i = 0;
    while (1) {
        clearstatcache(); //清除文件状态缓存
        $lastModifiedTime = filemtime($questionFile);

        // echo 'id: ', date('i:s'), "\n\n";
        // if ($lastModifiedTime > $readTime) {
        //     if ($question = trim(file_get_contents($questionFile))) {
        //         echo 'data: ', $question, "\n\n";
        //         file_put_contents($answerFile, "\n==$lastModifiedTime > $readTime=======\n$question\n========".var_export($question, 1), FILE_APPEND);
        //         // file_put_contents($questionFile, '');
        //     }
        //     $readTime = $lastModifiedTime;
        // } elseif ($i++ > 3) {
        //     $i = 0;
        //     echo ": )\n\n"; // 表示注释, 保持连接不中断
        // }

        echo 'id: ', date('i:s'), "\n\n";
        if ($question = file_get_contents($questionFile)) {
            echo 'data: ', $question, "\n\n";
            
            file_put_contents($answerFile, "\n==$lastModifiedTime > $readTime=======\n<$question>\n========".var_export($question, 1), FILE_APPEND);
            file_put_contents($questionFile, '');
        } else {
            echo ": )\n\n"; // 表示注释, 保持连接不中断
        }
        ob_flush();
        flush();
        // 解决CPU占用率问题：忽快忽慢
        usleep(500000);
    }
    die;
}

// get response from chatgpt agent (ajax post)
if ('save_response' === $act) {
    $response = file_get_contents('php://input');
    echo file_put_contents($answerFile, "\n$response", FILE_APPEND), 'saved';
}
