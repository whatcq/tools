<?php

/**
 * 快速获得js的cdn链接
 * @author Cqiu
 */

if (isset($_REQUEST['search'])) {
    $api = 'https://api.cdnjs.com/libraries';
    $params = [
        'search'        => $_REQUEST['search'],
        'search_fields' => 'name',
        'fields'        => 'name,latest',
        'limit'         => 3,
        // 'output'        => 'human',
    ];
    $data = json_decode(file_get_contents($api . '?' . http_build_query($params)), true);
    $url = $data['results'][0]['latest'];
} else {
    $path = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
    $url = 'https://cdnjs.cloudflare.com/' . ltrim($path, '/');

    $_REQUEST['cache'] = 1;
}

if (isset($_REQUEST['cache'])) {
    $cachePath = cacheFile($url);
    $newUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
        . '://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['SCRIPT_NAME']) . $cachePath;
    die($newUrl);
}

die($url);

function cacheFile($url)
{
    $pis = parse_url($url);
    $file = './' . $pis['path'];
    if (!file_exists($file)) {
        makeFolder(dirname($file));
        $content = file_get_contents($url);
        if ($content) {
            file_put_contents($file, $content);
        }
    }

    return $pis['path'];
}

function makeFolder($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}
