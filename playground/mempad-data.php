<?php
include_once '../php-analysis.php';
require_once '../lib/App.php';

$configFile = './config2.database.php';
$configs = [
        'defaultController' => 'Index',
        'defaultAction' => 'index',
    ] + include $configFile;

App::run($configs);

class IndexController
{
    public function actionIndex()
    {
        echo 'hello world';
    }

    public function actionAudios()
    {
        // 获取所有音频文件
        $files = glob(APP_DIR.'/../data/*');
        foreach($files as &$file){
            $file = str_replace(APP_DIR.'/', '', $file);
        }
        return $files;
    }

    // 接收提交表单
    function actionHeatmap()
    {
        $GLOBALS['__no_debug_bar'] = 1;
        header("Content-type: text/plain; charset=utf-8");
        $table = 'log';
        $model = new Model("local.cqiu.$table");
        $r = $model->findAll([], null, 'date(`created_at`) as `date`, count(*) as `count`', null, 'created_at');
        echo "date,count\n";
        while ($row = current($r)) {
            echo $row['date'] . ',' . $row['count'] . "\n";
            next($r);
        }
    }

    function actionView()
    {
        $date = date('Y-m-d', strtotime($_REQUEST['date']));

        $table = 'log';
        $model = new Model("local.cqiu.$table");
        $r = $model->findAll(['`created_at` BETWEEN :a AND :b', ':a' => $date, ':b' => $date . ' 23:59:59']);
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($r);
    }

    function actionUpdate2Bb()
    {
        $files = [
            'D:\mysoft\fuer\jianguoyun\mempad\2011-cqiu_diary.lsf',
            'D:\mysoft\fuer\jianguoyun\mempad\2012-dairy.lsf',
            'D:\mysoft\fuer\jianguoyun\mempad\2016-meishi.lsf',
            'D:\mysoft\fuer\jianguoyun\mempad\2017-txz.lsf',
            'D:\mysoft\fuer\jianguoyun\mempad\2020-mdwl.lsf',
            'D:\mysoft\fuer\jianguoyun\mempad\2021-cqgg.lsf',

            'D:\mysoft\fuer\jianguoyun\mempad\2022-fmcw.lst',
            'D:\mysoft\fuer\jianguoyun\mempad\2024wxh.lsf',
            'D:\mysoft\fuer\jianguoyun\home.hp.lsf',
            //'D:\mysoft\fuer\jianguoyun\67hang.lsf',
            'D:\mysoft\mempad64\67hang.lsf',
            'D:\mysoft\fuer\jianguoyun\2025yy.lsf',
            //'D:\mysoft\fuer\jianguoyun\pc-i11.lsf',
            'D:\mysoft\mempad64\pc-i11.lsf',
        ];
        // 'D:\mysoft\fuer\jianguoyun\67hang.lsf'
        $files = $_REQUEST['selected'] ?? ['D:\mysoft\mempad64\67hang.lsf'];
        $files or die('no file selected');

        $table = 'log';

        //die('are you sure?');
        echo '<pre>';
        $GLOBALS['model'] = new Model("local.cqiu.$table");
        $GLOBALS['model']->execute("SET FOREIGN_KEY_CHECKS=0;TRUNCATE TABLE `$table`;");
        foreach ($files as $file) {
            echo $file, '<br>';
            $fp = new mempad2db($file);
            //$fp->write2db();
            //$fp->batchInsert();
            $fp->update();
        }
        echo '</pre>';
    }
}

define('SP', chr(0));

/*
CREATE TABLE `log` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `parent_id` int NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '日志内容',
  `source` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '来源',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='日记表'
*/

class mempad2db
{
    private $file;

    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new Exception('File not found:' . $file);
        }
        $this->file = $file;
    }

    public function fixDate($title)
    {
        $monTh = array(
            '01' => '一月',
            '02' => '二月',
            '03' => '三月',
            '04' => '四月',
            '05' => '五月',
            '06' => '六月',
            '07' => '七月',
            '08' => '八月',
            '09' => '九月',
            '10' => '十月',
            '11' => '十一月',
            '12' => '十二月',
        );
        krsort($monTh);
        return substr(str_replace(array_values($monTh), array_keys($monTh), $title), 0, 10);
    }

    /**
     * 写入带parent_id
     */
    public function write2db()
    {
        /** @var Model $model */
        $model = $GLOBALS['model'];
        $data = file_get_contents($this->file);
        $sourceFile = basename($this->file);
        $tmp = explode(SP, $data);
        unset($data);
        $nodes = [];
        $prevLevel = 0;
        $rows = [];
        foreach ($tmp as $i => $v) {
            if ($i % 2 == 0 && $v && ($level = ord(substr($v, 0, 1))) <= $prevLevel + 1) {
                if ($prevLevel != $level && $rows) {
                    //print_r($nodes);
                    $model->batchInsert($rows);//"SELECT id FROM `$table` ORDER BY id DESC LIMIT 1"
                    $id = $model->find([], 'id DESC', 'id')['id'];
                    echo $id, "<br>";
                    $nodes[$prevLevel] = [
                        'title' => end($rows)['title'] ?? '',
                        'id' => $id,
                    ];
                    $rows = [];
                }
                $prevLevel = $level;

                $title = substr($v, 1);
                $content = $tmp[$i + 1];
                $parentId = $nodes[$level - 1]['id'] ?? 0;
                $newTitle = $level > 1 ? $nodes[$level - 1]['title'] . '/' . $title : $title;
                $createdAt = ($t = strtotime($this->fixDate($newTitle))) ? date('Y-m-d H:i:s', $t) : '2010-01-01 00:00:00';

                $rows[] = [
                    'parent_id' => $parentId,
                    'title' => $newTitle,
                    'content' => $content,
                    'source' => $sourceFile,
                    'created_at' => $createdAt,
                ];
            }
            //$i>18 && exit;
        }
        $rows && $model->batchInsert($rows);
    }

    /**
     * 批量插入数据，无parent_id
     */
    public function batchInsert()
    {
        $model = $GLOBALS['model'];
        $data = file_get_contents($this->file);
        $sourceFile = basename($this->file);
        $tmp = explode(SP, $data);
        unset($data);
        $nodes = [];
        $prevLevel = 0;
        $rows = [];
        foreach ($tmp as $i => $v) {
            if ($i % 2 == 0 && $v && ($level = ord(substr($v, 0, 1))) <= $prevLevel + 1) {
                $title = substr($v, 1);
                $content = $tmp[$i + 1];
                $parentId = 0;
                $newTitle = $level > 1 ? $nodes[$level - 1]['title'] . '/' . $title : $title;
                $createdAt = ($t = strtotime($this->fixDate($newTitle))) ? date('Y-m-d H:i:s', $t) : '2010-01-01 00:00:00';

                $nodes[$level] = ['title' => $newTitle];
                $prevLevel = $level;

                $rows[] = [
                    'parent_id' => $parentId,
                    'title' => $newTitle,
                    'content' => $content,
                    'source' => $sourceFile,
                    'created_at' => $createdAt,
                ];
                if (count($rows) > 200) {
                    echo $model->batchInsert($rows);
                    $rows = [];
                }
            }
        }
        $rows && print($model->batchInsert($rows));
    }

    public function update()
    {
        $model = $GLOBALS['model'];
        $data = file_get_contents($this->file);
        $sourceFile = basename($this->file);
        $tmp = explode(SP, $data);
        unset($data);
        $nodes = [];
        $prevLevel = 0;

        foreach ($tmp as $i => $v) {
            if ($i % 2 == 0 && $v && ($level = ord(substr($v, 0, 1))) <= $prevLevel + 1) {
                $title = substr($v, 1);
                //_log($i, $nodes, $level, $title);
                $newTitle = $level > 1 ? $nodes[$level - 1]['title'] . '/' . $title : $title;
                $content = $tmp[$i + 1];
                $createdAt = ($t = strtotime($this->fixDate($newTitle))) ? date('Y-m-d H:i:s', $t) : '2010-01-01 00:00:00';

                $model->upsert(['title' => $newTitle, 'source' => $sourceFile], ['content' => $content, 'created_at' => $createdAt]);

                $nodes[$level] = ['title' => $newTitle,];
                $prevLevel = $level;
            }
        }
    }
}
