<?php

use Orhanerday\OpenAi\OpenAi;

if (!empty($_REQUEST['prompt'])) {
    include 'vendor/autoload.php';

    date_default_timezone_set('Asia/Chongqing');
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header("Access-Control-Allow-Origin:*");

    $log = './chatgpt.log';
    $token = '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';

    $prompt = $_REQUEST['prompt'];

    $openAi = new OpenAi($token);
    file_put_contents($log, "==========\n$prompt\n", FILE_APPEND);
    $str = '';
    // 终于找到eventSource,error原因: 这个请求报错了：php证书过期!
    $response = $openAi->completion([
        'prompt'            => urldecode($prompt),
        'temperature'       => 0.9,
        'top_p'             => 1,
        'model'             => 'text-davinci-003',
        'max_tokens'        => 1024,
        'frequency_penalty' => 0,
        'presence_penalty'  => 0.6,
        'stop'              => [" Human:", " AI:"],
        "stream"            => true,
    ], function ($curl_info, $data) use (&$str, &$empty, $log) {
        // file_put_contents($log, "------------$curl_info\n$data\n", FILE_APPEND);
        if ($data === "data: [DONE]\n\n") {
            echo $data;
        } else {
            $json = json_decode(substr($data, 6), 1);
            $text = $json['choices'][0]['text'] ?? '';
            if (empty($str) && $text[0] === "\n") {
                $text = ltrim($text);
            }
            $str .= $text;
            $text = nl2br(str_replace('  ', '&nbsp;&nbsp;', htmlspecialchars($text)));
            echo "data: $text\n";
        }
        echo PHP_EOL;
        ob_flush();
        flush();

        return strlen($data);
    });

    file_put_contents($log, "------------\n$str\n", FILE_APPEND);
    die("data: [DONE]\n\n");
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>ChatGPT</title>
<style>
    body {
        margin: 0 auto;
        width: 600px;
    }

    div>div {
        border-radius: 5px;
        background-color: #f3ecd9;
        padding: 5px;
        margin-bottom: 10px;
    }
</style>
<script>
    var sentances = [],
        sentance = '';

    function read() {
        let text = sentances.shift();
        if (!text) return;
        if (document.getElementById('toggle_read').innerText == '🕪') {
            speechSynthesis.speak(new SpeechSynthesisUtterance(text));
        }
        read();
    }

    function strip(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || "";
    }
    window.onload = function() {
        var nick = 'cqiu'; //prompt("enter your name");
        var bar = document.getElementById('bar');
        var input = document.getElementById('input');
        var bt = document.getElementById('bt');
        var i = 1;

        bt.onclick = function() {
            var div = document.createElement("div");
            div.innerHTML = ('<u>' + nick + '</u>: ' + input.value);
            document.body.insertBefore(div, bar);
            div = document.createElement("div");
            var botId = 'msg_' + (i++);
            div.innerHTML = ('<u>bot</u>:<div id="' + botId + '"></div>');
            document.body.insertBefore(div, bar);
            input.scrollIntoView();

            var chat = new window.EventSource("?prompt=" + input.value);
            // var emptyChar = true;
            chat.onmessage = function(e) {
                if (e.data == "[DONE]") {
                    chat.close();
                    if (sentance.length > 10) {
                        sentances.push(strip(sentance));
                        sentance = '';
                        read();
                    }
                    input.select();
                    return;
                }
                var text = e.data; //JSON.parse(e.data).choices[0].text;
                // if (emptyChar) {
                //     if (text[0] === "\n") {
                //         text = text.trimStart();
                //         emptyChar = false;
                //     }
                // }
                sentance += text;
                if (text.indexOf('<br />') > -1) {
                    sentances.push(strip(sentance));
                    sentance = '';
                    read();
                }
                document.getElementById(botId).innerHTML += text;
                console.log(text)
                input.scrollIntoView();
            };
            chat.onerror = function(e) {
                console.log(e);
                chat.close();
                alert('EventSource Error');
            };
        };
        document.getElementById('toggle_read').onclick = function() {
            if (this.innerText == '🕨') {
                this.innerText = '🕪';
            } else {
                this.innerText = '🕨';
            }
        };
    };
</script>

<div style="position: fixed;bottom: 0;width:80%" id="bar">
    <span id="toggle_read" style="cursor:pointer">🕪</span>
    <input id='input' style="width:80%" onkeydown="if(event.keyCode == 13){bt.click()}" />
    <input type='button' value="send" label="send" id="bt" />
</div>
