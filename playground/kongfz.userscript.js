// ==UserScript==
// @name         Copy书
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Copy content from a page
// @author       Cqiu
// @match        http*://item.kongfz.com/book/*.html
// @match        http*://book.kongfz.com/*/*
// @icon         https://www.google.com/s2/favicons?sz=64&domain=kongfz.com
// @grant        GM_addStyle
// @grant        GM.setClipboard
// @require      https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js
// ==/UserScript==

(function () {
    'use strict';

    // 添加按钮样式
    GM_addStyle(`
        #copy-button {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 240px;
            height: 210px;
            background-color: #4CAF50;
            color: white;
            font-size: 10px;
            line-height: 1.5;
        }
    `);

    if (window.location.hostname.includes('item.kongfz.com')) {
        var title = $('.detail-title').text()
            , pub = $('span[itemprop="bookFormat"]').next().text()
            , author = $('.zuozhe .text-value').text().replace(/\s+/g, ' ')
            , isbn = $('span[itemprop="isbn"]').next().text();
    } else {
        var title = $('h1.title').text().replace(/\s+/g, ' ');
        var getInfo = function () {
            var text = $(this).text();
            if (text.includes('出版社')) {
                return pub = text.replace(/出版社:/g, ' ').replace(/\s+/g, ' ');
            }
            if (text.includes('作者')) {
                return author = text.replace(/作者:/g, ' ').replace(/\s+/g, ' ');
            }
            if (text.includes('ISBN')) {
                return isbn = text.replace(/ISBN:/g, ' ').replace(/\s+/g, ' ');
            }
        };
        if ($('.keywords-define-1200').length > 0) {
            $('.keywords-define-1200 li').each(getInfo);
        } else if ($('.detail-list1').length > 0) {
            $('.detail-list1 li').each(getInfo);
        } else {
            pub = '';
            author = '';
            isbn = '';
        }
    }

    var pairs = {
        '标题': title
        , '标题2': title.replace(/\/.*/g, '')
        , '出版社': pub
        , '作者': author
        , 'ISBN': isbn
    };

    var tpl = `
《${pairs['标题2']}》
【正版二手包邮】

…………………………………………………………
【书名】：${pairs['标题']}
【出版社】：${pairs['出版社']}
【作者】：${pairs['作者']}
【ISBN编码】：${pairs['ISBN']}
`;
    // 添加按钮到页面
    $('body').append(`<textarea id="copy-button" >${tpl}</textarea>`);

    // 绑定按钮的点击事件
    $('#copy-button').focus(function () {
        GM.setClipboard($(this).val());
        console.log('Content copied to clipboard!');
        $(this).css('background-color', 'green');
    });
})();
