// ==UserScript==
// @name         chatgpt-agent
// @version      1.0
// @description  agent web chatgpt to my service
// @match        *://*/*
// @grant        GM_xmlhttpRequest
// @connect      *
// ==/UserScript==

(function () {
    'use strict';

    console.log('ajax intercept');
    // @todo connect ssr to get directive

    let myService = 'http://localhost/cqiu/tools/tasks/chatgpt-agent-service.php';
    // let inputBox = document.querySelector('#Top > section:nth-child(1) > div.fucView.flex.justifyCenter > div > div > textarea');
    // let sendButton = document.querySelector('.el-icon-s-promotion');

    setTimeout(() => {
        var chat = new window.EventSource(myService + "?act=get_question");
        chat.onmessage = function (e) {
            console.log(e.data);
            // inputBox.value = e.data;
            // sendButton.click();
            $vm.question = e.data;
            $vm.sureSendWay();
        };
    }, 500);

    let saveResponse = function (resp) {
        GM_xmlhttpRequest({
            method: "POST",
            url: myService + "?act=save_response",
            data: resp,
            onload: function (response) {
                console.log('保存的结果：', response);
            },
            onerror: function (error) {
                console.log('保存的结果err：', error);
            }
        });
    }

    //----------------
    // 覆盖XMLHttpRequest.prototype.send()方法
    var oldXHR = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send = function () {
        var self = this;
        var oldOnReadyStateChange;
        var url = arguments[0];

        function onReadyStateChange() {
            if (self.readyState == 4 /* complete */) {
                if (/\/v1\/chat\/result/.test(self.responseURL)) {
                    saveResponse(self.responseText);
                    // console.log('[XHR]', url, self.getAllResponseHeaders(), self.responseText);
                    console.log('URL: ' + self.responseURL);
                    // console.log('Headers: ' + self.getAllResponseHeaders());
                    console.log('Response: ' + self.responseText);
                }
            }

            if (oldOnReadyStateChange) {
                oldOnReadyStateChange();
            }
        }

        if (self.addEventListener) {
            self.addEventListener("readystatechange", onReadyStateChange, false);
        } else {
            oldOnReadyStateChange = self.onreadystatechange;
            self.onreadystatechange = onReadyStateChange;
        }

        oldXHR.apply(this, arguments);
    };

    // 覆盖fetch()方法
    var oldFetch = window.fetch;
    window.fetch = function () {
        var url = arguments[0];
        return oldFetch.apply(this, arguments).then(function (response) {
            console.log('[fetch]', url, response.headers, response.text());
            return response;
        });
    };

})();