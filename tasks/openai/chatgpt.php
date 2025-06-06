<?php

use Orhanerday\OpenAi\OpenAi;

if (!empty($_REQUEST['prompt'])) {
    include 'vendor/autoload.php';

    date_default_timezone_set('Asia/Chongqing');
    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-cache');
    header("Access-Control-Allow-Origin:*");

    $log = './chatgpt.log';

    $prompt = $_REQUEST['prompt'];

    $token = file_get_contents('./deepseek.token');
    $openAi = new OpenAi($token);
    $openAi->setBaseURL('https://api.deepseek.com');
    $time = date('y-m-d H:i');
    file_put_contents($log, "==========$time\n$prompt\n", FILE_APPEND);
    $str = '';
    // 终于找到eventSource,error原因: 这个请求报错了：php-ssl证书过期!
    $response = $openAi->chat([
        'model' => 'deepseek-chat',
        'messages' => [
            // [
            //     'role' => 'system',
            //     'content' => 'You are a helpful assistant'
            // ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'stream' => true,
    ], function ($curl_info, $datas) use (&$str, &$empty, $log) {
        // $curl_info = '';//json_encode(curl_getinfo($curl_info), 448);
        // file_put_contents($log . '.debug', "------------$curl_info\n$datas\n", FILE_APPEND);
        foreach (explode("\n\n", $datas) as $data) {
            if ($data === "data: [DONE]") {
                break;
            } else {
                if (!trim($data)) continue;
                $json = json_decode(substr($data, 6), 1);
                $text = $json['choices'][0]['delta']['content'] ?? '';
                if (empty($str) && isset($text[0]) && $text[0] === "\n") {
                    $text = ltrim($text);
                }
                $str .= $text;
                $text = nl2br(str_replace('  ', '&nbsp;&nbsp;', htmlspecialchars($text)));
                echo "data: $text";
            }
            echo "\n\n";
        }
        ob_flush();
        flush();

        return strlen($datas);
    });

    // $token = file_get_contents('./chatgpt.token');
    // $openAi = new OpenAi($token);
    // $time = date('y-m-d H:i');
    // file_put_contents($log, "==========$time\n$prompt\n", FILE_APPEND);
    // $str = '';
    // // 终于找到eventSource,error原因: 这个请求报错了：php-ssl证书过期!
    // $response = $openAi->completion([
    //     'prompt'            => urldecode($prompt),
    //     'temperature'       => 0.9,
    //     'top_p'             => 1,
    //     'model'             => 'text-davinci-003',
    //     'max_tokens'        => 1024,
    //     'frequency_penalty' => 0,
    //     'presence_penalty'  => 0.6,
    //     'stop'              => [" Human:", " AI:"],
    //     "stream"            => true,
    // ], function ($curl_info, $data) use (&$str, &$empty, $log) {
    //     if ($data === "data: [DONE]\n\n") {
    //         echo $data;
    //     } else {
    //         $json = json_decode(substr($data, 6), 1);
    //         $text = $json['choices'][0]['text'] ?? '';
    //         if (!isset($json['choices'][0]['text'])) {
    //             file_put_contents($log . '.debug', "------------$curl_info\n$data\n", FILE_APPEND);
    //         }
    //         if (empty($str) && isset($text[0]) && $text[0] === "\n") {
    //             $text = ltrim($text);
    //         }
    //         $str .= $text;
    //         $text = nl2br(str_replace('  ', '&nbsp;&nbsp;', htmlspecialchars($text)));
    //         echo "data: $text\n";
    //     }
    //     echo PHP_EOL;
    //     ob_flush();
    //     flush();
    //
    //     return strlen($data);
    // });

    file_put_contents($log, "------------\n$str\n", FILE_APPEND);
    die("data: [DONE]\n\n");
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport"
      content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
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

    #voi {
        width: 20px
    }

    #voi:focus {
        width: 50px
    }
</style>

<div id="here"></div>
<div id="bar">
    <select id="voi"></select>
    <span id="toggle_speech" style="cursor:pointer">🕪</span>
    <input id='input' accesskey="Z" style="width:80%"
           onkeydown="if(event.keyCode === 13){document.getElementById('bt').click()}"/>
    <input type='button' value="send" id="bt"/>
</div>

<script>
    var sentences = [],
        sentence = '',
        synth = window.speechSynthesis,
        voices = [],
        vi = 8;

    function strip(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent.replace(/(#|\*)/g, '') || "";
    }

    function getLen(str) {
        let len = 0;
        for (let i = 0; i < str.length; i++) {
            if (str[i].match(/^[\u4e00-\u9fa5]+$/)) {
                len += 1;
            } else {
                len += 0.3; // 英文该按单词论长短，这里用于发音
            }
        }
        return Math.floor(len);
    }

    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = function () {
            voices = synth.getVoices().filter((v, i) => /Online.*Chinese/.test(v.name));
            // console.log(voices);
            let voiceSelect = document.getElementById('voi');
            voiceSelect.innerHTML = "";
            for (let i = 0; i < voices.length; i++) {
                let tmp = voices[i].name.replace(/(Microsoft | Online \(Natural\)|Chinese )/g, '');
                const option = document.createElement("option");
                option.textContent = tmp;
                voiceSelect.appendChild(option);
            }
            voiceSelect.selectedIndex = vi;
        };
    }

    window.onload = function () {
        var nick = 'cqiu'; //prompt("enter your name");
        var here = document.getElementById('here');
        var input = document.getElementById('input');
        var bt = document.getElementById('bt');
        var toggle_speech = document.getElementById('toggle_speech');
        var i = 1;
        var reading = false;

        // 半天理不清这个逻辑：if(🕪){add queue;if(!playing)play();}
        function readQueue(sentence) {
            if (toggle_speech.innerText !== '🕪') {
                sentences = []; // clear queue
                return;
            }

            if (sentence) sentences.push(sentence);
            if (reading) return;

            let text;
            while (sentences.length > 0) {
                text = sentences.shift();
                if (text) break; // until text not empty
            }
            if (!text) return;
            console.log(text)

            let msg = new SpeechSynthesisUtterance(text);
            vi = document.getElementById('voi').selectedIndex;
            msg.voice = voices[vi];
            speechSynthesis.speak(msg);
            reading = true;

            setTimeout(function () {
                reading = false;
                readQueue('');
            }, 1000 * getLen(text) / 5); // 5字/s
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
                if (e.data === "[DONE]") {
                    chat.close();
                    if (sentence.length > 2) {
                        readQueue(strip(sentence))
                        sentence = '';
                    }
                    input.select();
                    return;
                }
                var text = e.data;
                sentence += text;
                if (text.indexOf('<br />') > -1 || text.indexOf('。') > -1) {
                    readQueue(strip(sentence))
                    sentence = '';
                }
                document.getElementById(botId).innerHTML += text;
                // console.log(text)
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