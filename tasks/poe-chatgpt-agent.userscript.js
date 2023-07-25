// ==UserScript==
// @name         poe-chatGPT-hook
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       Cqiu
// @match        https://poe.com/*
// @icon         https://www.google.com/s2/favicons?sz=64&domain=poe.com
// @grant        none
// ==/UserScript==

(function () {
    console.log('hook websocket!');

    // 创建 WebSocket 原型对象的引用
    var WS = window.WebSocket;

    // 保存答案：重写 WebSocket 构造函数
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


    // 保存问题：覆盖fetch()方法
    var oldFetch = window.fetch;
    window.fetch = function () {
        var url = arguments[0];
        var options = arguments[1];
        if (/api\/gql_POST/.test(url)) {
            // print request data
            console.log('[fetch]', url, options);
        }
        return oldFetch.apply(this, arguments).then(function (response) {
            console.log('[fetch]', url, response.headers, response.clone().text());
            return response;
        });
    };

    // @todo 输入问题:如何操作react的界面元素？触发方法。
})();
