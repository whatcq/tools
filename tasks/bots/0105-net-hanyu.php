<?php

$html = file_get_contents('https://hanyu.sogou.com/result?query=' . urlencode($keyword));
file_put_contents('hanyu.sogou.html', $html); // for test
if (strpos($html, '抱歉，没有找到')) {
    $responseText = '--';
} else {
    preg_match('#<div id="shiyiDiv".*>(.*)</div>#i', $html, $matches);
    $responseText = strip_tags($matches[0]);
}
