<?php

// 假设你的OpenAPI JSON文件名为api.json
$jsonFile = './校园集市.openapi.json';

// 读取JSON文件
$jsonContent = file_get_contents($jsonFile);

// 解析JSON内容为PHP数组
$openApiData = json_decode($jsonContent, true);

// 检查是否解析成功
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error parsing JSON: ' . json_last_error_msg());
}

echo <<<STYLE
<style type="text/css">
*{font-family: 'Microsoft YaHei',Arial,serif;}
a{display: inline-block; padding: 2px 5px;background: #c6dfff;border-radius: 3px;}
label{clear:left; width: 130px;display: inline-block;color: gray;font-style: italic;}
tr:nth-child(odd),li:nth-child(odd){background-color: #f2f2f2;}
tr:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
tr:nth-child(5n+0),li:nth-child(5n+0) {background-color: #e9e6e6;}
tr:hover,li:hover{background: #c3e9cb;}
pre{margin:0;}
i{font-size:60%;color:gray;}
table{font-size:80%}
td{word-break: break-all}
td {white-space: nowrap;overflow: hidden;text-overflow: ellipsis;max-width: 0;}
td span {display: inline-block;max-width: 100%;}
.fixed-header {position: relative;}
.fixed-header > * {position: sticky;top: 0;resize: horizontal;overflow: auto;background: #c3e9cb;}
</style>
STYLE;

// 输出表格头部
echo "<table style='width:100%;'>";
echo "<tr class='fixed-header'><th>Method</th><th>接口名</th><th>Summary</th><th>Description</th></tr>";

// 遍历paths对象，它包含了所有的API路径
foreach ($openApiData['paths'] as $path => $pathItem) {
    // 遍历每个路径下的操作（如get, post, put, delete等）
    foreach ($pathItem as $method => $operation) {
        // 输出每一行的数据
        echo "<tr>";
        echo "<td>" . strtoupper($method) . "</td>";
        echo "<td>" . htmlspecialchars($path) . "</td>";
        echo "<td>" . (isset($operation['summary']) ? htmlspecialchars($operation['summary']) : 'N/A') . "</td>";
        echo "<td>" . (isset($operation['description']) ? htmlspecialchars($operation['description']) : 'N/A') . "</td>";
        echo "</tr>";
    }
}

// 输出表格结束标签
echo "</table>";
