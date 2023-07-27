// ==UserScript==
// @name         poe-chatGPT-hook
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       Cqiu
// @match        https://poe.com/*
// @icon         https://www.google.com/s2/favicons?sz=64&domain=poe.com
// @grant        unsafeWindow
// @run-at       document-start
// ==/UserScript==

/*
@grant none 表示直接在页面中运行，这就不能用GM_*函数
      unsafeWindow 表示在Tampermonkey沙箱中运行，可以用GM_*函数
*/

(function () {
    console.log('hook websocket!');

    const uWin = unsafeWindow;

    // 创建 WebSocket 原型对象的引用
    var WS = uWin.WebSocket;

    // 保存答案：重写 WebSocket 构造函数
    uWin.WebSocket = function() {
        let url = arguments[0];
        let protocols;
        let options;

        // 处理参数形式
        if (arguments.length > 1) {
            protocols = arguments[1];
        }
        if (arguments.length > 2) {
            options = arguments[2];
        }

        // 调用原始 WebSocket 构造函数
        var ws = new WS(url, protocols, options);
        // 加上message监听
        ws.addEventListener("message", (event) => {
            console.log("===> ", event.data);
        });

        // 返回 WebSocket 实例
        return ws;
    };

    // 保存问题：覆盖fetch()方法
    var oldFetch = uWin.fetch;
    uWin.fetch = function () {
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
