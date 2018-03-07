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
        'files'     => get_included_files(),
        //'constants' => get_defined_constants(),
        'functions' => get_defined_functions()['user'],
        //'classes'   => get_declared_classes(),
        'get'   => $_GET,
        'post'   => $_POST,
        //'server'   => $_SERVER,

    ];
    echo '<pre>';
    print_r($stats);
});