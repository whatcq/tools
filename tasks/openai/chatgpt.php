<?php

use Orhanerday\OpenAi\OpenAi;

if (!empty($_REQUEST['prompt'])) {
    include 'vendor/autoload.php';

    date_default_timezone_set('Asia/Chongqing');
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header("Access-Control-Allow-Origin:*");

    $log = './chatgpt.log';
    $token = file_get_contents('./chatgpt.token');

    $prompt = $_REQUEST['prompt'];

    $openAi = new OpenAi($token);
    file_put_contents($log, "==========\n$prompt\n", FILE_APPEND);
    $str = '';
    // 终于找到eventSource,error原因: 这个请求报错了：php-ssl证书过期!
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
        if ($data === "data: [DONE]\n\n") {
            echo $data;
        } else {
            $json = json_decode(substr($data, 6), 1);
            $text = $json['choices'][0]['text'] ?? '';
            if (!isset($json['choices'][0]['text'])) {
                file_put_contents($log . '.debug', "------------$curl_info\n$data\n", FILE_APPEND);
            }
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
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>ChatGPT</title>
<style>
    body {
        margin: 0 auto 30px;
        width: 800px;
        font: 16px/21px Consolas;
    }

    #bar {
        position: fixed;
        bottom: 0;
        margin: 0 auto;
        width: 800px;
    }

    #bar input {
        font: 16px/21px Consolas;
    }

    div > div {
        border-radius: 5px;
        background-color: #f3ecd9;
        padding: 5px;
        margin-bottom: 10px;
    }

    div > i {
        background: lightblue;
        border-radius: 10px;
        padding: 0px 5px;
        color: whitesmoke;
    }
</style>

<div id="here"></div>
<div id="bar">
    <span id="toggle_speech" style="cursor:pointer">🕪</span>
    <input id='input' accesskey="Z" style="width:85%" onkeydown="if(event.keyCode == 13){bt.click()}"/>
    <input type='button' value="send" id="bt"/>
</div>

<script>
    var sentances = [],
        sentance = '',
        synth = window.speechSynthesis,
        voices = [];

    function strip(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || "";
    }

    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = function () {
            voices = synth.getVoices().filter((v, i) => /Online.*Chinese/.test(v.name));
            console.log(voices);
        };
    }

    window.onload = function () {
        var nick = 'cqiu'; //prompt("enter your name");
        var bar = document.getElementById('bar');
        var here = document.getElementById('here');
        var input = document.getElementById('input');
        var bt = document.getElementById('bt');
        var toggle_speech = document.getElementById('toggle_speech');
        var i = 1;

        function read() {
            let text = sentances.shift();
            if (!text) return;
            if (toggle_speech.innerText === '🕪') {
                let msg = new SpeechSynthesisUtterance(text);
                msg.voice = voices[10];
                speechSynthesis.speak(msg);
                setTimeout(read)
            }
            read();
        }

        bt.onclick = function () {
            var div = document.createElement("div");
            div.innerHTML = ('<u>' + nick + '</u>: <i>' + i + '</i>' + input.value);
            document.body.insertBefore(div, here);
            div = document.createElement("div");
            var botId = 'msg_' + (i++);
            div.innerHTML = ('<u>bot</u>:<div id="' + botId + '"></div>');
            document.body.insertBefore(div, here);
            here.scrollIntoView();

            var chat = new window.EventSource("?prompt=" + input.value);
            chat.onmessage = function (e) {
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
                var text = e.data;
                sentance += text;
                if (text.indexOf('<br />') > -1 || text.indexOf('。') > -1) {
                    sentances.push(strip(sentance));
                    sentance = '';
                    read();
                }
                document.getElementById(botId).innerHTML += text;
                console.log(text)
                here.scrollIntoView();
            };
            chat.onerror = function (e) {
                console.log(e);
                chat.close();
                alert('EventSource Error');
            };
        };
        toggle_speech.onclick = function () {
            if (this.innerText === '🕨') {
                this.innerText = '🕪';
            } else {
                this.innerText = '🕨';
            }
        };
    };
</script>
