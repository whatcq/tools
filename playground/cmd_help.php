<?php
$cmd = $_REQUEST['cmd'] ?? 'wget';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $cmd ?> Command Generator</title>
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
<h1>wget Command Generator</h1>
<form>
    <?php

    // 执行 wget --help 命令获取帮助信息
    $helpOutput = shell_exec("$cmd --help");

    // 合并折行
    $helpOutput = preg_replace("#\n +(?=(\w|\())#", " ", $helpOutput);
    // echo '<pre>' . $helpOutput . '</pre>';die;

    // 解析帮助信息
    $lines = explode("\n", $helpOutput);

    $sentences = [];
    ob_start();
    foreach ($lines as $line) {
        $line = trim($line);
        // 检查是否为选项行
        if (substr($line, 0, 1) === '-') {
            // 提取选项名称和描述
            preg_match('/^\s*(.+)\s{3,}(.*)$/', $line, $matches);
            if (!isset($matches[2])) {
                preg_match('/^\s*(\S+\s{2}\S+)\s+(.*)$/', $line, $matches);
            }

            // @todo 还有些情况没有处理

            $option = $matches[1];
            // 处理带参数的
            $var = '';
            if (strpos($option, '=')) {
                [$option, $var] = explode('=', $option);
            }
            $sentences[] = $description = $matches[2];

            // 输出复选框和描述
            echo '<label><input type="checkbox" value="' . $option . '">' . $option;
            echo '</label> ';
            $var && print('<input type="text" placeholder="' . $var . '">');
            echo '<span>' . $description . '</span><br>';
        } else {
            echo '<span>' . $line . '</span><br>';
        }
    }
    $html = ob_get_clean();
    include '../lib/functions2.php';
    $sentences && $fanyi = translate($sentences);
    $fanyi && $html = strtr($html, $fanyi);
    // echo '<pre>', print_r($fanyi, true), '</pre>';
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
