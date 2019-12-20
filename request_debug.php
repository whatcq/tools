<?php
/**
 * 简单记录http request/response,调试用
 * include 'request_debug.php';
 * @author Cqiu
 * @date 2019-12-20
 */
/*
cat > .debug.ini <<eof
debug=1
debugDataFile=/tmp/debug_data.log
eof
*/

// 闭包函数，锁定变量影响范围
(function () {
    if (!is_file($_configFile = '.debug.ini')) {
        return;
    }
    $_config = parse_ini_file($_configFile);
    if (empty($_config['debug'])) {
        return;
    }

    $_debug_data = [
        '_GET'  => $_GET,
        '_POST' => $_POST,
    ];
    ob_start();
    register_shutdown_function(function () use ($_debug_data, $_config) {
        $_debug_data['response'] = ob_get_contents();
        ob_end_flush();
        file_put_contents(
            empty($_config['debugDataFile']) ? $_config['debugDataFile'] : '/tmp/debug_data.log',
            print_r($_debug_data, 1),
            FILE_APPEND
        );
    });
})();
