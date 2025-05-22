<?php
/**
 * use mysql function under php7 width mysqli
 * 太复辟了。。
 */
$_connection = NULL;
function mysql_connect($server, $user, $password){
	global $_connection;
	return $_connection = mysqli_connect($server, $user, $password);
}
function mysql_select_db($db){
	global $_connection;
	return mysqli_select_db($_connection, $db);
}
function mysql_query($query){
	global $_connection;
	return mysqli_query($_connection, $query);
}
function mysql_fetch_array($result){
	return mysqli_fetch_array($result);
}
function mysql_fetch_row($result){
	return mysqli_fetch_row($result);
}
function mysql_num_rows($result){
	return mysqli_num_rows($result);
}
function mysql_set_charset($charset){
	global $_connection;
	return mysqli_set_charset($_connection, $charset);
}
function mysql_real_escape_string($string){
	global $_connection;
	return mysqli_real_escape_string($_connection, $string);
}
function mysql_error() {
	global $_connection;
	return mysqli_error($_connection);
}
function mysql_close() {
	global $_connection;
	return mysqli_close($_connection);
}
return;

//test------------------

$dbname = 'zidian';

$link = mysql_connect('127.0.0.1', 'root', 'xxxx')
	or die('无法连接: ' . mysql_error());

mysql_query("set character set 'utf8'");
mysql_select_db($dbname) or die('不能连接数据库！');
mysql_query("SET NAMES UTF8");

// 执行 SQL 查询
echo $result = mysql_query('select * from zi limit 1') 
	or die('查询失败: ' . mysql_error());