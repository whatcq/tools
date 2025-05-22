<?php

class Str
{
    /**
     * 生成随机字符串 需求很多样化
     * - 密码、验证码
     * - 简单随机字符串 uniqid()就行，基于时间戳13位
     * @param int $length
     * @return string
     * @throws \Random\RandomException
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    public static function random2($length = 10, $chars = '0aA~'): string
    {
        $map = [
            '0' => '0123456789',
            'a' => 'abcdefghijklmnopqrstuvwxyz',
            'A' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '~' => '~!@#$%^&*()_-=+{}[]|:;,.<>?',
        ];
        $characters = '';
        foreach (str_split($chars) as $x) {
            $characters .= $map[$x] ?? $x;
        }
        $n = strlen($characters) - 1;
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            // mt_rand 快速生成，随机性较低，不适合安全应用
            $randomString .= $characters[random_int(0, $n)];
        }
        return $randomString;
    }

    /**
     * Safely casts a float to string independent of the current locale.
     * The decimal separator will always be `.`.
     *
     * @param float|int $number a floating point number or integer.
     * @return string the string representation of the number.
     * @since 2.0.13
     */
    public static function floatToString($number)
    {
        // . and , are the only decimal separators known in ICU data,
        // so its safe to call str_replace here
        return str_replace(',', '.', (string)$number);
    }

    #### Inflector
    public static function toCamel($word, $delimiter = '_', $lcfirst = true)
    {
        $s = str_replace(' ', '', ucwords(str_replace($delimiter, ' ', $word)));
        if ($lcfirst) $s[0] = strtolower($s[0]);
        return $s;
    }

    public static function camelTo($word, $delimiter = '_')
    {
        // snake: $delimiter = '_'
        // kebab: $delimiter = '-'
        // words: $delimiter = ' ' then you ucwords
        return strtolower(preg_replace('/(?<![A-Z])[A-Z]/', $delimiter . '$0', $word));
    }
}