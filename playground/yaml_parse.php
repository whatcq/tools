<?php

/**
 * use symfony yaml parse
 */
include 'include.php';

spl_autoload_register(function ($className) {
    $map = ['Symfony\Component\Yaml' => 'D:\laragon\www\cqiu\yii2-bowl\vendor\symfony\yaml'];
    foreach ($map as $namespace => $folder) {
        $len = strlen($namespace);
        if (substr($className, 0, $len) === $namespace) {
            $filePath = $folder . str_replace('\\', '/', substr($className, $len)) . '.php';
            require_once $filePath;
        }
    }
});

if (!function_exists('yaml_parse')) {
    function yaml_parse($input)
    {
        return Symfony\Component\Yaml\Yaml::parse($input);
    }
}

var_dump(
    class_exists('Symfony\Component\Yaml\Yaml'),
    yaml_parse(file_get_contents('20231031tasks.yml'))
);
