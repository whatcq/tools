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
    die('file not found');
}
// view file
if (isset($_GET['file'])) {
	$file = $_GET['file'];
	header('Content-Type: ' . mime_content_type($file));
	header('Content-Disposition: inline; filename="' . basename($file) . '"');
	file_exists($file) && readfile($file) || die('file not found');
	die;
}
// 去掉路径里的.和..
function normalizePath($path)
{
    if (empty($path)) return $path;
    $path = str_replace('\\', '/', $path);
    $stack = [];
    foreach (explode('/', $path) as $part) {
        if ($part == '..') {
            $last = end($stack);
            (!$last || $last == '..') && ($stack[] = $part) || array_pop($stack);
        } elseif ($part != '.' && $part != '') {
            $stack[] = $part;
        }
    }

    return ($path[0] == '/' ? '/' : '') . implode('/', $stack) ?: $path;
}
# list
$dir = $_GET['dir'] ?? '.';
$dir = normalizePath($dir);
if (!is_dir($dir)) {
	return;
}
?>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<title>File Server</title>
<style type="text/css">
	li{padding: 1px 3px;max-width: 300px;}
	li:nth-child(odd){background-color: #f2f2f2;}
	li:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
	li:hover{background: #c3e9cb;}
	.right{float:right;padding: 0 5px;background: yellowgreen;border-radius: 5px;}
</style>
<?php
if ($dh = opendir($dir)) {
    $items = ['dir' => [], 'file' => []];
    while (($item = readdir($dh)) !== false) {
        $path = "$dir/$item";
        $type = is_dir($path) ? 'dir' : 'file';
        $items[$type][] = $path;
    }
    closedir($dh);

    echo '<ol>';
    sort($items['dir']); // fix sort for linux
    foreach ($items['dir'] as $path) {
        $folder = basename($path);
        echo "<li><a href='?dir=$path'>$folder</a></li>\n";
    }
    sort($items['file']);
    foreach ($items['file'] as $path) {
        $file = basename($path);
        echo "<li><a href='?file=$path'>$file</a> <a href=\"?down=$path\" class=\"right\">⇓</a></li>\n";
    }
    echo '</ol>';
}
