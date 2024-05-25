<?php
/**
 * 全量测试脚本：多个参数组合=>结果表
 * @author      Cqiu
 * @date        2024-05-25
 */

function comb(array $array, $r = []): Generator
{
    if (empty($array)) {
        yield $r;

        return;
    }
    $k = key($array);
    foreach (array_shift($array) as $v) {
        $r[$k] = $v;
        yield from comb($array, $r);
    }
}

function runAll($conditions, $keys = [])
{
    // if (!$keys && !$conditions instanceof Generator) $keys = array_keys($conditions[0]);
    echo '<table style="min-width: 200px" border="0" cellpadding="3">';
    echo '<tr><th style="min-width: 15px;"></th><th>'
    , implode('</th><th>', $keys)
    , '</th><th>result</th>'
    , '</th></tr>';
    $i = 1;
    foreach ($conditions as $_) {
        echo '<tr><td>', $i++, '</td><td>'
        , implode('</td><td>', array_map(fn($_) => colorful($_), $_))
        , '</td><td>', colorful(getTestResult($_)), '</td>'
        , '</td></tr>';
    }
    echo '</table>';
}

function colorful($str)
{
    return '<span style="color:' . stringToColor($str) . '">' . $str . '</span>';
}

function stringToColor($str)
{
    // 使用 crc32 函数将字符串转换为数字
    $hash = crc32($str);

    // 将数字映射到 RGB 颜色
    $r = ($hash & 0xFF0000) >> 16;
    $g = ($hash & 0x00FF00) >> 8;
    $b = $hash & 0x0000FF;

    // 返回颜色值
    return sprintf('#%02X%02X%02X', $r, $g, $b);
}

// ---------------
// 参数 => 集合
$input = [
    'a' => [1, 0],
    'b' => [1, 0],
];
function getTestResult($vars)
{
    extract($vars);
    $result = $a && !$b || !$a && $b;

    return var_export($result, 1);
}

echo <<<CSS
<link rel="stylesheet" href="../DBQ/dbq.css">
<style> td {text-align: center}</style>
CSS;

runAll(comb($input), array_keys($input));
