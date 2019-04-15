<?php
/**
 * 简单分析php程序的状况
 *
 * Author: Cqiu
 * Date: 2017-10-28
 */

/*
 * Usage：
 * include('php-analysis.php');
 */
if (!isset($_GET['_t'])) return;

$cqiu__startTime = microtime(true);
register_shutdown_function(function () {
    $stats = [
        'runtime'   => round(microtime(true) - $GLOBALS['cqiu__startTime'], 3).' s',
        'memory'    => sprintf('%.3f MB', memory_get_peak_usage() / 1048576),
        'files'     => strpos($_GET['_t'], 'x')!==false ? 'x' : get_included_files(),
        'constants' => strpos($_GET['_t'], 'T')===false ? 'T' : get_defined_constants(),
        'functions' => strpos($_GET['_t'], 'F')===false ? 'F' : get_defined_functions()['user'],
        'classes'   => strpos($_GET['_t'], 'C')===false ? 'C' : get_declared_classes(),
        'get'   => strpos($_GET['_t'], 'g')===false ? 'g' : $_GET,
        'post'   => strpos($_GET['_t'], 'p')===false ? 'p' : $_POST,
        'server'   => strpos($_GET['_t'], 's')===false ? 's' : $_SERVER,
    ];
    echo '<pre>';
    print_r($stats);
    echo '</pre>';
});