<title>Quick Query</title>
<style type="text/css">label{clear:left; width: 130px;display: inline-block;color: gray;font-style: italic;}</style>
<form><input name="q" value="<?php echo $q = $_REQUEST['q'] ?>" onload="this.focus();"></form>
<?php
// common sqls
$sqls = [
	'.' => 'SHOW DATABASES',
	'#' => 'SHOW TABLES',
	'#user%' => null, //tables
	'-user_id%' => null, //tables having the column
];
foreach ($sqls as $_q => $sql) {
	echo " <a href=\"?q=", urlencode($_q), "\" title=\"$sql\">$_q</a>";
}

//解析请求，返回sql+params
function parse($q) {
	global $sqls;
	if (!empty($sqls[$q])) {
		return [$sqls[$q]];
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
			'SELECT TABLE_SCHEMA,TABLE_NAME, COLUMN_NAME FROM information_schema.columns'
			. ' WHERE TABLE_SCHEMA=?s AND COLUMN_NAME LIKE ?s',
			DB_NAME,
			substr($q, 1),
		];
	}

	if (isset($tables[$table])) {
		$table = $tables[$table];
	}

	$params = [];
	if ($id) {
		$sql = "SELECT * FROM $table";
		$sql .= ' WHERE id=?i';
		$params = [$id];
	} elseif (is_null($id)) {
		$sql = "SELECT * FROM $table LIMIT 100";
	} else {
		$sql = "SELECT COUNT(*) FROM $table";
	}

	return array_merge([$sql], $params);
}

// 显示不同的结果
function render($data) {
	if (count($data) === 1) {
		$data = current($data);
		if (count($data) === 1) {
			echo current($data);
		} else {
			echo '<ol>';
			foreach ($data as $key => $value) {
				echo "<li><label>$key</label>$value</li>";
			}
			echo '</ol>';
		}
		return;
	}
	echo '<table border="1">';
	echo '<tr bgcolor="#dddddd">';
	foreach (current($data) as $key => $null) {
		echo "<th>$key</th>";
	}
	echo '</tr>';
	foreach ($data as $_key => $_data) {
		echo '<tr>';
		foreach ($_data as $key => $value) {
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

	// parse && execute
	$w = parse($q);
	// var_dump($w);die;
	$r = call_user_func_array('DB::q', $w);
	$d = $r->fetchAll(PDO::FETCH_ASSOC);

	var_dump($w);
	echo '<hr />';
	render($d);
}
?>