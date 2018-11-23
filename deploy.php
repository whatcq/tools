<?php
/**
 * remote deploy request script
 * @Cqiu 2018-1-4
 */

// $file = '/dev/shm/git_pull.msg';
$file = '/data/app/git_pull.msg';

//================
// 后台进程，收到消息就更新代码
// $ nohup php deploy.php &
if (PHP_SAPI === "cli") {
    `touch $file && chmod 0777 $file`; //用户权限问题，因此cli需要先运行
    $log_file = '/data/app/project-dir/deploy.log';
    while (1) {
        $folders = trim(file_get_contents($file));
        if ($folders) {
            $folders = array_unique(explode(PHP_EOL, $folders));
            foreach ($folders as $folder) {
                `cd /data/app/$folder && echo "$folder -----\n" >> $log_file;git pull >> $log_file  2>&1`;
            }
            `date >> $log_file && echo > $file`;
        }
        sleep(3);//s
    }
    exit;
}
//================
// git web hook: http://domain.etc/deploy.php?project-dir/aa
$deployFolders = ['project-dir', 'project-dir/aa', 'project-dir/bb'];
if (isset($_SERVER['QUERY_STRING']) && $folder = $_SERVER['QUERY_STRING']) {
    if (in_array($folder, $deployFolders)) {
        echo `echo "$folder\n" >> $file && echo ok!`;
    } else {
        echo 'allow folders:', implode('<li>', $deployFolders);
    }
} else {
    file_put_contents($file, implode(PHP_EOL, $deployFolders) . PHP_EOL) && print 'all ok!';
}
