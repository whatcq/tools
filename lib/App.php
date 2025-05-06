<?php

/**
 * 一个简单的php框架 with 自动加载、路由
 */
defined('APP_DIR') or define('APP_DIR', dirname($_SERVER['SCRIPT_FILENAME']));
defined('APP_DEBUG') or define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(-1);
    ini_set("display_errors", "On");
    set_error_handler("_err_handle");
} else {
    error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
    ini_set("display_errors", "Off");
    ini_set("log_errors", "On");
    set_error_handler("_err_handle2");
}

/**
 * 配置文件: .env/.ini; config.php; db-config;
 */
class Config
{
    static $_configs = array(
        'debug' => false,
        'rewrite' => array(
            '/<m>/<c>/<a>' => '/<m>/<c>/<a>',
            '/<c>/<a>' => '/<c>/<a>',
        ),
        'db' => array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => 'root',
            'dbname' => 'test',
        ),
    );

    public static function set($key, $value)
    {
        static::$_configs[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        if (isset(static::$_configs[$key])) {
            return static::$_configs[$key];
        }
        if ($default !== null) {
            return $default;
        }
        throw new \Exception('Config not found:' . $key);
    }
}

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

class App
{
    static $module = null;

    public static function run()
    {
        list($action, $controller, static::$module) = isset($_REQUEST['r'])
            ? array_reverse(explode('/', trim($_REQUEST['r'], '/'), 3))
            : ['index', 'index', null];

        spl_autoload_register(['App', 'inner_autoload']);

        // linux区分大小写
        $controllerName = $controller . 'Controller';
        $actionName = 'action' . $action;

        if (!class_exists($controllerName)) throw new Exception("Err: Controller '$controllerName' is not exists!");
        if (!method_exists($controllerName, $actionName)) throw new Exception("Err: Method '$actionName' of '$controllerName' is not exists!");

        $controller_obj = new $controllerName();
        $controller_obj->$actionName();
    }

    public static function inner_autoload($class)
    {
        global $__module;
        $class = str_replace("\\", "/", $class);
        foreach (array('model', 'include', 'controller' . (empty($__module) ? '' : DS . $__module)) as $dir) {
            $file = APP_DIR . DS . 'protected' . DS . $dir . DS . $class . '.php';
            if (file_exists($file)) {
                include $file;
                return;
            }
        }
    }
}
