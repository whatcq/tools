<?php
/**
 * 监控php文件变化并执行&输出结果
 * @author:Cqiu
 * @date: 2017-6-15 10:39
 */

define('IS_WIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? true : false);

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
        `cls`;
        echo date('H:i:s'), "\n--------------\n";
        if (IS_WIN)
            echo @iconv('UTF-8', 'GBK', `php $file`);
        else
            echo `php $file`;
        $sign = $_sign;
    }
    sleep(2);
}
