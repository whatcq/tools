window.voices = [];
// HiuGaai (Cantonese Traditional)
// HiuMaan (Hong Kong)
// WanLung (Hong Kong)

// Huihui|Kangkang|Yaoyao
// Xiaoxiao (Mainland)活泼、温暖,女声
// Xiaoyi (Mainland)Female, rich styles, 动漫，元气少女
// Yunjian (Mainland)Male, New voice, 体育评论风格
// Yunxi (Mainland)男声，活泼阳光的声音，风格丰富，非常流行的声音，广泛应用于多个配音短视频
// Yunxia (Mainland)Male, rich styles, 动漫，正太
// Yunyang (Mainland)专业流利，新闻

// Xiaobei (Northeastern Mandarin)东北女声
// Xiaoni (Zhongyuan Mandarin Shaanxi)陕西话
//
// HsiaoChen (Taiwan)台湾绵女声，不区分多音字
// YunJhe (Taiwan)
// HsiaoYu (Taiwanese Mandarin)

window.voices_styles = {
    'male': v => /Yunjian|Yunxi |Yunyang|YunJhe|HsiaoYu/.test(v.name),
    'female': v => /Xiaoxiao|Xiaoyi|HsiaoChen|Yaoyao|Kangkang|Huihui/.test(v.name),
    'kid': v => /Yunxia/.test(v.name),
    'clear': v => /Yunyang|Yunjian|Xiaoxiao/.test(v.name),
    'sun': v => /Yunxi /.test(v.name),
    'soft': v => /HsiaoChen/.test(v.name),
    'special': v => /Xiaobei|Xiaoni/.test(v.name),
    'local': v => v.localService === true,
};
if (speechSynthesis.onvoiceschanged !== undefined) {
    speechSynthesis.onvoiceschanged = function () {
        if (window.voices.length > 0) return;
        // const isEdge = navigator.userAgent.indexOf('Edge') !== -1;
        // const isChrome = navigator.userAgent.indexOf('Chrome') !== -1;
        // const isSafari = navigator.userAgent.indexOf('Safari') !== -1;
        // const isFirefox = navigator.userAgent.indexOf('Firefox') !== -1;
        // const isOpera = navigator.userAgent.indexOf('Opera') !== -1;
        // const isIe = navigator.userAgent.indexOf('MSIE') !== -1;
        // const isIe11 = !!window.MSInputMethodContext && !!document.documentMode;
        window.voices = speechSynthesis.getVoices()
            .filter((v, i) => ['zh-CN', 'zh-CN-shaanxi', 'zh-CN-liaoning', 'zh-TW'].includes(v.lang))
            .sort((a, b) => b.name.includes('Online') - a.name.includes('Online'));
        console.log(voices);
        setTimeout(() => typeof initVoices === 'function' && initVoices(), 10);
    };
}

function speak({text = "", voice = "sun", end = 0, rate = 1, pitch = 1, volume = 1, event} = {}) {
    text = text || event.target.innerText;
    if (!text) {
        console.warn('no text');
        return;
    }
    console.log('start speak...');
    var msg = new SpeechSynthesisUtterance(text);
    if (end)
        msg.onend = function (event) {
            console.log("SpeechSynthesisUtterance.onend");
            end();
        };
    if (typeof voice === 'number') {
        msg.voice = window.voices[voice] || window.voices[0];
    } else {
        const f = typeof voice === 'string' ? voices_styles[voice] || (v => v.name.includes(voice)) : voice;
        msg.voice = window.voices.find(f) || window.voices[0];
    }
    msg.rate = rate; // 0.5~2
    msg.pitch = pitch; // 0~2 free online voice不支持
    msg.volume = volume; // 0~1
    speechSynthesis.speak(msg);
}
