<?php

require_once __DIR__ . '/../../lib/functions.php';

/* @var $text string */
if (
    !empty($_SESSION['mode'])
    && strlen($text) < 30
    && in_array($text, ['不玩了', '退出', '我不想玩了', '再见', '拜拜', '喜欢土豆。', '我喜欢土豆。'])
) {
    $_SESSION['mode'] = null;
    return '好的！bye';
}

if (strpos($text, '开始成语接龙') !== false) $_SESSION['mode'] = '成语接龙';
elseif (strpos($text, '开始声律启蒙') !== false && strlen($text) < 30) $_SESSION['mode'] = '声律启蒙';
elseif (strpos($text, '在搞啥子') !== false && strlen($text) < 30) $_SESSION['mode'] = '助手';

return; // 一定要，否则默认会return true;
