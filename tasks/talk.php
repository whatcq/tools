<?php
if (!empty($_GET['talk'])) {
    $logFile = 'talk.log';
    // @todo rich input：声音|图片|代码 输入，指定哪几个bot来回答
    $text = $_GET['talk'];

    file_put_contents($logFile, "\n" . date('Y-m-d H:i:s') . ' ' . $text, FILE_APPEND);

    session_start();
    $botName = 'bot';
    $outHtml = '';
    foreach (glob('bots/*.php') as $script) {
        // @todo return json with rich content
        // @todo 功能列表-使用说明-自我介绍
        if ($responseText = include $script) {
            file_put_contents($logFile, "\n" . $script . ': ' . $responseText, FILE_APPEND);
            $responseText = str_replace(["\n", "\r"], ['\\n', ''], addslashes($responseText));
            die('<script>parent.response("' . $botName . '", "' . $responseText . '")</script>' . $outHtml);
        }
    }
    die;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <title>说话</title>
    <link rel="stylesheet" href="talk.css"/>
</head>

<body>
<div style="width: 700px;margin: 0 auto;">
    <div id="chatroom"></div>

    <form id="talk-form" target="talkFrame">
        <div>
            <textarea name="talk" id="input" rows="4" cols="80" accesskey="Z"></textarea>
        </div>
        <input type="submit">
        <input type="reset">
        <input type="button" value="清屏" onclick="$('chatroom').innerHTML=''">
        <input type="text" id="back_msg" size="60">
        <span id="toggle_speech" style="cursor:pointer">🕪</span>
        <select id="voi"></select>
        <input type="checkbox" id="speak-out" title="说话开关" xchecked/>
    </form>
    <div id="speaker-div" style="display:none">
        <audio controls autoplay muted id="speaker">
            <source src="" type="audio/mpeg">
        </audio>
        speed:<input type="number" name="speed" id="speed" value="8" min=0 max=15 width="20"/>
        vol:<input type="number" name="vol" id="vol" value="9" min=0 max=15 width="20"/>
        <select name="per" id="per">
            <option value="0">标准女音</option>
            <option value="1">标准男音</option>
            <option value="3" selected>斯文男音</option>
            <!--
        <option value="4">小萌萌</option>
        <option value="5">知性女音</option>
        <option value="6">老教授</option>
        <option value="8">葛平音</option>
        <option value="9">播音员</option>
        <option value="10">京腔</option>
        <option value="11">温柔大叔</option>
        -->
        </select>
    </div>
    <iframe name="talkFrame" id="talkFrame" width=100% height="420" src="about:blank" title="audio-play"
            style="border: 1px solid #bfbfbf;"></iframe>
</div>
</body>
<script src="open-web.js"></script>
<script>
    function $(str) {
        return document.getElementById(str);
    }

    var input = $('input');
    var chatroom = $('chatroom');
    var speaker = $('speaker');
    var nick = 'cqiu'; //prompt("enter your name");

    var synth = window.speechSynthesis,
        voices = [],
        rate = 1.5,
        vi = 6;

    function strip(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || "";
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
            if (voices.length > 0) return;
            voices = synth.getVoices().filter((v, i) => ['zh-CN', 'zh-TW'].includes(v.lang))
                .sort((a, b) => b.name.includes('Online') - a.name.includes('Online'));
            console.log(voices);
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

    var toggle_speech = document.getElementById('toggle_speech');

    function readQueue(sentence) {
        if (toggle_speech.innerText !== '🕪') {
            sentences = []; // clear queue
            return;
        }

        if (!sentence) return;
        console.log(sentence)

        speechSynthesis.cancel();
        let msg = new SpeechSynthesisUtterance(sentence);
        msg.onend = function (event) {
            console.log("SpeechSynthesisUtterance.onend");
            setTimeout(() => input.focus(), 1500);
        };
        vi = document.getElementById('voi').selectedIndex;
        msg.voice = voices[vi];
        msg.rate = rate; // 0.5~2
        // msg.pitch = 1;// 0~2 free online voice不支持
        speechSynthesis.speak(msg);
    }

    window.onload = function () {
        $('input').focus();
        toggle_speech.onclick = function () {
            if (this.innerText === '🕨') {
                this.innerText = '🕪';
            } else {
                this.innerText = '🕨';
                speechSynthesis.cancel();
            }
        };
    };

    // 防抖动函数
    const debounce = function (func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    };

    function renderChat(who, msg) {
        var div = document.createElement("div");
        div.className = 'chat';
        if (who === nick) div.className += ' i-say';
        div.innerHTML = ('<u class="q-' + who + '">' + who + '</u>' + msg); //.trim();
        chatroom.append(div);
        input.scrollIntoView();
    }

    function speak(msg) {
        return readQueue(strip(msg.replace(/(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/, '')));

        if (!$('speak-out').checked) return;
        var vol = $('vol').value,
            speed = $('speed').value,
            per = $('per').value;
        msg = encodeURIComponent(msg.replace(/<[^>]+/g, ''));
        speaker.src = `https://tts.baidu.com/text2audio?tex=${msg}&cuid=baike&lan=ZH&ie=utf-8&ctp=1&pdt=301&vol=${vol}&rate=32&per=${per}&spd=${speed}`;
    }

    var responseLength = 0;

    function response(who, msg) {
        renderChat(who, msg.replace(/\n/g, '<br>').replace('  ', '&nbsp;&nbsp'));
        speak(msg);
        input.value = '';
        $('back_msg').value = '';
        $('back_msg').focus();
        responseLength = msg.length;
    }

    var form = $('talk-form');

    form.onsubmit = function () {
        var msg = input.value;
        if (!msg.trim()) return false;
        renderChat(nick, msg);
        if (openWeb(msg)) {
            input.value = '';
            return false;
        }
        return true;
    };

    input.oninput = debounce(function () {
        if (!form.onsubmit()) return;
        form.submit();
        $('talkFrame').focus();
    }, 1000);

    document.onkeydown = function (e) {
        if (e.key === 'Escape') {
            speechSynthesis.cancel();
            // speaker.pause();
            $('talk-form').reset();
            return false;
        }
    };

    // speaker.onended = speaker.onpause = debounce(function () {
    //     input.focus();
    // }, 1500);

    var speakerDiv = $('speaker-div');
    $('speak-out').onclick = function () {
        if (this.checked) {
            speakerDiv.style.display = '';
        } else {
            speakerDiv.style.display = 'none';
        }
    };
</script>

</html>