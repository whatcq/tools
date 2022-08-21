<?php
/**
 * 通过AltRun/utools网页快开，快速打开对应工具
 */
$q = $_GET['q'] ?? '';

$maps = [
    'q' => 'Q.php',
    'dbq' => 'DBQ',
    'run' => 'my-run.php',
    'php' => 'playground.php',
    'jsrun' => 'jsrun',
    'talk' => 'tasks/talk.php',
];

isset($maps[$q]) && header('location: ../' . $maps[$q]) && die('');

die('<a href="../">index</a>');
