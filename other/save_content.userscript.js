// ==UserScript==
// @name         Save Content
// @namespace    http://tampermonkey.net/
// @version      0.2
// @description  Cache Scys.com page content and export as JSON
// @author       Your Name
// @match        https://scys.com/view/docx/*
// @grant        GM_setValue
// @grant        GM_getValue
// @grant        GM_download
// ==/UserScript==

(function() {
    'use strict';

    const contentSelector = 'div.block-wrapper';
    let pageContent = {};

    function cacheContent() {
        const contentElements = document.querySelectorAll(contentSelector);
        contentElements.forEach(element => {
            const index = element.getAttribute('index');
            if (!pageContent[index]) {
                pageContent[index] = element.innerHTML.replace(/<!---->/g, '');
            }
        });
    }

    function exportToJSON() {
        // console.log(1234)
        const content = `<link rel="stylesheet" crossorigin="" href="https://search01.shengcaiyoushu.com/test/assets/index-CpiLVP6q.css">` +
            Object.values(pageContent).join('');
        const blob = new Blob([content], {type: 'text/plain;charset=utf-8'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = document.title.replaceAll(/[<>:"\/\\|?*]/g, '').trim() + '.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        // const filename = 'scys-content.json';
        // // const data = JSON.stringify(pageContent, null, 2);
        // const data = Object.entries(pageContent).join('');
        // const blob = new Blob([data], {type: 'text/plain;charset=utf-8'});
        // const url = URL.createObjectURL(blob);
        // // GM_download(url, filename);
        // GM_download({
        //     url: url,
        //     name: filename,
        //     saveAs: true
        // });
    }

    function initObserver() {
        const targetNode = document.querySelector('#app > div > div:nth-child(1) > div > div.docx-page > div.wrap');
        const config = { childList: true, subtree: true };

        const observer = new MutationObserver((mutationsList, observer) => {
            if (mutationsList.length === 0) return;
            cacheContent();
        });

        observer.observe(targetNode, config);
    }
    function initScript() {
        cacheContent();

        const exportButton = document.createElement('button');
        exportButton.textContent = '保存';
        exportButton.style = 'background:lightblue;position: fixed; bottom: 20px; right: 20px;';
        exportButton.addEventListener('click', exportToJSON);
        document.body.appendChild(exportButton);

        // window.addEventListener('scroll', cacheContent, true); // 不行?
        initObserver();
    }

    // document.addEventListener('DOMContentLoaded', initScript);
    setTimeout(initScript, 2000);
})();