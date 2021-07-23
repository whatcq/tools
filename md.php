<?php
/**
 * markdown reader by ?p=[filePath]
 *
 * @cqiu 2016-08-01
 */
/*
# Apache .htaccess locate ['DOCUMENT_ROOT'] @2017/12/11
file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/.htaccess", <<<SETTING

# display Markdown as HTML by default
RewriteEngine on
RewriteRule (.+\.(markdown|mdown|md|mkd))$ {$_SERVER['PHP_SELF']}
RewriteRule (.+\.(markdown|mdown|md|mkd)\-text)$ {$_SERVER['PHP_SELF']} [L]
IndexIgnore .??* *~ HEADER* README.html readme.txt RCS CVS *,v *,t *#

SETTING
    ,FILE_APPEND) && die('.htacess appended!');
die('failed!');
// */

if (isset($_GET['src'])) {
    readfile(preg_replace('#\?.*#', '', $_GET['src']));
    exit;
}

if (isset($_GET['p'])) {
    $p = $_GET['p'];
} elseif (isset($_SERVER['REDIRECT_URL'])) {
    $p = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
} else {
    $p = null;
}

//urlencode之后地址栏不好看
function urlencode2($str)
{
    return str_replace(['+', ' '], ['%2B', '%20'], $str);
}

function mdList($p)
{
    $html = '<li class="title"><a href="./.">' . basename($p) . ' \</a></li>'
        . '<li class="title"><a href="?p=' . dirname($p) . '">..</a></li>';
    foreach (glob("$p/*", GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $html .= '<li><a href="?p=' . urlencode2($dir) . '">' . basename($dir) . '</a></li>';
    }
    $isWWW = strpos($p, $_SERVER['DOCUMENT_ROOT']) === 0;
    foreach (glob("$p/*.{md,markdown}", GLOB_BRACE) as $file) {
        $baseFile = basename($file);
        $href = $isWWW ? substr($file, strlen($_SERVER['DOCUMENT_ROOT'])) : "?p=" . urlencode2($file);
        $html .= '<li class="md ' . ($file == $GLOBALS['p'] ? ' title' : '') . '"><a href="' . $href . '">' . $baseFile . '</a></li>';
    }
    return $html;
}
$base = is_dir($p) && strpos($p, $_SERVER['DOCUMENT_ROOT']) === 0 ? substr($p, strlen($_SERVER['DOCUMENT_ROOT'])) : '';
if (is_file($p)) {
    $content = file_get_contents($p);
} elseif (is_dir($p)) {
    if (file_exists($p . '/README.md')) {
        if (strpos($p, $_SERVER['DOCUMENT_ROOT']) === 0) {
            header('location:' . substr($p, strlen($_SERVER['DOCUMENT_ROOT'])) . '/README.md');
            die;
        }

        $content = file_get_contents($p = $p . '/README.md');
    } else {
        $content = null;
        $html = mdList($p);
    }
} else {
    $content = null;
    var_dump($p);
    $html = 'File not found!';
}
if ($content) {
    //include 'lib/Parsedown.php';
    //$parsedown = new Parsedown();
    //$content = $parsedown->text($content);
    # 某些解析不是github的方式。。
    include 'lib/Parser.php';
    $parsedown = new HyperDown\Parser;
    $parsedown->enableHtml();
    $content = $parsedown->makeHtml($content);

    $path = dirname($p) . DIRECTORY_SEPARATOR;
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
    <link rel="stylesheet" href="<?= dirname($_SERVER['PHP_SELF']) ?>/lib/github-markdown.css">
    <?php //= '<base href="' . $base . '/"/>' ?>

    <style>
        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(194, 161, 161, 0.1);
            border-radius: 10px;
            background-color: #F5F5F5
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
            background-color: #F5F5F5
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 5px;
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .1);
            background-color: rgb(209, 190, 171)
        }

        body {
            margin: 0;
        }

        #sidebar {
            float: left;
            width: 250px;
            position: fixed;
            top: 0;
            bottom: 0;
            background: #f6f8fa;
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
            overflow: auto;
        }

        .sidebar-nav li {
            text-indent: 10px;
            line-height: 30px;
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

        .sidebar-nav li a:before {
            color: #1f9b4c;
            content: '◇ '
        }
        .sidebar-nav li.md a:before {
            color: #1f9b4c;
            content: '▶ '
        }

        #toc {
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            overflow: auto;
            background: #f6f8fa;
            display: none;
            font-size: 85%;
        }

        #toc ol {
            padding-left: 20px;
            border-left: 1px lightgray solid;
        }
        #toc a {
            color: #0366d6;
            text-decoration: none;
        }
        #toc a:visited {
            color: gray;
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
<div id="toc"></div>

<script>
    (function () {
        var tocBox = document.getElementById("toc");
        var documentBox = document.getElementById("content");
        var toc = "";
        var level = 1;//ignore h1
        var duplicateIndex = 0;
        //fix duplicate anchor
        var anchorSet = {};

        documentBox.innerHTML =
            documentBox.innerHTML.replace(
                /<h([2-6]).*?>(.+?)<\/h([2-6])>/gim,
                function (str, openLevel, titleText, closeLevel) {
                    if (openLevel !== closeLevel) {
                        return str;
                    }

                    if (openLevel > level) {
                        toc += (new Array(openLevel - level + 1)).join("<ol>");
                    } else if (openLevel < level) {
                        toc += (new Array(level - openLevel + 1)).join("</ol>");
                    }

                    level = parseInt(openLevel);

                    var anchor = titleText.replace(/<.*?>/g, '').replace(/ /g, "_");
                    if (anchor) {
                        text = anchor;
                        if (!!anchorSet[anchor]) {
                            anchor = anchor + (duplicateIndex++);
                        }
                        anchorSet[anchor] = 1;

                        toc += "<li><a href=\"#" + anchor + "\">" + text
                            + "</a></li>";
                    }

                    return "<h" + openLevel + "  id=\"" + anchor + "\">"
                        + titleText + "</h" + closeLevel + ">";
                }
            );

        if (level) {
            toc += (new Array(level + 1)).join("</ol>");
        }

        if (toc) {
            tocBox.innerHTML += toc;
            tocBox.style.display = 'block';
            documentBox.style.marginRight = '250px';
        }
    })();
</script>

<!-- <link href="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.1.0/styles/a11y-light.min.css" rel="stylesheet"> -->
<!-- <link href="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.1.0/styles/base16/dracula.min.css" rel="stylesheet"> -->
<link href="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.1.0/styles/base16/edge-light.min.css" rel="stylesheet">
<script src="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.1.0/highlight.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.1.0/languages/php.min.js"></script>
<script>hljs.highlightAll();</script>

</body>
</html>
