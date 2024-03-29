<?php

if (empty($_SESSION['mode']) || $_SESSION['mode'] !== '成语接龙') {
    return;
}

$botName = '成语接龙';

require_once __DIR__ . '/../../lib/DB.php';

$links = [
    'local' => [
        'host' => '127.0.0.1',
        'database' => 'zici',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ]
];

$link = 'local';
$config = $links[$link];
isset($links[$config['extend'] ?? '']) && $config = array_merge($links[$config['extend']], $config);
isset($config['port']) && $config['host'] .= ':' . $config['port'];
define('DB_HOST', $config['host']);
define('DB_NAME', $config['database']);
define('DB_USER', $config['username']);
define('DB_PASS', $config['password']);
isset($config['charset']) && define('DB_CHAR', $config['charset']);
DB::x('SET NAMES "' . DB_CHAR . '"');

if (empty($_SESSION['step'])) {
    $_SESSION['step'] = 1;
    return '成语接龙开始！你先！';
}

$keyword = $text;

function jielong($keyword)
{
    $items = DB::q('SELECT chengyu FROM chengyu WHERE chengyu LIKE ?s', mb_substr($keyword, -1) . '%')->fetchAll(PDO::FETCH_ASSOC);
    if (!$items) return null;
    return $_SESSION['chengyu'] =  current($items[array_rand($items)]);
}

if (in_array(trim($keyword, '。 '), ['不玩了', '退出', '我不想玩了'])) {
    $_SESSION['mode'] = null;
    $_SESSION['chengyu'] = null;
    $_SESSION['error'] = 0;
    return '我也不想玩了！bye';
}

if (in_array($keyword, ['重来'])) {
    $_SESSION['chengyu'] = null;
    $_SESSION['error'] = 0;
    return '好吧，你说';
}


if (!empty($_SESSION['chengyu']) && mb_substr($keyword, 0, 1) !== $zi = mb_substr($_SESSION['chengyu'], -1)) {
    $_SESSION['error'] = ($_SESSION['error'] ?? 0) + 0.5;

    if ($_SESSION['error'] > 2) {
        goto ERROR;
    }

    return ["需要“{$zi}”开头的", "你这不是{$zi}字开头的呀~"][mt_rand(1, 2) - 1];
}

if (!DB::q('SELECT chengyu FROM chengyu WHERE chengyu=?s', $keyword)->fetch(PDO::FETCH_ASSOC)) {
    $_SESSION['error'] = ($_SESSION['error'] ?? 0) + 1;

    if ($_SESSION['error'] > 2) {
        goto ERROR;
    }

    return ['你这是成语吗？重来一个', '再试一下'][$_SESSION['error'] - 1];
}

$next = jielong($next);
if (!$next) {
    $_SESSION['chengyu'] = null;
    return $msg . '……，然后呢，我接不上啦，呜呜！还是重来吧，你先';
}
return $next;

ERROR:
if (empty($_SESSION['chengyu'])) {
    return '你就一个成语没说，还是我开始吧：'
        . $_SESSION['chengyu'] = current(DB::q('SELECT chengyu FROM chengyu WHERE id=?i', mt_rand(1, 30804))->fetch(PDO::FETCH_ASSOC));
}
$next = jielong($_SESSION['chengyu']);
if (!$next) {
    $zi = mb_substr($_SESSION['chengyu'], -1);
    $_SESSION['chengyu'] = null;
    return '算啦！没有' . $zi . '字开头的成语。重来，你先。';
}
$msg = '我帮你想到一个：' . $next;
$_SESSION['error'] = 0;
$next = jielong($next);
if (!$next) {
    $_SESSION['chengyu'] = null;
    return $msg . '……，然后呢，我接不上啦，呜呜！还是重来吧，你先';
}
return $msg . '，' . $next;
