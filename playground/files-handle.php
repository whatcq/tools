<?php
/**
 * 筛选出文件，再进行下一步操作
 * @todo tbc
 * 脚本：批量生成组件
 */

if (($_GET['act'] ?? '') === 'do') {
    echo '<pre>';
    // print_r($_POST);
    $folders = $_POST['selected'] ?? [];
    foreach ($folders as $folder) {
        echo '<li>', $folder;
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
<html lang="en">
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
    </style>
</head>
<body>
<form method="post" action="<?php
echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="directory">目录:
        <input type="text" id="directory" name="directory" value="<?php
        echo isset($_POST['directory']) ? $_POST['directory'] : '.'; ?>">
    </label>
    <label for="include_folders">
        <input type="checkbox" id="include_folders" name="include_folders" <?php
        echo isset($_POST['include_folders']) ? 'checked' : ''; ?>>
        文件夹</label>
    <label for="include_files">
        <input type="checkbox" id="include_files" name="include_files" <?php
        echo isset($_POST['include_files']) ? 'checked' : ''; ?>>
        文件</label>
    <label for="search_string">匹配:
        <input type="text" id="search_string" name="search_string" value="<?php
        echo isset($_POST['search_string']) ? $_POST['search_string'] : ''; ?>"><br><br>
    </label>
    <input type="submit" name="submit" value="搜索">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $directory = $_POST['directory'];
    $include_folders = isset($_POST['include_folders']);
    $include_files = isset($_POST['include_files']);
    $search_string = $_POST['search_string'];

    $results = array();

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    foreach ($files as $file) {
        $match = !$search_string || strpos($file->getFilename(), $search_string) !== false;
        if ($file->isDir() && $include_folders && $match) {
            $results[] = array(
                'type' => 'folder',
                'path' => $file->getPathname(),
            );
        } elseif ($file->isFile() && $include_files && $match) {
            $results[] = array(
                'type' => 'file',
                'path' => $file->getPathname(),
            );
        }
    }

    if (!empty($results)) {
        echo "<h2>搜索结果:</h2>";
        echo "<form method='post' action='?act=do' target='iframe'>";
        foreach ($results as $result) {
            echo "<label><input type='checkbox' name='selected[]' value='" . $result['path'] . "'>";
            if ($result['type'] === 'folder') {
                echo "<span class='folder-icon'></span>";
            } else {
                echo "<span class='file-icon'></span>";
            }
            echo $result['path'] . "</label><br>";
        }
        echo "<input type='hidden' name='directory' value='" . $directory . "'>";
        echo "<input type='hidden' name='include_folders' value='" . ($include_folders ? 'on' : '') . "'>";
        echo "<input type='hidden' name='include_files' value='" . ($include_files ? 'on' : '') . "'>";
        echo "<input type='hidden' name='search_string' value='" . $search_string . "'>";
        echo "<input type='submit' name='submit' value='处理选中项'>";
        echo "</form>";
    } else {
        echo "未找到匹配的文件或文件夹。";
    }
}
?>
<iframe name="iframe" style="width: 100%;"></iframe>
</body>
</html>