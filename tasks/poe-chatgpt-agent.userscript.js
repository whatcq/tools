// ==UserScript==
// @name         poe-chatGPT-hook
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       Cqiu
// @match        https://poe.com/
// @icon         https://www.google.com/s2/favicons?sz=64&domain=poe.com
// @grant        none
// ==/UserScript==

(function () {
    console.log('hook websocket!');

    // 创建 WebSocket 原型对象的引用
    var WS = window.WebSocket;

    // 重写 WebSocket 构造函数
    window.WebSocket = function (url, protocols) {
        // 创建 WebSocket 实例
        var ws = new WS(url, protocols);

        // 加上message监听
        ws.addEventListener("message", (event) => {
            console.log("===> ", event.data);
        });

        // 返回 WebSocket 实例
        return ws;
    };
})();
