// ==UserScript==
// @name         poe-chatGPT-hook
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  try to take over the world!
// @author       Cqiu
// @match        https://poe.com/*
// @icon         https://www.google.com/s2/favicons?sz=64&domain=poe.com
// @connect      localhost
// @grant        unsafeWindow
// @grant        GM_xmlhttpRequest
// @run-at       document-start
// ==/UserScript==

/*
@grant none 表示直接在页面中运行，这就不能用GM_*函数
      unsafeWindow 表示在Tampermonkey沙箱中运行，可以用GM_*函数
*/

(function () {
    console.log('hook websocket!');

    const uWin = unsafeWindow;

    const myService = 'http://localhost/cqiu/tools/tasks/chatgpt-agent-service.php';

    let savedData;
    let saveResponse = function (resp) {
        if (savedData === resp) {
            return;
        }
        savedData = resp;
        GM_xmlhttpRequest({
            method: "POST",
            url: myService + "?act=save_response",
            data: resp,
            onload: function (response) {
                console.log('保存的结果：', response.responseText);
            },
            onerror: function (error) {
                function strip(html) {
                    let doc = new DOMParser().parseFromString(html, 'text/html');
                    return doc.body.textContent || "";
                }
                console.log('保存的结果err：', strip(error.responseText));
            }
        });
    }

    // 创建 WebSocket 原型对象的引用
    var WS = uWin.WebSocket;

    // 保存答案：重写 WebSocket 构造函数
    uWin.WebSocket = function () {
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
            // console.log("===> ", event.data);
            try {
                let payload = JSON.parse(JSON.parse(event.data).messages[0]).payload;
                if ("complete" == payload.data.messageAdded.state && !payload.data.messageAdded.clientNonce) {
                    saveResponse(payload.data.messageAdded.text);
                }
            } catch { }
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
            const data = JSON.parse(options.body);
            if (data.variables && typeof data.variables.query === 'string' && data.variables.query.length > 0) {
                saveResponse('======>' + data.variables.query);
            }
        }
        return oldFetch.apply(this, arguments);/*.then(function (response) {
            console.log('[fetch]', url, response.headers, response.clone().text().then(data => data));
            return response;
        });*/
    };

    // 输入问题:如何操作react的界面元素？触发方法。
    setTimeout(() => {
        // fix CSP:
        // Tampermonkey > 设置 > 配置模式：高级 > 安全:找到 "Modify existing content security policy (CSP) headers" (修改现有内容安全策略 (CSP) 头部)，将其设置为 "Remove entirely (possibly unsecure)" (完全移除 (可能不安全)) 或 "Yes" (是)。保存！
        // pub_sub.exe -port 1985
        var chat = new window.EventSource('http://127.0.0.1:1985/subscribe?channel=question');//(myService + "?act=get_question");
        chat.onmessage = function (e) {
            console.log(e.data);
            const data = JSON.parse(e.data);
            // return;
            // #__next > div > div.AnnouncementWrapper_container__Z51yh > div > main > div > div > div > div:nth-child(1) > div > div.ChatHomeMain_inputContainer__9mgRh > div > div.GrowingTextArea_growWrap__im5W3.ChatMessageInputContainer_textArea__fNi6E > textarea
            document.querySelector('#__next > div.PageWithSidebarLayout_centeringDiv___L9br > div > section > div.PageWithSidebarLayout_scrollSection__IRP9Y.PageWithSidebarLayout_startAtBottom__wKtfz > div > div > footer > div > div > div.GrowingTextArea_growWrap___1PZM.ChatMessageInputContainer_textArea__kxuJi > textarea')
                .value = data.message;
            // #__next > div > div.AnnouncementWrapper_container__Z51yh > div > main > div > div > div > div:nth-child(1) > div > div.ChatHomeMain_inputContainer__9mgRh > div > div.ChatMessageInputContainer_actionContainerRight__fyfsX.ChatMessageInputContainer_actionContainerBase__8BKrX > button
            document.querySelector('#__next > div.PageWithSidebarLayout_centeringDiv___L9br > div > section > div.PageWithSidebarLayout_scrollSection__IRP9Y.PageWithSidebarLayout_startAtBottom__wKtfz > div > div > footer > div > div > button.Button_buttonBase__0QP_m.Button_primary__pIDjn.ChatMessageSendButton_sendButton__OMyK1.ChatMessageInputContainer_sendButton__s7XkP')
                .click();
        };
    }, 500); /**/
})();
