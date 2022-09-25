<?php

session_start();
header("Access-Control-Allow-Origin: *");

require '../lib/DB.php';

//======= ajax request
$act = $_GET['act'] ?? '';
if ($act) {
    $data = (function () use ($act) {
        $links = [
            'local' => [
                'HOST' => '127.0.0.1:33066',
                'NAME' => 'zici',
                'USER' => 'root',
                'PASS' => '',
                'CHAR' => 'utf8',
            ],
        ];
        file_exists($configFile = '../playground/config2.database.php')
        && $links = array_merge($links, include $configFile);
        if ($act === 'links') {
            return array_keys($links);
        }

        $link = $_GET['link'] ?? '';
        if (!isset($links[$link])) {
            return null;
        }

        define('DB_HOST', $links[$link]['HOST']);
        define('DB_NAME', 'information_schema');
        define('DB_USER', $links[$link]['USER']);
        define('DB_PASS', $links[$link]['PASS']);
        $dbInstance = DB::instance(true);
        if ($dbInstance instanceof PDOException) {
            return $dbInstance->getMessage();
        }
        !empty($links[$link]['CHAR']) && DB::x('SET NAMES "' . $links[$link]['CHAR'] . '"');
        if ($act === 'dbs') {
            return DB::q('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
        }

        if ($act === 'tables') {
            $table = $_GET['table'] ?? '';
            $matchField = preg_match('#^[\w_\d]+$#i', $table) ? 'TABLE_NAME' : 'TABLE_COMMENT';
            return DB::q("SELECT 
  `TABLE_SCHEMA`,`TABLE_NAME`,`TABLE_COMMENT`
FROM
  `information_schema`.`TABLES` 
WHERE `TABLE_SCHEMA` NOT IN (
    'information_schema',
    'performance_schema',
    'sys',
    'mysql'
  ) 
  AND $matchField LIKE '%$table%'")->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($act === 'fields') {
            $field = $_GET['field'] ?? '';
            $matchField = preg_match('#^[\w_\d]+$#i', $field) ? 'COLUMN_NAME' : 'COLUMN_COMMENT';
            return DB::q("SELECT 
  `TABLE_SCHEMA`,`TABLE_NAME`,`COLUMN_NAME`,`COLUMN_COMMENT`
FROM
  `information_schema`.`COLUMNS` 
WHERE `TABLE_SCHEMA` NOT IN (
    'information_schema',
    'performance_schema',
    'sys',
    'mysql'
  ) 
  AND $matchField LIKE '%$field%'")->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    })();
    res(1, $data);
}
//======

$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
$fetchType = isset($_REQUEST['fetchType']) ? $_REQUEST['fetchType'] : PDO::FETCH_ASSOC;
$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : null;
$link = isset($_REQUEST['link']) ? $_REQUEST['link'] : (isset($_COOKIE['link']) ? $_COOKIE['link'] : null);

if (!$link) {
    // 快捷化输入连接信息，..语义分割,有点难了
    function parseDsn($str)
    {
        $str = trim($str);
        if (empty($str)) {
            return false;
        }
        // dsn/url解析
        $info = parse_url($str);
        if (isset($info['scheme'])) {
            return [
                'DBMS' => $info['scheme'],
                'HOST' => isset($info['host']) ? $info['host'] . (isset($info['port']) ? ':' . $info['port'] : '') : '',
                'NAME' => isset($info['path']) ? substr($info['path'], 1) : '',
                'USER' => isset($info['user']) ? $info['user'] : '',
                'PASS' => isset($info['pass']) ? $info['pass'] : '',
                'CHAR' => 'UTF8',
            ];
        }
        preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/', trim($str), $matches);
        if (isset($matches[0])) {
            return [
                'DBMS' => $matches[1],
                'USER' => $matches[2],
                'PASS' => $matches[3],
                'HOST' => $matches[4] . ':' . $matches[5],
                'NAME' => $matches[6],
                'CHAR' => 'UTF8',
            ];
        }
        // .env/.ini解析
        preg_match_all('/(\w+)\s?=\s?(\S*)/', $str, $matches);
        $dsn = [];
        if (!empty($matches[0])) {
            foreach ($matches[1] as $i => $field) {
                if (stripos($field, 'host')) $dsn['HOST'] = $matches[2][$i];
                if (stripos($field, 'port')) $dsn['HOST'] .= ':' . $matches[2][$i];
                if (stripos($field, 'pass')) $dsn['PASS'] = $matches[2][$i];
                if (stripos($field, 'user')) $dsn['USER'] = $matches[2][$i];
                if (stripos($field, 'name')) $dsn['NAME'] = $matches[2][$i];
            }
            return $dsn;
        }
        // multiline define/array map
        preg_match_all('/(["\'])(.*?)\1/', $str, $matches);
        $dsn = [];
        if (!empty($matches[0])) {
            foreach ($matches[2] as $i => $field) {
                if (false !== stripos($field, 'host')) $dsn['HOST'] = $matches[2][++$i];
                if (false !== stripos($field, 'port')) $dsn['HOST'] .= ':' . $matches[2][++$i];
                if (false !== stripos($field, 'pass')) $dsn['PASS'] = $matches[2][++$i];
                if (false !== stripos($field, 'user')) $dsn['USER'] = $matches[2][++$i];
                elseif (in_array(strtolower($field), ['db', 'data', 'database']) || false !== stripos($field, 'name')) $dsn['NAME'] = $matches[2][++$i];
            }
            return $dsn;
        }
        return false;
    }

    $dsn = parseDsn($q);
    if (!$dsn) {
        res(0, null, 'parse link failed!');
    }
    $info = serialize($dsn);
    $q = '#';
    $fetchType = PDO::FETCH_COLUMN;
} else {
    $links = include('../playground/config2.database.php');
    if (isset($links[$link])) {
        $dsn = $links[$link];
        isset($_REQUEST['db']) && $dsn['NAME'] = $_REQUEST['db'];
    }
}

if (!isset($_SESSION[$link]) && !isset($dsn)) {
    res(0, null, 'link not set!');
}

isset($dsn) or $dsn = unserialize($_SESSION[$link]);
define('DB_HOST', $dsn['HOST']);
define('DB_NAME', $dsn['NAME']);
define('DB_USER', $dsn['USER']);
define('DB_PASS', $dsn['PASS']);
!empty($dsn['CHAR']) && define('DB_CHAR', $dsn['CHAR']);
$dbInstance = DB::instance(true);
if ($dbInstance instanceof PDOException) {
    res(0, null, $dbInstance->getMessage());
}
if (isset($info)) {
    $link = md5($info);
    $_SESSION[$link] = $info;
    setcookie('link', $link);
    $_COOKIE['link'] = $link;
}
defined('DB_CHAR') && DB::x('SET NAMES "' . DB_CHAR . '"');

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
    '#*'        => ':tables info',
    '#user%'    => ':tables like',
    '-%user_id' => ':tables having the column',
    'u'         => ':user data limit 30',
    'u#'        => ':count user data',
    'u#2'       => ':query user data',
    'u#*'       => ':table structure',
    'u#~'       => ':PROCEDURE ANALYSE',
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
        return ['SHOW TABLES LIKE ?s', $var ?: '%'];
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
        throw new RuntimeException("Error table name", 1);
    }

    $params = [];
    if ($var) {
        if ($var === '*') {
            define('TABLE_DEFINE', 1);
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
                $offset = (int)$offset;
                $limit = (int)$limit;
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
        'info' => $_REQUEST,
        'cookie' => $_COOKIE,
        'data' => $data,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE));
}

// 显示不同的结果
function render($data)
{
    if (empty($data)) {
        echo '无数据';
        return;
    }
    if (defined('TABLE_DEFINE')) {
        echo '<ul class="table-structure">';
        foreach ($data as $field) {
            $style = explode('(', $field['Type'])[0];
            echo "<li class='$style'>{$field['Field']}<span>{$field['Comment']}</span></li>";
        }
        echo '</ul>';
        return;
    }
    if (count($data) === 1) {
        $data = current($data);
        if (count($data) === 1) {
            echo current($data);
        } else {
            echo '<ol>';
            foreach ($data as $key => $value) {
                is_null($value) && $value = '<i>&lt;null></i>';
                echo "<li><label>$key</label>$value</li>";
            }
            echo '</ol>';
        }
        return;
    }
    echo '<table border="0" cellpadding="3">';
    echo '<tr bgcolor="#dddddd" class="fixed-header"><th>#</th>';
    foreach (current($data) as $key => $null) {
        echo "<th>$key</th>";
    }
    echo '</tr>';
    foreach ($data as $_key => $_data) {
        echo "<tr><td><i>$_key</i></td>";
        foreach ($_data as $key => $value) {
            is_null($value) && $value = '<i>&lt;null></i>';
            echo "<td>$value</td>";
        }
        echo '</tr>';
    }
    echo '</table>';
}

function renderHtml($data)
{
    ob_start();
    render($data);
    $content = ob_get_contents();
    ob_clean();
    return $content;
}

if ($show || $q) {
    if (in_array($show, $shows)) {
        $w = 'SHOW ' . $show;
        $r = DB::q($w);
    } else {
        try {
            $w = parse($q);
            $r = call_user_func_array('DB::q', $w);
        } catch (Exception $e) {
            res(0, $w ?? $q, $e->getMessage());
        }
        $w = json_encode($w);
    }
    $d = $r->fetchAll($fetchType);
    if ($q === '#') {
        res(1, $d, $q);//array_map('current', $d)
    }
    res(1, renderHtml($d), $w);
}

res();
