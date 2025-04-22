<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Q2</title>
    <link rel="stylesheet" href="./DBQ/dbq.css"/>
    <style>
        table {
            width: 100%
        }
        details {
            margin: 0;
            padding: 0;
            border: 1px solid #f6f6f6;
        }
        details > summary {
            padding: 0;
            margin: 0;
            cursor: pointer;
        }
    </style>
<?php

/**
 * dbq view tables_group
 */
$links = [
    'local' => [
        'HOST' => '127.0.0.1',
        'DB_NAME' => 'ai1',
        'USER' => 'root',
        'PASS' => '',
        'color' => 'lightblue', # option bgcolor
        //'extend' => 'local', # extend config
    ]
];
file_exists($configFile = 'playground/config2.database.php')
&& $links = array_merge($links, include $configFile);

$q = $_REQUEST['q'] ?? 'docker inventory_test delivery';
// link.db.table.column.value.info
list($link, $db, $table, $column, $value, $info) = explode('.', strtr($q, ' ', '.') . '......');
// var_dump($link, $db, $table, $column, $value);
$hiddenInputs = '';
foreach ($_REQUEST as $k => $v) {
    // $hiddenInputs .= "<input type='hidden' name='$k' value='$v'>";
}
echo <<<FORM
<form action="">
$hiddenInputs
    <input type="text" name="q" value="$q" style="width:100%" accesskey="Z">
<!--        <textarea name="q" id="input2" cols="30" rows="3" style="width:100%" accesskey="Z">$q</textarea>-->
    <div>
<!--        <select name="link" id="links" size="3"></select>-->
        <input type="text" name="link" id="link" value="$link" placeholder="link" />
        <input type="text" name="db" id="db" value="$db" placeholder="db" />
        <input type="text" name="table" id="table" value="$table" placeholder="table">
        <input type="text" name="field" id="field" value="$column" placeholder="field">
        <input type="text" name="value" id="value" value="$value" placeholder="value">
        <input type="submit" />
    </div>
FORM;

$qs = [
    '～ show all links'     => '',
    '≡ show all dbs'       => 'docker',
    '▤▦ show all tables'   => 'docker ict',
    '▧ show tables group'  => 'docker ict delivery',
    '▨ show tables match'  => 'docker ict delivery_',
    '▥ show table columns' => 'docker ict db',
];
foreach ($qs as $act => $_q) {
    $_GET['q'] = $_q;
    // echo '<li><a href="?' . http_build_query($_GET) . '">' . $act . '</a></li>';
    echo '<a href="?' . http_build_query($_GET) . '">' . $act . '</a> ';
}

if (!isset($links[$link])) {
    echo '<pre>';
    print_r($links);
    echo '</pre>';
    // @todo input link
    die('no link:' . $link);
}

require './lib/DB.php';
$config = $links[$link];
define('DB_HOST', $config['HOST']);
// define('DB_NAME', 'information_schema');
define('DB_USER', $config['USER']);
define('DB_PASS', $config['PASS']);
isset($config['CHAR']) && define('DB_CHAR', $config['CHAR']);

$dbs = DB::q('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
if (empty($db)) {
    render($dbs);
    die;
}

// get match database
$matchDbs = getMatches($dbs, $db);
if (count($matchDbs) !== 1) {
    render($matchDbs);
    die('match dbs');
}

// if match one database, get tables
if (empty($table)) {
    $tables = DB::q(
        'SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS n,INDEX_LENGTH idx,AVG_ROW_LENGTH w,AUTO_INCREMENT i,TABLE_COLLATION,CREATE_TIME,UPDATE_TIME,TABLE_TYPE type,ENGINE e,ROW_FORMAT FROM information_schema.`TABLES` where TABLE_SCHEMA=?s',
        $matchDbs[0]
    )
        ->fetchAll(PDO::FETCH_ASSOC);
    $groups = tableClassify(array_column($tables, 'TABLE_NAME'));

    // 表多排前面，杂项排最后
    uasort($groups, fn($a, $b) => count($b) - count($a));
    $other = [];
    isset($groups['_']) && ($other['_'] = $groups['_']);
    isset($groups['__']) && ($other['__'] = $groups['__']);
    unset($groups['_'], $groups['__']);
    $groups = $groups + $other;

    foreach ($groups as $k => $v) {
        $count = count($v);
        echo "<details><summary>$k ($count)</summary>\n";
        render(array_filter($tables, fn($item) => in_array($item['TABLE_NAME'], $v)));
        echo "</details>\n";
    }
    // render($tables);
    die;
}

// if isset table_groups, get table_groups
$tableGroups = file_exists($tgf = 'playground/table_groups.php') ? include($tgf) : [];
if (isset($tableGroups[$table])) {
    $tables = $tableGroups[$table];
    render($tables);
    die;
}

// match tables or all tables
DB::x("USE $matchDbs[0]");
$tables = DB::q('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$matchTables = getMatches($tables, $table);
if (count($matchTables) !== 1) {
    render($matchTables);
    die('match tables');
}

$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : null;
$table = $matchTables[0];
$var = $column . $value;

print_r($_GET);

$params = [];
// var_dump((!isset($_GET['table']) || $_GET['table']!=$table));die;
if ($var && (!isset($_GET['table']) || $_GET['table'] != $table)) {
    if ($var === '*') {
        // 表字段
        $sql = "SHOW FULL FIELDS FROM $table";
        goto QQ;
    } elseif ($var === '@') {
        // 表分析
        $sql = "SELECT * FROM $table PROCEDURE ANALYSE(1, 10)";
        goto QQ;
    }

    define('SHOW_TABLE', $table);
    $sql = "SELECT * FROM $table";
    // 字段匹配
    if ($column && $value) {
        if ($value[0] === '~') {
            $sql .= " WHERE `$column` LIKE ?s";
            $params[] = addslashes(substr($value, 1));
            goto QQ;
        }
        $sql .= " WHERE `$column` = ?s";
        $params[] = $value;
        goto QQ;
    }
    // 全部匹配
    if ($var[0] === '~' || $var[0] === '=') {
        $isLike = $var[0] === '~';
        $var = addslashes(substr($var, 1));
        $where = [];
        $columns = DB::q("DESC $table")->fetchAll(PDO::FETCH_ASSOC);
        if ($isLike) {
            foreach ($columns as $column) {
                if (strpos($column['Type'], 'int') !== false) continue;
                $where[] = "`{$column['Field']}` LIKE '%$var%'";
            }
        } else {
            $isNum = is_numeric($var);
            $isDate = $isNum ? false : preg_match('#\d{4}-\d{2}-\d{2}#', $var);
            foreach ($columns as $column) {
                // 你不是我是，跳过
                if (!$isNum && strpos($column['Type'], 'int') !== false) continue;
                if (!$isDate && strpos($column['Type'], 'date') !== false) continue;
                $where[] = "`{$column['Field']}`='$var'";
            }
        }
        $where = $where ? ' WHERE ' . implode(' OR ', $where) : '';
        $sql .= "$where LIMIT 30";

        goto QQ;
    }

    // 主键匹配
    // $pkField = getPk($table);
    // $pkField or $pkField = 'id';
    // $sql .= " WHERE $pkField=?s";
    // $params = [$var];
    // 索引匹配
    $keyFields = getKeyFields($table);
    $where = [];
    foreach ($keyFields as $field) {
        $where[] = "$field=?s";
        $params[] = $var;
    }
    $where = $where ? ' WHERE ' . implode(' OR ', $where) : '';
    $sql .= "$where LIMIT 30";
} elseif ($var == '#') {
    $sql = "SELECT COUNT(*) FROM $table";
} else {
    $where = $orderBy = '';
    $offset = 0;
    $limit = 30;
    if ($show) {
        if (strpos($show, ',') !== false) {
            list($offset, $limit) = explode(',', $show);
            $offset = (int)$offset;
            $offset > 0 or $offset = 1;
            $limit = (int)$limit;
        } elseif ($show < 0) {
            $orderBy = " ORDER BY 1 DESC";
            $limit = abs($show);
        }
    }
    $fields = '*';
    if (isset($_GET['table']) && $_GET['table'] == $table) {
        $where = [];
        foreach ($_GET['where'] ?? [] as $field => $value) {
            if (strlen($value)) {
                if (preg_match('/^(<>|>=|>|<=|<|=)/', $value, $matches)) {
                    $operator = $matches[1];
                    $value = substr($value, strlen($operator));
                } elseif (strpos($value, '%') !== false) {
                    $operator = ' LIKE ';
                } else {
                    $operator = '=';
                }
                $field = addslashes($field);
                $where[] = "`$field`{$operator}?s";
                $params[] = $value;
            }
        }
        $where = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        if (!empty($_GET['fields'])) {
            $fields = implode(',', array_map(fn($v) => '`' . trim($v) . '`', array_filter(explode("\n", $_GET['fields']))));
        }
    }
    $sql = "SELECT $fields FROM $table{$where}{$orderBy} LIMIT $offset,$limit";
    define('SHOW_TABLE', $table);
}

QQ:
include './lib/SqlFormatter.php';
$sql = DB::autoQuote($sql, $params);
echo '<details ><summary>sql</summary>';
echo SqlFormatter::format(SqlFormatter::removeComments($sql), true);
echo '</details>';

$data = DB::q($sql)->fetchAll(PDO::FETCH_ASSOC);
render($data);
die('--end--');

//---------------
function getMatches($items, $item)
{
    $matchDbs = [];
    $dbPis = explode('_', $item);
    foreach ($items as $_item) {
        is_array($_item) && $_item = current($_item);
        if ($_item == $item) {
            return [$item];
        }
        $abbr = implode('', array_map(fn($v) => $v[0] ?? '', explode('_', strtr($_item, '-', '_'))));
        if ($abbr == $item) {
            return [$_item];
        }
        $match = true;
        foreach ($dbPis as $pis) {
            if ($pis && strpos($_item, $pis) === false) {
                $match = false;
                break;
            }
        }
        $match && $matchDbs[] = $_item;
    }

    return $matchDbs;
}

function tableClassify($tables)//, $prefix = '/^t_/'preg_replace($prefix, '', )
{
    $words = [];
    foreach ($tables as $table) {
        foreach (explode('_', $table) as $word) {
            isset($words[$word]) ? $words[$word]++ : $words[$word] = 1;
        }
    }
    arsort($words);
    $prefix = '';
    // 有bug，其它部分==prefix怎么办？
    (100 * current($words) / count($tables) > 95) && $prefix = array_shift($words); // remove prefix

    $groups = [];
    foreach ($tables as $table) {
        foreach (explode('_', $table) as $word) {
            if (isset($words[$word])) {
                if ($words[$word] > 1) {
                    $groups[$word][] = $table;
                } else {
                    $groups['_'][] = $table;
                }
                break;
            }
        }
    }
    foreach ($groups as $k => $v) {
        if (count($v) == 1) {
            $groups['__'][] = current($v);
            unset($groups[$k]);
        }
    }

    return $groups;
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
        // scalar
        if (count($data) === 1) {
            echo current($data);

            return;
        }
        // 单组数据
        SINGLE_GROUP:
        echo '<ol>';
        foreach ($data as $key => $value) {
            is_null($value) && $value = '<i>&lt;null></i>';
            echo "<li><label>$key</label>$value</li>";
        }
        echo '</ol>';

        return;
    }
    if (!is_array(current($data))) {
        goto SINGLE_GROUP;
    }

    defined('SHOW_TABLE')
    && print('<textarea name="fields" style="position:absolute;top:0;right:0;width:150px;height:100px;font: 10px/15px Courier;">' . implode(
            "\n", array_keys($data[0])
        ) . '</textarea>');
    echo '<table border="0" cellpadding="3">';
    echo '<thead><tr bgcolor="#dddddd" class="fixed-header"><th class="number">#</th>';
    $filters = defined('SHOW_TABLE')
        ? '<tr><td><input type="hidden" name="table" value="' . SHOW_TABLE . '"></td>' : false;
    foreach (current($data) as $key => $null) {
        echo "<th>$key</th>";
        $value = htmlspecialchars($_GET['where'][$key] ?? '');
        $filters && $filters .= "<td><input type='text' name='where[$key]' value='$value' style='padding:0;width:100%'></td>";
    }
    $filters && print('</tr>' . $filters);
    echo '</tr></thead><tbody>';
    foreach ($data as $_key => $_data) {
        echo "<tr><td><i>$_key</i></td>";
        foreach ($_data as $key => $value) {
            is_null($value) && $value = '<i>&lt;null></i>';
            echo "<td>$value</td>";
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

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

// 获取主键字段名
function getKeyFields($table)
{
    $_sql = "SHOW KEYS FROM $table";
    $pkFields = [];
    if ($pkInfo = DB::q($_sql)->fetch(PDO::FETCH_ASSOC)) {
        $pkFields[] = $pkInfo['Column_name'];
    }

    return $pkFields;
}
