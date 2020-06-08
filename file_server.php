<?php
/**
Simple File Server
> php -S 0.0.0.0:80 ffile.php
@cqiu 2020/5/30
*/
// download file
if (isset($_GET['down'])) {
	$file = $_GET['down'];
	$download_name = basename($file);
	if (file_exists($file)) {
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $download_name);
		readfile($file);
		//header('X-Sendfile: ' . $file);
		exit;
	}
}
// view file
if (isset($_GET['file'])) {
	$file = $_GET['file'];
	header('Content-Type: ' . mime_content_type($file));
	header('Content-Disposition: inline; filename="' . basename($file) . '"');
	readfile($file);
	die;
}

# list
$dir = $_GET['dir'] ?? '.';
if (!is_dir($dir)) {
	return;
}
?>
<title>File Server</title>
<style type="text/css">
	li{padding: 1px 3px}
	li:nth-child(odd){background-color: #f2f2f2;}
	li:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
	li:hover{background: #c3e9cb;}
	.right{float:right;}
</style>
<?php
if ($dh = opendir($dir)) {
	echo '<ol>';
	while (($file = readdir($dh)) !== false) {
		$path = $dir . DIRECTORY_SEPARATOR . $file;
		$type = is_dir($path)?'dir':'file';
		echo "<li><a href='?$type=$path'>$file</a>";
		if ($type === 'file')echo " <a href=\"?down=$path\" class=\"right\">⇓</a>";
		echo "</li>", PHP_EOL;
	}
	echo '</ol>';
	closedir ($dh);
}
