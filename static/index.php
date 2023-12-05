<?php

/**
 * 快速获得js的cdn链接
 * - /tools/static/?search=clipboard&cache 缓存到本地，否则用cdn
 * @author Cqiu
 */

################ list all ###################
if (isset($_REQUEST['list'])) {
    function getAllFilePaths($dir, $relativePath = '')
    {
        $filePaths = array();
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $filePath = $dir . '/' . $file;
                if (is_file($filePath) && in_array(substr($file, -3), ['.js', 'css', 'htm', 'tml'])) {
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

    echo <<<SCRIPT
<script>
function copyToClipboard(obj) {
    obj.select();
    typeof window.clipboardData==='object' 
        ? window.clipboardData.setData('text', obj.value)
        : document.execCommand('copy');
}
</script>
SCRIPT;

    foreach (getAllFilePaths('.') as $v) {
        echo <<<LINE
<li><input id="filePath" type="text" value="$v" onfocus="copyToClipboard(this)" size="100" />
LINE;
    }
    die;
}

############# search | cache ############
if (isset($_REQUEST['search'])) {
    $data = getLibs();
    echo '<pre>';
    print_r($data);
    die('<pre>');
}
if (isset($_REQUEST['cache'])) {
    $_REQUEST['search'] = $_REQUEST['cache'];
    $data = getLibs();
    $url = $data['results'][0]['latest'] ?? '';
    $url or die('no url!!!');
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

function getLibs()
{
    $api = 'https://api.cdnjs.com/libraries';
    $params = [
        'search' => $_REQUEST['search'],
        'search_fields' => 'name',
        'fields' => 'name,latest',
        'limit' => 3,
        // 'output'        => 'human',
    ];
    return json_decode(file_get_contents($api . '?' . http_build_query($params)), true);
}

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
