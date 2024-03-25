// ==UserScript==
// @name         styles
// @version      0.1
// @description  调整样式，hidden一些block
// @author       Cqiu
// @match        https://yiyan.baidu.com/*
// @match        https://poe.com/*
// @match        https://chat.360.cn/*
// @match        https://search.tiangong.cn/*
// @match        https://chatglm.cn/main/detail
// @match        https://gemini.google.com/*
// @grant       GM_addStyle
// @grant       unsafeWindow
// ==/UserScript==

let weburl = unsafeWindow.location.href
if (weburl.indexOf('yiyan.baidu.com') != -1) {
    GM_addStyle(`.ZWgOKikC,.Bas_pjOu{display:none !important}.NYG3ffBi{opacity: 0.1;}
    .Fj0bBRFn{padding:0 !important}
    .xgTDL7D_{height: 100%;padding-bottom: 0;}
    .usD7jE7m{height: calc(100vh - 110px);padding: 10px 0 0 40px;}`)
}else if (window.location.hostname.includes('poe.com')) {
    GM_addStyle(`
    .HeaderBar_header__ydlc6{display:none;}
    .SidebarLayout_left__Ew2CE{width: 200px}
    .BaseNavbar_chatTitleNavbar__5zghX{display:none}
    .Button_buttonBase__Bv9Vx{height:10px}
    .PageWithSidebarLayout_leftSidebar__Y6XQo{width:100px}`)
}else if (window.location.hostname.includes('gemini.google.com')) {
    GM_addStyle(`
    .boqOnegoogleliteOgbOneGoogleBar{display:none;}
    .capabilities-disclaimer{display:none!important}
    bard-sidenav-content bard-mode-switcher{margin: 0px 10px!important;height: 14px;}
    .actions-container-v2{margin: 0;height:10px!important;}
    .response-container.ui-v2-enabled{padding-bottom:0!important}
    .markdown.ui-v2-enabled{line-height: 1.25rem!important}
    .response-container-footer{padding:0!important;margin:0!important;height:14px!important}
    .response-container-footer *{padding:0!important;margin:0!important;}
    `)
}else if (window.location.hostname.includes('chat.360.cn')) {
    GM_addStyle(`
    #nworld-app-container > div > div.page-index.w-full.h-full.flex > div.main-right-container.flex.w-0.flex-grow.flex-col > div.h-0.flex-grow > div > div.relative.flex-1.h-full.min-w-310px > div.z-9.absolute.bottom-0.left-0.right-0.h-200px.\!\<sm\:h-150px.pointer-events-none > div > div > section{display:none}
    `)
}else if (window.location.hostname.includes('chatglm.cn')) {
    GM_addStyle(`
    .el-container > aside.aside{width: 67px!important}
    aside.scroll-display-none{width: 67px!important}
    .main > .detail-container{min-width: 800px}
    .policy-wrap{display:none !important}
    `)
}else if (window.location.hostname.includes('search.tiangong.cn')) {
    GM_addStyle(`
    header{    opacity: 0.7;}
    #s-input-wrapper{padding:0}
    @media (min-width: 768px) {.el-input-wrapper[data-v-2d700868] {min-height: 34px;padding: 0px 72px 0px 78px;}}
    `)
}