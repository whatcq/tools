// ==UserScript==
// @name         chatgpt-agent
// @version      1.0
// @description  agent web chatgpt to my service
// @match        *://chatgptbot.space/*
// @match        *://chatgptbot.me/*
// @match        *://chatgptbot.cc/*
// @grant        GM_xmlhttpRequest
// @connect      *
// ==/UserScript==
// #include      /\.*:\/\/chatgpt.*\/.*/

(function () {
    'use strict';

    console.log('ajax intercept');

    let myService = 'http://localhost/cqiu/tools/tasks/chatgpt-agent-service.php';

    setTimeout(() => {
        var chat = new window.EventSource(myService + "?act=get_question");
        chat.onmessage = function (e) {
            console.log(e.data);
            let _vm = document.querySelector('#Top').__vue__;
            console.log(_vm)
            if (typeof _vm == 'undefined') {
                console.error('_vm is undefined');
                return;
            }
            _vm.question = e.data;
            _vm.sureSendWay();
        };
    }, 500);

    let saveResponse = function (resp) {
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
                    // console.log('URL: ' + self.responseURL);
                    // console.log('Response: ' + self.responseText);
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