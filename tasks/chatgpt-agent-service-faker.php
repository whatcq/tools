<?php

/**
 * ChatGPT Agent Service Faker
 * - get request question from api|text|xiaoai
 * - send question to chatgpt agent by eventsource
 * - get response from chatgpt agent (ajax post)
 * @author Fuer Liu <gdaymate@126.com>
 */

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$questionKey = 'question';
$answerKey = 'answer';
while (1) {
    $question = $redis->rpop($questionKey);
    if (!$question) goto A_REST;

    echo '> ', $question, PHP_EOL;
    $redis->del($answerKey);
    // $redis->publish($questionKey, $question);
    foreach (getAnswer($question) as $answer) {
        echo '< ', $answer, PHP_EOL;
        $redis->lpush($answerKey, $answer);
    }

    A_REST:
    usleep(100000);
}

function getAnswer($question)
{
    if (strpos($question, '天气') !== false) {
        foreach (['今天天气不错，挺风和日丽的', '抓紧时间洗洗被子', '或者朗读一下经典文章'] as $answer)
            yield $answer;
        return;
    }
    for($i = 1; $i < 3; $i++) {
        yield getRandomLine('增广贤文.txt', mt_rand(1, 758));
    }
}

// get a random line from file,which has known lines
function getRandomLine($filename, $lineNumber)
{
    $file = fopen($filename, 'r');
    if ($file) {
        $current_line_number = 0;
        while (($line = fgets($file)) !== false) {
            $current_line_number++;
            if ($current_line_number == $lineNumber) {
                return trim($line);
            }
        }
        fclose($file);
    }
}
