<?php

include '../php-analysis.php';
include '../lib/App.php';

App::run();

class IndexController
{
    private $linkConfigFile = APP_DIR . '/../playground/config-link.json';

    public function actionIndex()
    {
        // q!
        $model = new Model('user');
        $model->create([
            'username' => 'admin',
            'password' => 'password',
            'email' => '<EMAIL>',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function actionAddLink()
    {
        // @todo input-validate-get
        $link = $_REQUEST['link'] ?? '';
        if (empty($link)) {
            res(0, null, 'link is empty!');
        }
        $q = $_REQUEST['q'] ?? '';
        if (empty($q)) {
            res(0, null, 'q is empty!');
        }
        $dsn = parseDsn($q);
        if (!$dsn) {
            res(0, null, 'parse link failed!');
        }
        $links = @json_decode(@file_get_contents($this->linkConfigFile), true);
        $links[$link] = $dsn;
        file_put_contents($this->linkConfigFile, json_encode($dsn, JSON_UNESCAPED_UNICODE));
        res(1, null, 'add link success!');
    }

    function parseDsn($str)
    {
        $str = trim($str);
        if (empty($str)) {
            return false;
        }
        // dsn/url解析
        $info = parse_url($str);
        if (isset($info['scheme'])) {
            return [
                'DBMS' => $info['scheme'],
                'HOST' => isset($info['host']) ? $info['host'] . (isset($info['port']) ? ':' . $info['port'] : '') : '',
                'NAME' => isset($info['path']) ? substr($info['path'], 1) : '',
                'USER' => isset($info['user']) ? $info['user'] : '',
                'PASS' => isset($info['pass']) ? $info['pass'] : '',
                'CHAR' => 'UTF8',
            ];
        }
        preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/', trim($str), $matches);
        if (isset($matches[0])) {
            return [
                'DBMS' => $matches[1],
                'USER' => $matches[2],
                'PASS' => $matches[3],
                'HOST' => $matches[4] . ':' . $matches[5],
                'NAME' => $matches[6],
                'CHAR' => 'UTF8',
            ];
        }
        // .env/.ini解析
        preg_match_all('/(\w+)\s?=\s?(\S*)/', $str, $matches);
        $dsn = [];
        if (!empty($matches[0])) {
            foreach ($matches[1] as $i => $field) {
                if (stripos($field, 'host')) $dsn['HOST'] = $matches[2][$i];
                if (stripos($field, 'port')) $dsn['HOST'] .= ':' . $matches[2][$i];
                if (stripos($field, 'pass')) $dsn['PASS'] = $matches[2][$i];
                if (stripos($field, 'user')) $dsn['USER'] = $matches[2][$i];
                if (stripos($field, 'name')) $dsn['NAME'] = $matches[2][$i];
            }
            return $dsn;
        }
        // multiline define/array map
        preg_match_all('/(["\'])(.*?)\1/', $str, $matches);
        $dsn = [];
        if (!empty($matches[0])) {
            foreach ($matches[2] as $i => $field) {
                if (false !== stripos($field, 'host')) $dsn['HOST'] = $matches[2][++$i];
                if (false !== stripos($field, 'port')) $dsn['HOST'] .= ':' . $matches[2][++$i];
                if (false !== stripos($field, 'pass')) $dsn['PASS'] = $matches[2][++$i];
                if (false !== stripos($field, 'user')) $dsn['USER'] = $matches[2][++$i];
                elseif (in_array(strtolower($field), ['db', 'data', 'database']) || false !== stripos($field, 'name')) $dsn['NAME'] = $matches[2][++$i];
            }
            return $dsn;
        }
        return false;
    }
}

function res($status = 0, $data = null, $message = '')
{
    header('Content-type: application/json;charset=utf-8');
    die(json_encode([
        'status' => $status,
        'info' => $_REQUEST,
        'cookie' => $_COOKIE,
        'data' => $data,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE));
}