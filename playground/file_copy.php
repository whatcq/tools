<?php

function replaceWithCase($search, $replace, $text, $pos = 0)
{
    if (is_array($search)) {
        foreach ($search as $k => $s) {
            $text = replaceWithCase($s, $replace[$k], $text, $pos);
        }
        return $text;
    }

    while (($pos = strpos(strtolower($text), strtolower($search), $pos)) !== false) {
        $substr = mb_substr($text, $pos, strlen($search));
        $remaining = mb_substr($text, $pos + strlen($search));

        if (ctype_upper($substr)) {
            $text = substr_replace($text, strtoupper($replace), $pos, strlen($search));

            continue;
        }

        $substrParts = preg_split('//u', $substr, null, PREG_SPLIT_NO_EMPTY);
        $replaceParts = preg_split('//u', $replace, null, PREG_SPLIT_NO_EMPTY);
        $newWord = '';

        foreach ($replaceParts as $k => $rp) {
            if (array_key_exists($k, $substrParts)) {
                $newWord .= ctype_upper($substrParts[$k]) ? mb_strtoupper($rp) : mb_strtolower($rp);
            } else {
                $newWord .= $rp;
            }
        }
        $text = substr_replace($text, $newWord, $pos, strlen($search));
        $pos = $pos + strlen($search);
    }

    return $text;
}

$pairs = [
    '举报' => '小组',
    '帖子分类管理' => '小组管理',
    'complaint' => 'group',
];

$search = array_keys($pairs);
$replace = array_values($pairs);

// 示例使用
$text = file_get_contents('./校园集市-cate.Apifox.json');
$newText = replaceWithCase($search, $replace, $text);
file_put_contents('./校园集市-group_cate.Apifox.json', $newText);
