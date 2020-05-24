<title>Quick Query</title>
<style type="text/css">
a{display: inline-block; padding: 2px 5px;background: #c6dfff;border-radius: 3px;}
label{clear:left; width: 130px;display: inline-block;color: gray;font-style: italic;}
tr:nth-child(odd){background-color: #f2f2f2;}
tr:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
pre{margin:0;}
i{font-size:60%;color:gray;}
</style>
<form style="display: inline-block;margin-bottom: 0;"><input name="q" value="<?php echo $q = $_REQUEST['q'] ?>" onload="this.focus();"></form>
<?php
// common sqls
$sqls = [
	'.' => 'SHOW DATABASES',
	'#' => 'SHOW TABLES',
	'#*' => ':SHOW TABLES',
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

//解析请求，返回sql+params
function parse($q) {
	global $sqls;
	if (!empty($sqls[$q]) && $sqls[$q][0] !== ':') {
		return [$sqls[$q]];
	}

	if ($q === '#*') {
		return ['SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS n,AVG_ROW_LENGTH l,INDEX_LENGTH idx,AUTO_INCREMENT i,TABLE_COLLATION,CREATE_TIME,UPDATE_TIME,TABLE_TYPE type,ENGINE e,ROW_FORMAT FROM information_schema.`TABLES` where TABLE_SCHEMA=?s', DB_NAME];
	}

	// table alias
	$tables = [
		'u' => 'user',
		'o' => 'order',
	];

	list($table, $id) = explode('#', $q);

	if (!$table) {
		return ['SHOW TABLES LIKE ?s', $id];
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

	$params = [];
	if ($id) {
		if ($id === '*') {
			return ["SHOW FULL FIELDS FROM $table"];
			return [
				'SELECT COLUMN_NAME,COLUMN_COMMENT,COLUMN_DEFAULT default_,IS_NULLABLE nul,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH m,CHARACTER_OCTET_LENGTH n,COLUMN_KEY,EXTRA FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA=?s AND TABLE_NAME=?s', //,CHARACTER_SET_NAME c,COLLATION_NAME,COLUMN_TYPE
				DB_NAME,
				$table,
			];
		} elseif ($id === '~') {
			return ["SELECT * FROM $table PROCEDURE ANALYSE(1, 10)"];
		}
		$sql = "SELECT * FROM $table";
		$sql .= ' WHERE id=?i';
		$params = [$id];
	} elseif (is_null($id)) {
		$sql = "SELECT * FROM $table LIMIT 30";
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
	echo '<tr bgcolor="#dddddd">';
	foreach (current($data) as $key => $null) {
		echo "<th>$key</th>";
	}
	echo '</tr>';
	foreach ($data as $_key => $_data) {
		echo '<tr>';
		foreach ($_data as $key => $value) {
			is_null($value) && $value = '<i>&lt;null></i>';
			echo "<td>$value</td>";
		}
		echo '</tr>';
	}
	echo '</table>';
}

if ($q) {
	define('DB_HOST', '127.0.0.1');
	define('DB_NAME', 'mdwl');
	define('DB_USER', 'root');
	define('DB_PASS', 'root');

	require 'lib/DB.php';

	// parse
	$w = parse($q);
	var_dump($w);
	// execute
	$r = call_user_func_array('DB::q', $w);
	$d = $r->fetchAll(PDO::FETCH_ASSOC);

	echo '<hr />';
	render($d);
}
?>