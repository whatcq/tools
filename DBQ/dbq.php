<?php

session_start();
header("Access-Control-Allow-Origin: *");

require '../lib/DB.php';

$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
$fetchType = isset($_REQUEST['fetchType']) ? $_REQUEST['fetchType'] : PDO::FETCH_ASSOC;
$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : null;
$link = isset($_REQUEST['link']) ? $_REQUEST['link'] : (isset($_COOKIE['link']) ? $_COOKIE['link'] : null);

if (!$link) {
    function parseDsn($str)
    {
        return [
            'HOST' => '127.0.0.1',
            'NAME' => 'zentao_prod',// foreabay_msr
            'USER' => 'root',
            'PASS' => '',
            'CHAR' => 'UTF8',
        ];
    }
    $dsn = parseDsn($q);
    if (!$dsn) {
        res(0, null, 'parse link failed!');
    }
    $info = serialize($dsn);
    $link = md5($info);
    $_SESSION[$link] = $info;
    setcookie('link', $link);
    $q = '#%';
    $fetchType = PDO::FETCH_COLUMN;
}

if (!isset($_SESSION[$link])) {
    res(0, null, 'link not set!');
}

isset($dsn) or $dsn = unserialize($_SESSION[$link]);
define('DB_HOST', $dsn['HOST']);
define('DB_NAME', $dsn['NAME']);
define('DB_USER', $dsn['USER']);
define('DB_PASS', $dsn['PASS']);
!empty($dsn['CHAR']) && define('DB_CHAR', $dsn['CHAR']) && DB::x('SET NAMES "' . DB_CHAR . '"');

$shows = [
    'TABLES',
    'DATABASES',
    'STATUS',
    'PROFILES',
    'PROCESSLIST',
    'VARIABLES',
    'GRANTS',
    'PRIVILEGES',
    'GLOBAL VARIABLES',
    'SLAVE HOSTS',
    'SLAVE STATUS',
    'RELAYLOG EVENTS limit 10',
    'EVENTS',
    'CHARACTER SET',
    'MASTER STATUS',
    'MASTER LOGS',
    'BINARY LOGS',
    'ENGINES',
];
// common sqls
$sqls = [
    '#*' => ':tables info',
    '#user%' => ':tables like',
    '-%user_id' => ':tables having the column',
    'u' => ':user data limit 30',
    'u#' => ':count user data',
    'u#2' => ':query user data',
    'u#*' => ':table structure',
    'u#~' => ':PROCEDURE ANALYSE',
];

// 获取主键字段名
function getPk($table)
{
    $_sql = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
    $pkField = null;
    if ($pkInfo = DB::q($_sql)->fetch(PDO::FETCH_ASSOC)) {
        $pkField = $pkInfo['Column_name'];
    }
    return $pkField;
}

//解析请求，返回sql+params
function parse($q)
{
    global $show;
    if ($q === '#*') {
        return ['SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS n,AVG_ROW_LENGTH l,INDEX_LENGTH idx,AUTO_INCREMENT i,TABLE_COLLATION,CREATE_TIME,UPDATE_TIME,TABLE_TYPE type,ENGINE e,ROW_FORMAT FROM information_schema.`TABLES` where TABLE_SCHEMA=?s', DB_NAME];
    }

    // table alias
    $tables = [
        'u' => 'user',
        'o' => 'order',
    ];

    $tmp = explode('#', $q);
    $table = $tmp[0];
    $var = isset($tmp[1]) ? $tmp[1] : null;

    if (!$table) {
        return ['SHOW TABLES LIKE ?s', $var];
    }

    if ($q[0] === '-') {
        return [
            'SELECT TABLE_SCHEMA,TABLE_NAME,COLUMN_NAME FROM information_schema.columns WHERE TABLE_SCHEMA=?s AND COLUMN_NAME LIKE ?s',
            DB_NAME,
            substr($q, 1),
        ];
    }

    if (isset($tables[$table])) {
        $table = $tables[$table];
    }

    if (!preg_match('/^[a-zA-Z\$_][a-zA-Z\d_]*$/i', $table)) {
        throw new Exception("Error table name", 1);
    }

    $params = [];
    if ($var) {
        if ($var === '*') {
            return ["SHOW FULL FIELDS FROM $table"];
            return [
                'SELECT COLUMN_NAME,COLUMN_COMMENT,COLUMN_DEFAULT default_,IS_NULLABLE nul,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH m,CHARACTER_OCTET_LENGTH n,COLUMN_KEY,EXTRA FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA=?s AND TABLE_NAME=?s', //,CHARACTER_SET_NAME c,COLLATION_NAME,COLUMN_TYPE
                DB_NAME,
                $table,
            ];
        } elseif ($var === '~') {
            return ["SELECT * FROM $table PROCEDURE ANALYSE(1, 10)"];
        }
        $sql = "SELECT * FROM $table";
        $pkField = getPk($table);
        $pkField or $pkField = 'id';
        $sql .= " WHERE $pkField=?i";
        $params = [$var];
    } elseif (is_null($var)) {
        $orderBy = '';
        $offset = 0;
        $limit = 30;
        if ($show) {
            if (strpos($show, ',') !== false) {
                list($offset, $limit) = explode(',', $show);
                $offset = (int) $offset;
                $limit = (int) $limit;
            } elseif ($show < 0) {
                $pkField = getPk($table);
                $orderBy = " ORDER BY $pkField DESC";
                $limit = abs($show);
            }
        }
        $sql = "SELECT * FROM $table{$orderBy} LIMIT $offset,$limit";
    } else {
        $sql = "SELECT COUNT(*) FROM $table";
    }

    return array_merge([$sql], $params);
}

function res($status = 0, $data = null, $message = '')
{
    header('Content-type: application/json');
    header("Content-type:text/html;charset=utf-8");
    die(json_encode([
        'status' => $status,
        'info'=>$_REQUEST,
        'cookie'=>$_COOKIE,
        'data' => $data,
        'message' => $message,
    ]));
}

if ($show || $q) {
    if (in_array($show, $shows)) {
        $w = 'SHOW ' . $show;
        $r = DB::q($w);
    } else {
        $w = parse($q);
        $r = call_user_func_array('DB::q', $w);
        $w = json_encode($w);
    }
    $d = $r->fetchAll($fetchType);
    res(1, $d, $w);
}

res();
