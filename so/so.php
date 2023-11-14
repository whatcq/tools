<?php

/**
 * 通过AltRun/utools网页快开，快速打开对应工具
 */
$q = $_GET['q'] ?? '';

$maps = [
    'q'     => 'Q.php',
    'dbq'   => 'DBQ',
    'run'   => 'my-run.php',
    'php'   => 'playground.php',
    'pg'    => 'playground/',
    'jsrun' => 'jsrun',
    'talk'  => 'tasks/talk.html',
    'chats' => 'tasks/chats.html',
    'chat'  => 'tasks/openai/chatgpt.php',
];

$url = $maps[$q] ?? matchRequest($q) ?: 'Q2.php?q=' . $q;
header('location: ../' . $url);

function matchRequest($q)
{
    $cacheFile = '../playground/tool-files.txt';

    if (!is_file($cacheFile)) {
        $folder = dirname(__DIR__);
        $filePaths = getAllFilePaths($folder);
        file_put_contents($cacheFile, implode("\n", $filePaths));
    }

    isset($filePaths) or $filePaths = file($cacheFile);
    $matches = [];
    foreach ($filePaths as $v) {
        if (strpos(basename($v), $q) !== false) {
            $matches[] = $v;
        }
    }

    if (count($matches) === 1) {
        return $matches[0];
    } elseif (count($matches) > 1) {
        echo '<ol>';
        foreach ($matches as $v) {
            echo "<li><a href=\"../$v\">$v</a></li>";
        }
        echo '</ol>';
        die;
    }

    return '';
}

function getAllFilePaths($dir, $relativePath = '')
{
    $filePaths = array();
    $handle = opendir($dir);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $filePath = $dir . '/' . $file;
            if (is_file($filePath) && in_array(substr($file, -4), ['.php', 'html', '.htm'])) {
                $filePaths[] = $relativePath . $file;// $filePath;
            }
            if (is_dir($filePath) && $file[0] != '.') {
                $filePaths = array_merge($filePaths, getAllFilePaths($filePath, $relativePath . $file . '/'));
            }
        }
    }
    closedir($handle);

    return $filePaths;
}
