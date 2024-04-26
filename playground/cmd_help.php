<?php

/**
 * 显示命令参数form，用于拼装
 */
$cmd = $_REQUEST['cmd'] ?? 'wget';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $cmd ?> Command Generator</title>
    <link rel="stylesheet" type="text/css" href="../lib/base.css"/>
    <link rel="stylesheet" type="text/css" href="../DBQ/dbq.css"/>
    <style>
        ol {
            padding-left: 30px;
            font-size: 12px
        }
        ol input[type="text"] {
            width: 50px;
            margin-left: 3px;
            border: none;
            border-bottom: 1px solid #1e8d0d;
        }
    </style>
    <script>
        function generateCommand() {
            var command = '<?= $cmd ?>';

            // 获取勾选的选项
            var options = document.querySelectorAll('input[type="checkbox"]:checked');
            for (var i = 0; i < options.length; i++) {
                command += ' ' + options[i].value;
            }

            // 获取输入的参数值
            var inputs = document.querySelectorAll('input[type="text"]');
            for (var j = 0; j < inputs.length; j++) {
                if (inputs[j].value.trim() !== '') {
                    command += ' ' + inputs[j].value.trim();
                }
            }

            // 显示生成的命令行
            document.getElementById('command').textContent = command;
        }
    </script>
    <style>
        label {
            min-width: 250px;
            display: inline-block
        }
    </style>
</head>
<body>
<h1><?= $cmd ?> Command Generator</h1>
<form>
    <?php

    // 执行 wget --help 命令获取帮助信息
    $helpOutput = shell_exec("$cmd --help");

    // 合并折行
    $helpOutput = preg_replace("#\n +(?=(\w|\(|'))#", " ", $helpOutput);
    // echo '<pre>' . $helpOutput . '</pre>';die;

    // 解析帮助信息
    $lines = explode("\n", $helpOutput);
    // echo '<pre>' . print_r($lines, 1) . '</pre>';die;
    $sentences = [];
    $ul = false;
    ob_start();
    foreach ($lines as $line) {
        $line = trim($line);
        if (!preg_match('/^(-{1,2}[a-zA-Z0-9\-_\[\]=\/]+)(?:(,\s+[a-zA-Z0-9\-\[\]=\/]+))?\s+(.*)/', $line, $matches)) {
            (print '</ol>') && $ul = false;
            echo '<span>' . $line . '</span><br>', PHP_EOL;
            continue;
        }

        $ul or (print '<ol>') && $ul = true;

        $option = $matches[1] . $matches[2];
        $description = $matches[3];
        // 处理带参数的
        $var = '';
        if (strpos($option, '=')) {
            [$option, $var] = explode('=', $option);
        }
        $description && $sentences[] = $description;

        // 输出复选框和描述
        echo '<li><label><input type="checkbox" value="' . $option . '">' . $option;
        $var && print('<input type="text" placeholder="' . $var . '">');
        echo '</label> ';
        echo '<span>' . $description . '</span></li>', PHP_EOL;
    }
    $html = ob_get_clean();
    include '../lib/functions2.php';
    $sentences
    && ($fanyi = cache_getOrSet('fanyi_' . md5(serialize($sentences)), translate($sentences)))
    && $html = strtr($html, $fanyi);
    echo $html;
    ?>
    <br>
    <h2>参数</h2>
    <input type="text" placeholder="URL" style="width: 300px;"><br><br>
    <input type="text" placeholder="保存路径" style="width: 300px;"><br><br>
    <input type="text" placeholder="其他参数" style="width: 300px;"><br><br>
    <button type="button" onclick="generateCommand()">生成命令</button>
</form>
<br>
<h2>生成的命令行</h2>
<pre id="command"></pre>
</body>
</html>
