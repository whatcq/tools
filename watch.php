<?php
/**
 * 监控php文件变化并执行&输出结果
 * @author:Cqiu
 * @date: 2017-6-15 10:39
 */

define('IS_WIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? true : false);
define('IS_GBK', (strlen('中文') === 4) ? true : false);
var_dump(IS_WIN, IS_GBK);
exit;

if ($argc < 2) {
    start:
    echo 'Please special file to watch > ';
    $file = trim(fgets(STDIN));
} else {
    $file = $argv[1];
}
if (!is_file($file)) {
    echo 'File not found : ', $file, PHP_EOL;
    goto start;
}
echo "monitor start: $file -------------\n";
$sign = 0;
while (1) {
    $_sign = md5_file($file);
    if ($_sign !== $sign) {
        if (IS_WIN) {
            `cls`;
            echo date('H:i:s'), "\n--------------\n";
            echo @iconv('UTF-8', 'GBK', `php $file`);
        } else {
            `clear`;
            echo date('H:i:s'), "\n--------------\n";
            $dir = dirname($file);
            echo `cd $dir && php $file`;
        }
        $sign = $_sign;
    }
    sleep(2);
}
