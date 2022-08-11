<?php

$botName = 'sogou汉语';

$html = file_get_contents('https://hanyu.sogou.com/result?query=' . urlencode($keyword));
file_put_contents('hanyu.sogou.html', $html); // for test

if (strpos($html, '抱歉，没有找到')) {
    return '--';
}

preg_match('#<div id="shiyiDiv".*>(.*)</div>#i', $html, $matches);
return strip_tags($matches[0]);
