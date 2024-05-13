<?php
/**
 * 脚本：批量迁移引用类的命名空间
 * @author: Cqiu
 * @date  : 2024-05-13
 */

function findClassesWithNamespace($folder, $namespace)
{
    $files = getAllPHPFiles($folder);
    $classes = [];

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $matches = findNamespaceMatches($content, $namespace);

        if (!empty($matches)) {
            $classes[$file] = $matches;
        }
    }

    return $classes;
}

function getAllPHPFiles($folder)
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    foreach ($iterator as $path => $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $path;
        }
    }

    return $files;
}

function findNamespaceMatches($content, $namespace)
{
    $pattern = '/use\s+([\w_\\\]+' . preg_quote($namespace, '/') . '[\w_\\\]+);/';
    preg_match_all($pattern, $content, $matches);

    return $matches[1];
}

function namespace2class($namespace)
{
    return BASE_DIR . '/' . str_replace('\\', '/', $namespace) . '.php';
}

// step 0: 设置要搜索的文件夹路径和命名空间
$folder = 'D:\laragon\www\fm\fmpets-Yii\inventory';
define('BASE_DIR', dirname($folder));
$namespace = '\\tp\\';// 需要替换的命名空间包含

echo '<pre>';
// 执行搜索: 哪些文件包含了哪些空间 要替换的
$classes = findClassesWithNamespace($folder, $namespace);
// step 1: just view finds
print_r($classes);
die;

// 要替换的命名空间
$namespaces = array_unique(
    array_reduce($classes, function ($carry, $item) {
        return array_merge($carry, $item);
    }, [])
);
sort($namespaces);

// 设置要迁移的文件夹路径和旧的命名空间
$folders = [];
foreach ($namespaces as $item) {
    $folders[dirname($item)] = 0;
}
// step 2: view namespace folders
// var_export($folders); // => $replaces
// die;

$replaces = array(
    'common\\constants\\tp'                      => 'inventory\\modules\\v1\\constants',
    'common\\constants\\tp\\delivery'            => 'inventory\\modules\\v1\\constants',
    'common\\constants\\tp\\warehouse'           => 'inventory\\modules\\v1\\constants',
    'common\\services\\tp\\Delivery\\Helper'     => 'inventory\\modules\\v1\\helper',
    'common\\services\\tp\\Inventory\\Helper'    => 'inventory\\modules\\v1\\helper',
    'common\\services\\tp\\TpMiscTools'          => 'inventory\\modules\\v1\\helper',
    'erp\\modules\\tp\\services\\warehouse\\dto' => 'inventory\\modules\\v1\\models',
    'common\\dto\\tp'                            => 'inventory\\modules\\v1\\models',
    'common\\services\\tp\\BizService'           => 'inventory\\modules\\v1\\services',
);
krsort($replaces); // 先替换长的

// step 3: 复制类文件
foreach ($namespaces as $item) {
    $oldFolder = dirname($item);
    if (!$newFolder = $replaces[$oldFolder] ?? 0) {
        continue;
    }
    $oldClass = namespace2class($item);
    $newClass = namespace2class($newFolder . '/' . basename($item));
    $newContent = str_replace($oldFolder, $newFolder, file_get_contents($oldClass));
    file_put_contents($newClass, $newContent);
    echo "<li>$newClass\n";
    // 添加进去替换
    $classes[$newClass] = 0;
}
// die;

// step 4: replace类引用
$replaceFolders = [];
foreach ($replaces as $oldFolder => $newFolder) {
    $replaceFolders["use $oldFolder"] = "use $newFolder";
}
array_map(fn($item) => dirname($item), $namespaces);
foreach ($classes as $file => $matches) {
    echo "File: $file\n";
    $newContent = str_replace(array_keys($replaceFolders), array_values($replaceFolders), file_get_contents($file));
    file_put_contents($file, $newContent);
}
