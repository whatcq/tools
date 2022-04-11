<?php
/**
 * Simple Database Query by Cqiu
 */
$links = [
    'local' => [
        'host' => '127.0.0.1',
        'database' => 'test',
        'username' => 'root',
        'password' => 'root',
        'color' => 'lightblue', # option bgcolor
        //'extend' => 'local', # extend config
    ]
];
file_exists($configFile = 'playground/config.database.php')
&& $links = array_merge($links, include $configFile);

$link = $_REQUEST['link'] ?? 'local';
$config = $links[$link];
isset($links[$config['extend'] ?? '']) && $config = array_merge($links[$config['extend']], $config);
isset($config['port']) && $config['host'] .= ':' . $config['port'];
define('DB_HOST', $config['host']);
define('DB_NAME', $config['database']);
define('DB_USER', $config['username']);
define('DB_PASS', $config['password']);
isset($config['charset']) && define('DB_CHAR', $config['charset']);

require 'lib/DB.php';

//-----------------query start
$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : null;

if (isset($_REQUEST['data'])) {
    header("Content-Type: text/event-stream\n\n");

    $w = parse('#%');
    if (defined('DB_CHAR')) DB::x('SET NAMES "' . DB_CHAR . '"');
    $r = call_user_func_array('DB::q', $w);
    $tables = $r->fetchAll(PDO::FETCH_COLUMN);

    ob_flush();
    flush();
    echo 'data: ', implode(',', $tables), "\n\n";
    ob_flush();
    flush();
    die;
}

//------------------page render
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
?>
<meta charset="utf-8">
<title>Quick Query</title>
<style type="text/css">
*{font-family: 'Microsoft YaHei',Arial,serif;}
a{display: inline-block; padding: 2px 5px;background: #c6dfff;border-radius: 3px;}
label{clear:left; width: 130px;display: inline-block;color: gray;font-style: italic;}
tr:nth-child(odd),li:nth-child(odd){background-color: #f2f2f2;}
tr:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
tr:nth-child(5n+0),li:nth-child(5n+0) {background-color: #e9e6e6;}
tr:hover,li:hover{background: #c3e9cb;}
pre{margin:0;}
i{font-size:60%;color:gray;}
table{font-size:80%}
td{word-break: break-all}
.fixed-header thead tr {position: relative;}
.fixed-header thead th {position: sticky;top: 0;resize: horizontal;overflow: auto;text-shadow: 1px 1px 0 #fff;background: #3c8dbc !important;background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #67a8ce)) !important;}
</style>
<script type="text/javascript">
function $(str) {
	return document.getElementById(str);
}
window.onload = function() {
	$('q').focus();
};
</script>
<form style="display: inline-block;margin-bottom: 0;">
	<div style="position:relative;">
        <input type="text" list="tables" name="q" id="q" value="<?php echo $q; ?>" style="height: 25px;" />
        <datalist id="tables"></datalist>

        <script>
            var skey = '<?=$link?>Tables';

            function setDataList() {
                var tables = localStorage.getItem(skey).split(',');
                var list = document.getElementById('tables');

                tables.forEach(function (item) {
                    var option = document.createElement('option');
                    option.value = item;
                    list.appendChild(option);
                });
            }

            if (localStorage.getItem(skey)) {
                setDataList();
            } else {
                var chat = new window.EventSource("?link=<?=$link?>&show=tables&data=1");
                chat.onmessage = function (e) {
                    var msg = e.data;
                    localStorage.setItem(skey, msg);
                    setDataList();
                    chat.close();
                };
            }
        </script>
        <select name="link" style="height: 25px;background: <?=$config['color']??'#fff'?>" onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor;">
            <?php
            foreach ($links as $_link => $info) {
                isset($links[$info['extend'] ?? '']) && $info = array_merge($links[$info['extend']], $info);
                $color = $info['color'] ?? '#fff';
                $selected = $link === $_link ? ' selected' : '';
                echo "<option value=\"$_link\" style=\"background: $color\"$selected> $_link - {$info['database']} </option>\n";
            }
            ?>
        </select>
		<select name="show" id="show" style="height: 25px;" onchange="this.form.submit()">
			<option value="">-- show --</option>
<?php
foreach ($shows as $_show) {
	echo "<option value=\"$_show\"", ($show === $_show ? ' selected' : ''), "> $_show </option>\n";
}
?>
		</select>
		<input type="submit" value="Go" />
	</div>
<?php
// common sqls
$sqls = [
	'#*' => ':tables info',
	'#user%' => ':tables like',
	'-%user_id' => ':tables having the column',
	'u' => ':user data limit 30',
	'u#' => ':count user data',
	'u#2' => ':query user data',
	'u#*' => ':table structure',
	'u#@' => ':PROCEDURE ANALYSE',
];
foreach ($sqls as $_q => $sql) {
	echo " <a href=\"?q=", urlencode($_q), "\" title=\"$sql\">$_q</a>";
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

//解析请求，返回sql+params
function parse($q)
{
	global $show;
	if ($q === '#*') {
		return ['SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS n,INDEX_LENGTH idx,AVG_ROW_LENGTH w,AUTO_INCREMENT i,TABLE_COLLATION,CREATE_TIME,UPDATE_TIME,TABLE_TYPE type,ENGINE e,ROW_FORMAT FROM information_schema.`TABLES` where TABLE_SCHEMA=?s', DB_NAME];
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
		} elseif ($var === '@') {
			return ["SELECT * FROM $table PROCEDURE ANALYSE(1, 10)"];
		}

        $sql = "SELECT * FROM $table";
        if ($var[0] === '~' || $var[0] === '=') {
            $sql .= ' WHERE';
            $var = addslashes(substr($var, 1));
            $columns = DB::q("DESC $table")->fetchAll(PDO::FETCH_ASSOC);
            if ($var[0] === '~') {
                foreach ($columns as $column) {
                    $sql .= " {$column['Field']} LIKE '%$var%' OR";
                }
            } else {
                $notNum = !is_numeric($var);
                foreach ($columns as $column) {
                    if ($notNum && strpos($column['Type'], 'int') !== false) continue;
                    $sql .= " {$column['Field']}='$var' OR";
                }
            }

            $sql = trim($sql, ' OR');
            return [$sql];
        }

        $pkField = getPk($table);
        $pkField or $pkField = 'id';
        $sql .= " WHERE $pkField=?s";
        $params = [$var];
	} elseif (is_null($var)) {
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
        if (isset($_GET['table']) && $_GET['table'] == $table && is_array($_GET['where'])) {
            $where = [];
            foreach ($_GET['where'] as $field => $value) {
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
        }
        $fields = '*';
        if (!empty($_GET['fields'])) {
            $fields = trim(strtr($_GET['fields'], "\n", ','), ", \t\n\r");
        }
        $sql = "SELECT $fields FROM $table{$where}{$orderBy} LIMIT $offset,$limit";
        define('SHOW_TABLE', $table);
	} else {
		$sql = "SELECT COUNT(*) FROM $table";
	}

	return array_merge([$sql], $params);
}

// 显示不同的结果
function render($data) {
	if (empty($data)) {
		echo '无数据';
		return;
	}
	if (!defined('SHOW_TABLE') && count($data) === 1) {
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

    defined('SHOW_TABLE') && print('<textarea name="fields" style="position:absolute;top:0;right:0;width:150px;height:100px;font: 10px/15px Courier;">' . implode("\n", array_keys($data[0])) . '</textarea>');
    echo '<table border="0" cellpadding="3" class="fixed-header">';
    echo '<thead><tr><th>#</th>';
    $filters = defined('SHOW_TABLE') ? '<tr><td>#<input type="hidden" name="table" value="' . SHOW_TABLE . '"></td>' : false;
    foreach (current($data) as $key => $null) {
        echo "<th>$key</th>";
        $value = htmlspecialchars($_GET['where'][$key] ?? '');
        $filters && $filters .= "<td><input type='text' name='where[$key]' value='$value' style='width:100%'></td>";
    }
    $filters && print('</tr>' . $filters);
	echo '</tr></thead>';
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

if ($show || $q) {
	if (in_array($show, $shows)) {
		$r = DB::q('SHOW ' . $show);
	} else {
		// parse
		$w = parse($q);
		var_dump($w);
		// execute
		$r = call_user_func_array('DB::q', $w);
	}
	$d = $r->fetchAll(PDO::FETCH_ASSOC);

	echo '<hr />';
	render($d);
}
?>
</form>