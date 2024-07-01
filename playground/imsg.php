<?php

/**
 * 在线note，不登录传递消息文本
 * - https://getnote.top/cqiu notepad.cc副本
 * - https://netcut.cn/cqiu 可设置密码
 * - https://txtpad.cn/cqiu 多标签
 * - https://cmd.im/58m5 不能自定义key,也不能改!
 * @author Cqiu
 */

include __DIR__ . '/../lib/functions.php';

/**
 * https://netcut.cn/pages/api.html
 * js版: https://xiuhengwu.github.io/html5-text-editor/
 */
class TextDb
{
    const API = 'https://api.textdb.online/update/';
    const URL = 'https://textdb.online/';

    public static function get($key = 'cqiu12')
    {
        return curl_get(self::URL . $key);
    }

    public static function set($key, $value)
    {
        $data = ['key' => $key, 'value' => $value];
        if (isset($value[200])) {
            return curl_post(self::API, $data);
        }
        return curl_get(self::API . '?' . http_build_query($data));
    }

    public static function del($key)
    {
        return curl_get(self::API . '?' . http_build_query(['key' => $key, 'value' => '']));
    }
}

class Note
{
    const URL = 'https://getnote.top/';

    public static function get($key = 'cqiu')
    {
        $html = curl_get(self::URL . $key);
        preg_match('/<textarea[^>]*id="content"[^>]*>(.*?)<\/textarea>/s', $html, $matches);
        return html_entity_decode($matches[1]);
    }

    public static function set($key, $value)
    {
        return curl_post(self::URL . $key, ['t' => $value]);
    }
}
