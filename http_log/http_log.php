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
fwrite($fh, file_get_contents('php://input'));
fwrite($fh, "\r\n\r\n");
fclose($fh);

echo "<html><head /><body><iframe src=\"$myFile\" style=\"height:100%; width:100%;\"></iframe></body></html>";
echo '<pre>';
// print_r($_SERVER);
print_r($_POST);
?>
<!--application/x-www-form-urlencoded-->
<form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="name" value="cqiu">
    <input type="text" name="happy" value="89">
    <input type="submit">
</form>
