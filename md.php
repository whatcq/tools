<?php
/**
 * markdown reader by ?p=[filePath]
 *
 * @cqiu 2016-08-01
 *
 * # Apache .htaccess locate ['DOCUMENT_ROOT'] @2017/12/11
 * # display Markdown as HTML by default
 * RewriteEngine on
 * RewriteRule (.+\.(markdown|mdown|md|mkd))$ ['PHP_SELF']
 * RewriteRule (.+\.(markdown|mdown|md|mkd)\-text)$ ['PHP_SELF'] [L]
 * #RewriteRule (.+\.(markdown|mdown|md|mkd))$ ['PHP_SELF']?p=['DOCUMENT_ROOT']/$1
 */

if (isset($_GET['src'])) {
    readfile(preg_replace('#\?.*#', '', $_GET['src']));
    exit;
}

include 'parsedown/Parsedown.php';

$parsedown = new Parsedown();

//$p = isset($_GET['p']) ? $_GET['p'] : null;
if (isset($_GET['p'])) {
    $p = $_GET['p'];
} elseif (isset($_SERVER['REDIRECT_URL'])) {
    $p = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
} else {
    $p = null;
}

function mdList($p)
{
    $html = '<li class="title"><a href="?p=' . dirname($p) . '">' . basename($p) . ' \</a></li>';
    foreach (glob($p . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $html .= '<li><a href="?p=' . $dir . '">' . basename($dir) . '</a></li>';
    }
    foreach (glob($p . DIRECTORY_SEPARATOR . '*.{md,markdown}', GLOB_BRACE) as $file) {
        $html .= '<li' . ($file == $GLOBALS['p'] ? ' class="title"' : '') . '><a href="?p=' . $file . '">' . basename($file) . '</a></li>';
    }
    return $html;
}
if (is_file($p)) {
    $content = file_get_contents($p);
} elseif (is_dir($p)) {
    if (file_exists($p . '/README.md')) {
        $content = file_get_contents($p = $p . '/README.md');
    } else {
        $content = null;
        $html = mdList($p);
    }
} else {
    $content = null;
    $html = 'File not found!';
}
if ($content) {
    $path = dirname($p) . DIRECTORY_SEPARATOR;
    $content = $parsedown->text($content);
    $content = preg_replace('#<img src="(?!((https?:)?//))(.*)"#i', '<img src="?src=' . $path . '\$3"', $content);
    $content = preg_replace('#<a href="(?!(https?://))(.*\.md)"#i', '<a href="?p=' . $path . '\$2"', $content);
    $content = preg_replace('#<a href="&lt;(.*\.md)>"#i', '<a href="?p=' . $path . '\$1"', $content);
} else {
    $content = $html;
}

?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
    <title><?= basename($p) ?></title>
    <link rel="stylesheet" href="<?= dirname($_SERVER['PHP_SELF']) ?>/github-markdown-css/github-markdown.css">
    <style>
        body {
            margin: 0;
        }

        #sidebar {
            float: left;
            width: 250px;
            position: fixed;
            top: 0;
            bottom: 0;
        }

        #content {
            width: *;
            margin-left: 250px;
            position: relative;
        }

        article {
            box-sizing: border-box;
            min-width: 200px;
            max-width: 980px;
            margin: 0 auto;
            padding: 45px;
        }

        .sidebar-nav {
            position: absolute;
            top: 0;
            width: 250px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .sidebar-nav li {
            text-indent: 20px;
            line-height: 40px;
        }

        .sidebar-nav li a {
            display: block;
            text-decoration: none;
            color: #999999;
        }

        .sidebar-nav li a:hover {
            text-decoration: none;
            color: #fff;
            background: rgba(15, 11, 11, 0.6);
        }

        .sidebar-nav li a:active, .sidebar-nav li a:focus {
            text-decoration: none;
        }

        .sidebar-nav > .title a {
            color: #000;
        }

    </style>
</head>
<body>
<div id="sidebar" class="sidebar-nav">
    <?= mdList($content === null ? $p : dirname($p)) ?>
</div>
<div id="content">
    <article class="markdown-body">
        <?= $content; ?>
    </article>
</div>
</body>
</html>
