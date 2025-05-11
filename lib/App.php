<?php

/**
 * 一个简单的php框架 with 自动加载、路由
 */
defined('IS_WIN') or define('IS_WIN', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
defined('APP_DIR') or define('APP_DIR', dirname($_SERVER['SCRIPT_FILENAME']));
defined('LIB_DIR') or define('LIB_DIR', __DIR__);
defined('APP_DEBUG') or define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(-1);
    ini_set('display_errors', 'On');
} else {
    error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
}
//set_error_handler(['App', 'errorHandler']);

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
    static $configs = array(
        'defaultController' => 'Index',
        'defaultAction' => 'index',
    );

    static $caches = array();

    static $module = null;

    public static function run($configs)
    {
        // @todo use yii\helpers\ArrayHelper::merge
        static::$configs = array_merge(static::$configs, $configs);
        // route: r=module.controller/action
        $controller = static::$configs['defaultController'];
        $action = static::$configs['defaultAction'];
        if (isset($_REQUEST['r'])) {
            $parts = explode('/', trim($_REQUEST['r'], '/'), 2);
            isset($parts[1]) && $action = $parts[1];
            $parts0 = explode('.', $parts[0], 2);
            isset($parts0[1])
                ? ($controller = $parts0[1]) && (static::$module = $parts0[0])
                : $controller = $parts0[0];
        }
        _log(static::$module, $controller, $action);

        spl_autoload_register(['App', 'innerAutoload'], true, true);

        // linux区分大小写
        $controllerName = ucfirst($controller) . 'Controller';
        $actionName = 'action' . $action;

        if (!class_exists($controllerName)) throw new Exception("Err: Controller '$controllerName' is not exists!");
        if (!method_exists($controllerName, $actionName)) throw new Exception("Err: Method '$actionName' of '$controllerName' is not exists!");

        (new $controllerName())->$actionName();
    }

    public static function innerAutoload($class)
    {
        $class = str_replace("\\", '/', $class);
        $dirs = ($isController = false !== strpos($class, 'Controller'))
            ? self::$module ? array('controller/' . static::$module, 'controller') : array('controller')
            : array('model', 'include');
        $ignoreCase = !IS_WIN && $isController;
        foreach ($dirs as $dir) {
            if (file_exists($file = APP_DIR . "/protected/$dir/$class.php")) {
                include $file;
                return;
            }
            if (!$ignoreCase) continue;
            $phpFiles = glob(APP_DIR . "/protected/$dir/*.php");
            if (!is_array($phpFiles)) continue;
            $lowerFile = strtolower($file);
            foreach ($phpFiles as $file) {
                if (strtolower($file) === $lowerFile) {
                    include $file;
                    return;
                }
            }
        }
        if (file_exists($file = LIB_DIR . "/$class.php")) {
            include $file;
        }
    }

    public static function log($module, $controller, $action)
    {
        $log_file = APP_DIR . '/protected/runtime/log/' . date('Ymd') . '.log';
        $log_str = date('Y-m-d H:i:s') . ' ' . $module . '.' . $controller . '.' . $action . "\n";
        file_put_contents($log_file, $log_str, FILE_APPEND);
    }

    public static function errorHandler($errno, $errStr, $errFile, $errLine)
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }
        switch ($errno) {
            case E_USER_ERROR:
                echo "<b>My ERROR</b> [$errno] $errStr<br />\n";
                echo "  Fatal error on line $errLine in file $errFile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
                exit(1);
                break;

            case E_USER_WARNING:
                echo "<b>My WARNING</b> [$errno] $errStr<br />\n";
                break;

            case E_USER_NOTICE:
                echo "<b>My NOTICE</b> [$errno] $errStr<br />\n";
                break;

            default:
                echo "Unknown errortype: [$errno] $errStr<br />\n";
        }
    }
}

App::run(isset($configs) ? $configs : []);
