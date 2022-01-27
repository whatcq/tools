<?php
/**
 * log raw http request
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

fwrite($fh, "\r\n");
isset($_SERVER['CONTENT_TYPE']) && fwrite($fh, strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') === 0
    ? http_build_query($_POST)
    : file_get_contents('php://input'));
fwrite($fh, "\r\n\r\n");
fclose($fh);

echo "<html><head /><body><iframe src=\"$myFile\" style=\"height:100%; width:100%;\"></iframe></body></html>";
echo '<pre>';
// print_r($_SERVER);
// print_r($_POST);
?>
<!-- multipart/form-data text/plain -->
<form action="" method="post" enctype="application/x-www-form-urlencoded">
    <input type="text" name="name" value="cqiu">
    <input type="text" name="happy" value="89">
    <input type="submit">
</form>
