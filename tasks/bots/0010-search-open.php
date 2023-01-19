<?php

$urls = [
    '抖音' => 'https://www.douyin.com/search/{key}?source=normal_search&aid=c2420552-601e-4aed-b61c-1921b05d0936&enter_from=recommend',
    '哔哩哔哩' => 'https://search.bilibili.com/all?keyword={key}&from_source=banner_search',
    '百度' => 'https://www.baidu.com/s?wd={key}',
    '搜狗' => 'https://www.sogou.com/web?query={key}',
    '必应' => 'http://cn.bing.com/search?q={key}',
    '微博' => 'http://s.weibo.com/weibo/{key}&Refer=index',
    '京东' => 'https://search.jd.com/Search?keyword={key}&enc=utf-8&wq={key}&pvid=bbcec2b5192949f894bba48b00042318',
    '淘宝' => 'http://s.taobao.com/search?q={key}',
    '天猫' => 'https://s.taobao.com/search?fromTmallRedirect=true&tab=mall&q={key}&spm=875.7931836%2FB.a2227oh.d100',
    '百科' => 'https://baike.baidu.com/item/%E7%8E%8B%E5%B0%8F%E5%B7%9D/7813874?fromModule=lemma_search-box', //https://baike.baidu.com/search/none?word={key}&pn=0&rn=10&enc=utf8&fromModule=lemma_search-box',
    '知道' => 'http://zhidao.baidu.com/search?lm=0&rn=10&pn=0&fr=search&word={key}',
    '地图' => 'http://map.baidu.com/m?ie=utf-8&fr=bks0000&word={key}',
    '翻译' => 'https://fanyi.so.com/#{key}',
];

/** @var $text string */
if (str_starts_with($text, '打开')) {
    $_text = mb_substr($text, 2);
    foreach ($urls as $site => $url) {
        if (str_starts_with($_text, $site)) {
            $url = str_replace('{key}', trim(mb_substr($_text, mb_strlen($site)), '，。'), $url);
            die('<script>parent.input.value="";window.open("' . $url . '")</script>');
        }
    }
}

return;
