<?php

// 写接口很简单的
// router
// request method/params/validate
// response

// 真正写起来发现不是3-4行代码的问题，越来越多。

// GET    /todo
// GET    /todo/1
// POST   /todo
// PUT    /todo/1
// PATCH  /todo/1
// DELETE /todo/1

// bash和cmd的区别：引号、转义字符==
// curl -X POST http://127.0.0.1/test/cqiu-tools/json-server.php?object=todo\&id=0 -H 'Content-Type: application/json' -d '{"item":"json-server in php", "date":"2018-11-23"}'
// curl -X POST http://127.0.0.1/test/cqiu-tools/json-server.php?object=todo -H 'Content-Type: application/json;charset=gbk' -d '{"item":"大学城 海哥 跆拳道", "date":"2018-11-24"}'
// curl http://127.0.0.1/test/cqiu-tools/json-server.php?object=todo\&id=1
// curl -X DELETE http://127.0.0.1/test/cqiu-tools/json-server.php?object=todo\&id=1

header('Content-Type:application/json;charset=utf-8');

$dataFile = 'db.json';

$object = $_GET['object'] ?? '';
$id = $_GET['id'] ?? null;

if (!$object) {
    response(['--']);
}
xdebug_disable();
ini_set('xdebug.overload_var_dump', 0);

var_dump($object, $id);

// var_dump($_POST); 不行。。
$input = file_get_contents('php://input');
$encode = mb_detect_encoding($input, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
$input = mb_convert_encoding($input, 'UTF-8', $encode);
$post = json_decode($input, true);
// print_r($post);

$data = json_decode(file_get_contents($dataFile), true);
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($data[$object][$id])) {
            response($data[$object][$id]);
        }

        if (isset($data[$object])) {
            if (is_null($id)) {
                response($data[$object]);
            }

            response(['object' => $object, 'id' => $id], 0);
        }
    case 'POST':
    case 'PUT':
        if (is_null($post)) {
            response(['empty'], 0);
        }

        isset($data[$object][$id])
        ? ($data[$object][$id] = array_merge($data[$object][$id], $post))
        : (is_null($id)
            ? ($data[$object][] = $post)
            : ($data[$object][$id] = $post)
        );

        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        response($post);
    case 'DELETE':
        if (isset($data[$object][$id])) {
            unset($data[$object][$id]);
            file_put_contents($dataFile, json_encode($data));
        }
        response(['id' => $id]);
}

function response($data, $result = 1)
{
    exit(json_encode([
        'result' => $result,
        'data' => $data,
    ]));
}
