<?php
/**
 * markdown reader by ?p=[filePath]
 *
 * @cqiu 2016-08-01
 */

if (isset($_GET['src'])) {
    readfile($_GET['src']);
    exit;
}

include 'parsedown/Parsedown.php';

$parsedown = new Parsedown();

$p = $_GET['p'] ?? null;

if (is_file($p)) {
    $content = file_get_contents($p);
} elseif (is_dir($p)) {
    if (file_exists($p . '/README.md')) {
        $content = file_get_contents($p = $p . '/README.md');
    } else {
        $content = null;
        $html = '<li><a href="?p=' . dirname($p) . '">' . dirname($p) . '<a></li>';
        foreach (glob($p . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $html .= '<li><a href="?p=' . $dir . '">' . $dir . '<a></li>';
        }
        foreach (glob($p . DIRECTORY_SEPARATOR . '*.md') as $file) {
            $html .= '<li><a href="?p=' . $file . '">' . $file . '<a></li>';
        }
    }
} else {
    $content = null;
    $html = 'File not found!';
}
if ($content) {
    $path = dirname($p) . DIRECTORY_SEPARATOR;
    $content = $parsedown->text($content);
    $content = preg_replace('# src="(?!(https?://))(.*)"#i', ' src="?src=' . $path . '\$2"', $content);
    $content = preg_replace('# href="(?!(https?://))(.*\.md)"#i', ' href="?p=' . $path . '\$2"', $content);
} else {
    $content = $html;
}

?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
    <title><?= basename($p) ?></title>
    <link rel="stylesheet" href="github-markdown-css/github-markdown.css">
    <style>
        body {
            box-sizing: border-box;
            min-width: 200px;
            max-width: 980px;
            margin: 0 auto;
            padding: 45px;
        }
    </style>
</head>
<body>
<article class="markdown-body">
    <?= $content; ?>
</article>
</body>
</html>
