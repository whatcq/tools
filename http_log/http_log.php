<?php

/**
 * log raw http request
 * php是CGI程序，得不到raw格式，得到的是处理过的数据$_POST,$_FILES等
 *
 * content-type：
 * - form-data(multipart/form-data)，支持上传文件的表单类型，可以上传文件，都是boundary分隔的键值对
 * - application/x-www-from-urlencoded，=&序列化的键值对
 */
$myFile = "requests-" . date('Ymd') . '.log';
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, "\n---------------------------------------------------------------\n");
fwrite($fh, "{$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']} {$_SERVER['SERVER_PROTOCOL']}\r\n");

foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $key = strtr(ucwords(strtolower(strtr(substr($key, 5), '_', ' '))), ' ', '-');
        fwrite($fh, "$key: $value\r\n");
    }
}
isset($_SERVER['CONTENT_LENGTH']) && fwrite($fh, "Content-Length: {$_SERVER['CONTENT_LENGTH']}\r\n");
isset($_SERVER['CONTENT_TYPE']) && fwrite($fh, "Content-Type: {$_SERVER['CONTENT_TYPE']}\r\n");

fwrite($fh, "\r\n");
if (isset($_SERVER['CONTENT_TYPE'])) {
    $bodyContent = strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') === 0
        ? http_build_query($_POST)
        : file_get_contents('php://input');
    fwrite($fh, $bodyContent);
}
fwrite($fh, "\r\n\r\n");

fclose($fh);

$myFile = dirname($_SERVER['REQUEST_URI']) . '/' . $myFile;
echo "<html><head /><body><iframe src=\"$myFile\" style=\"height:100%; width:100%;\"></iframe></body></html>";
echo '<pre>';
// print_r($_SERVER);
// print_r($_POST);
?>
<!-- multipart/form-data text/plain -->
<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" enctype="<?=
$_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded' ?>">
    <label>name:<input type="text" name="name" value="cqiu"></label>
    <label>happy:<input type="text" name="happy" value="89"></label>
    <?php
    foreach ($_POST as $k => $v) {
        echo '<label>' . $k . ':</label><input type="text" name="' . $k . '" value="' . htmlspecialchars($v) . '"></label><br />';
    }
    ?>
    <input type="submit">
</form>
