// ==UserScript==
// @name         hacker vision
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  hacker vison under dark env!
// @author       Cqiu
// @match        https://blog.csdn.net/*
// @match        *://*/*
// @grant        unsafeWindow
// ==/UserScript==

(function() {
    'use strict';

    function loadCssCode(code){
        var style = document.createElement('style');
        style.type = 'text/css';
        style.rel = 'stylesheet';
        try{
            //for Chrome Firefox Opera Safari
            style .appendChild(document.createTextNode(code));
        }catch(ex){
            //for IE
            style.styleSheet.cssText = code;
        }
        var head = document.getElementsByTagName('head')[0];
        head.appendChild(style);
    }
    loadCssCode('html:not(*:-webkit-full-screen) body, html:not(*:-webkit-full-screen) {-webkit-filter: contrast(91%) brightness(84%) invert(1);background: rgb(9, 9, 9) !important;}');

})();
