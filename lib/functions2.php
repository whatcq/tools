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
