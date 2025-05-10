<?php
/**
 * 筛选出文件，再进行下一步操作
 * @todo tbc
 * 脚本：批量生成组件
 */
$directory = $_REQUEST['directory'] ?? '';
$include_folders = isset($_REQUEST['include_folders']);
$include_files = isset($_REQUEST['include_files']);
$pattern = $_REQUEST['pattern'] ?? '';
$exclude = $_REQUEST['exclude'] ?? '';

if (($_GET['act'] ?? '') === 'do') {
    $folders = $_POST['selected'] ?? [];
    foreach ($folders as $folder) {
        echo '<li>', $folder;
        continue;
        $html = file_get_contents("$folder/index.html") ?? '';
        $css = file_get_contents("$folder/index.css") ?? '';
        $js = file_get_contents("$folder/index.js") ?? '';
        $name = basename($folder);
        $vue = "D:\laragon\www\cqiu\\video3\\video-uniapp\components/$name.vue";
        // if (file_exists($vue)) {
        //     echo '已存在';
        //     continue;
        // }
        $content = <<< VUE
<template>
$html
</template>

<script>
export default {
  name: '$name',
  $js
};
</script>

<style scoped>
$css
</style>
VUE;

        file_put_contents($vue, $content);
    }
    die;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>文件/文件夹检索</title>
    <style>
        .file-icon::before {
            content: "📄"; /* 文件图标 */
            font-weight: 900;
            margin-right: 5px;
        }

        .folder-icon::before {
            content: "📁"; /* 文件夹图标 */
            font-weight: 900;
            margin-right: 5px;
        }

        body {
            margin: unset;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cfcfcf;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ced5ce;
        }

        .container {
            display: flex;
            gap: 1px;
            overflow: hidden;
        }

        .left-panel {
            flex: 1;
            max-height: 100vh; /* 限制最大高度为视口高度 */
            overflow-y: auto; /* 超出时显示垂直滚动条 */
            padding-right: 1px;
        }

        .left-panel > * {
            margin: 10px;
        }

        .right-panel {
            flex: 1;
            padding: 0;
            border-left: 1px solid #ddd;
            height: 100vh;
            overflow-y: auto;
        }

        .right-panel > iframe {
            width: 99%;
            height: 99%;
            padding: 0;
            margin: 0;
        }

        #form label {
            padding: 5px 1px;
        }

        #form label > span {
            width: 45px;
            display: inline-block;
        }

        #form label.line {
            display: block;
        }

        #form label.line input {
            width: calc(100% - 65px);
        }

        form input[type="submit"] {
            background: antiquewhite;
            border-radius: 5px;
        }

        ol.files-list {
            background: #f8f8ef;
            border-radius: 5px;
            max-height: 500px;
            overflow-y: auto;
        }
        ol.files-list li label {
            display: flex;
        }
        ol.files-list li label input:checked + span {background-color: #f1cd9d;}
        ol.files-list li:nth-child(odd){background-color: #f2f2f2;}

    </style>
</head>
<body>
<div class="container">
    <div class="left-panel">
        <form method="get" id="form">
            <label for="directory" class="line">
                <span>目录:</span>
                <input type="text" id="directory" name="directory" value="<?php echo $directory ?: '.' ?>" size="40">
            </label>
            <label for="match">
                <span>匹配:</span>
                <input type="text" id="pattern" name="pattern" value="<?php echo $pattern; ?>">
            </label>
            <label for="exclude">
                <span>排除:</span>
                <input type="text" id="exclude" name="exclude" value="<?php echo $exclude; ?>">
            </label>
            <label for="include_folders">
                <input type="checkbox" id="include_folders"
                       name="include_folders" <?php echo $include_folders ? 'checked' : ''; ?>>
                文件夹</label>
            <label for="include_files">
                <input type="checkbox" id="include_files"
                       name="include_files" <?php echo $include_files ? 'checked' : ''; ?>>
                文件</label>
            <input type="submit" value="搜索">
            <input type="reset">
        </form>

        <div>搜索结果:</div>
        <form method='post' action='?act=do' target='iframe' id="files-form">
            <ol class="files-list">
                <?php
                if ($directory) {
                    $results = array();

                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::SELF_FIRST,
                        RecursiveIteratorIterator::CATCH_GET_CHILD
                    );

                    foreach ($files as $file) {
                        $filename = $file->getFilename();
                        $ok = !$exclude || strpos($filename, $exclude) === false;
                        if (!$ok) continue;
                        $ok = !$pattern || strpos($filename, $pattern) !== false;
                        if (!$ok) continue;
                        if ($file->isDir() && $include_folders) {
                            $results[] = array(
                                'type' => 'folder',
                                'path' => $file->getPathname(),
                            );
                        } elseif ($file->isFile() && $include_files) {
                            $results[] = array(
                                'type' => 'file',
                                'path' => $file->getPathname(),
                            );
                        }
                    }

                    if (!empty($results)) {
                        foreach ($results as $result) {
                            echo "<li><label><input type='checkbox' name='selected[]' value='" . $result['path'] . "'>";
                            if ($result['type'] === 'folder') {
                                echo "<span class='folder-icon'>{$result['path']}</span>";
                            } else {
                                echo "<span class='file-icon'>{$result['path']}</span>";
                            }
                            echo  "</label></li>";
                        }
                    } else {
                        echo '== 无结果 ==';
                    }
                }
                ?>
            </ol>

            <label><input type='checkbox' id='select-all' onclick='toggleAll()'>全选</label>
            <label><input type='checkbox' id='select-invert' onclick="invertSelection()">反选</label>
            (<span id="select"></span> / <span id="total"></span>)
            <input type='submit' name='submit' value='处理选中项'>
        </form>
    </div>
    <div id="content" class="right-panel">
        <iframe name="iframe"></iframe>
    </div>
</div>

<script>
    function toggleAll() {
        var checkboxes = document.querySelectorAll('#files-form input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = document.getElementById('select-all').checked;
        }
    }

    function invertSelection() {
        var checkboxes = document.querySelectorAll('#files-form input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = !checkboxes[i].checked;
        }
    }
</script>
</body>
</html>