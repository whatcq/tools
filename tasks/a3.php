<?php

/**
 * A3=Cqiu's Assistant
 */

/*
I am A3,I will help you everything!

I must stay live!

I need:
- 了解你（注意信息保密）
- 识别你（不是你就终止服务）
- 运维：运行日志，守护自身,数据备份

I can do:
- 根据输入内容自动判断，并执行相应操作，这样就不再重复了
- 辅助分解任务
- 定时任务：提醒和监督
- 爬虫：抓取信息

需求方面：
- 精力管理（时间管理）
- 目标管理
*/

function _log($msg, $level = 'info')
{
    echo date('[Y-m-d H:i:s] ') . $msg . "\r\n";
    file_put_contents(
        'a3-' . date('Ymd') . '.log',
        date('[Y-m-d H:i:s] ') . "[$level] " . $msg . "\r\n",
        FILE_APPEND
    );
}

_log('A3 started', 'info');
register_shutdown_function(function () {
    _log('A3 shutdown', 'info');
});
// @todo 语义化的时间周期表达式
// @todo crontab解析
// @todo 进程间通信
file_put_contents('crontabs.txt', '');
$crontabs = file('crontabs.txt');
while (1) {

}
