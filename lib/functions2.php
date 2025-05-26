<?php

/**
 * c/java等代码转成php
 * @param $s
 * @return array|string|string[]|null
 */
function c2php($s)
{
    $s = str_replace(['void ', 'int '], '', $s);
    $s = preg_replace_callback('/[_a-z][_\w]*\b(?!\()/m', function ($matches) {
        $item = $matches[0];
        if (in_array($item, ['function', 'if', 'else', 'while', 'for', 'foreach', 'return', 'true', 'false', 'break',])) {
            return $item;
        }

        return '$' . $item;
    }, $s);

    return $s;
}

/**
 * 翻译
 * @param array $lines
 * @return array|false
 */
function translate(array $lines)
{
    include_once 'functions.php';
    if (preg_match('/[^\x00-\x7F]/', $lines[0])) {
        $from = 'chinese_simplified';
        $to = 'english';
    } else {
        // is ascii
        $from = 'english';
        $to = 'chinese_simplified';
    }
    $map = [];
    foreach (array_chunk($lines, 100) as $chunk) {
        $content = curl_post(
            'https://api.translate.zvo.cn/translate.json?v=2.4.2.20230719',
            [
                'from' => $from,
                'to' => $to,
                'text' => json_encode($chunk, JSON_UNESCAPED_UNICODE),
            ],
            ['Content-Type: application/x-www-form-urlencoded'],
            5
        );
        $result = json_decode($content, true);
        $map = array_merge($map, array_combine($chunk, $result['text'] ?? $chunk));
    }

    return $map;
}

function cache_get($key, $expire = 0)
{
    $file = 'cache/' . $key;

    if ($expire > 0) {
        $mtime = filemtime($file);
        if ($mtime + $expire > time()) {
            return unserialize(file_get_contents($file));
        }
        unlink($file);
        return null;
    }

    if (file_exists($file)) {
        return unserialize(file_get_contents($file));
    }

    return null;
}

function cache_set($key, $value)
{
    $file = 'cache/' . $key;
    if (is_null($value)) {
        return unlink($file);
    }
    is_dir('cache') or mkdir('cache', 0777, true);

    return file_put_contents($file, serialize($value));
}

function cache_getOrSet($key, $value, $expire = 0)
{
    $cachedValue = cache_get($key, $expire);
    if ($cachedValue !== null) {
        return $cachedValue;
    }
    $value = is_callable($value) ? call_user_func($value) : $value;
    cache_set($key, $value);

    return $value;
}

function confirmPost()
{
    if (time() - ($_POST['confirm'] ?? 0) < 100) {
        return true;
    }
    ?>
    <style>label{width: 100px; display: inline-block;}input{width: 500px;}</style>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" enctype="<?=
        $_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded' ?>">
        <input type="hidden" name="confirm" value="<?= time() ?>">
        <?php
        foreach ($_POST as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k1 => $v1) {
                    echo "<label>{$k}[{$k1}]:</label><input type=\"text\" name=\"{$k}[{$k1}]\" value=\"" . htmlspecialchars($v1) . '"></label><br />';
                }
            } else {
                echo "<label>{$k}:</label><input type=\"text\" name=\"{$k}\" value=\"" . htmlspecialchars($v) . '"></label><br />';
            }
        }
        ?>
        <input type="submit" value="== 确认 ==">
    </form>
    <?php
    exit;
}
