<?php
$links = include 'playground/config.database.php';
$link = $_REQUEST['link'] ?? 'local';
$config = $links[$link];
define('DB_HOST', $config['host']);
define('DB_NAME', $config['database']);
define('DB_USER', $config['username']);
define('DB_PASS', $config['password']);
isset($config['charset']) && define('DB_CHAR', $config['charset']);

require 'lib/DB.php';

$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
$show = isset($_REQUEST['show']) ? $_REQUEST['show'] : null;

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
tr:nth-child(odd){background-color: #f2f2f2;}
tr:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
tr:hover{background: #c3e9cb;}
pre{margin:0;}
i{font-size:60%;color:gray;}
table{font-size:80%}
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
		<span style="margin-left:200px;width:18px;overflow:hidden;">
			<select style="width:218px;margin-left:-200px;height: 25px;"
			 onchange="$('q').value=this.value;$('q').focus();$('show').value=''">
<?php
$w = parse('#%');
if (defined('DB_CHAR'))DB::x('SET NAMES "'.DB_CHAR.'"');
$r = call_user_func_array('DB::q', $w);
$d = $r->fetchAll(PDO::FETCH_COLUMN);

foreach ($d as $_table) {
	echo "<option value=\"$_table\"> $_table </option>\n";
}
?>
			</select>
		</span>
		<input type="text" name="q" id="q" value="<?php echo $q; ?>"
			style="width:200px;position:absolute;left:2px;top:4px;height: 20px;border:0;" />
        <select name="link" style="height: 25px;">
            <?php
            foreach ($links as $_link => $info) {
                $color = $info['color'] ?? '#fff';
                $selected = $link === $_link ? ' selected' : '';
                echo "<option value=\"$_link\" style=\"background: $color\"$selected> $_link </option>\n";
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
</form>
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
	'u#~' => ':PROCEDURE ANALYSE',
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

// 显示不同的结果
function render($data) {
	if (empty($data)) {
		echo '无数据';
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
	echo '<tr bgcolor="#dddddd"><th>#</th>';
	foreach (current($data) as $key => $null) {
		echo "<th>$key</th>";
	}
	echo '</tr>';
	foreach ($data as $_key => $_data) {
		echo "<tr><td>$_key</td>";
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