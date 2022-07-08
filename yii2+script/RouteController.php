<?php

namespace controllers;

use Yii;
use yii\web\Controller;

/**
 * 打印路由 | 控制器方法，for develop/debug
 * yii2-debug:2.1 已经有这功能了！
 * debug/default/view?panel=router
 * @author  Cqiu
 * @date    2022-5-26
 */
class RouteController extends Controller
{
    /**
     * 所有路由规则
     */
    public function actionIndex()
    {
        echo '<style>*{font: 80% Consolas;}
a{display: inline-block; padding: 2px 5px;background: #c6dfff;border-radius: 3px;}
label{clear:left; width: 130px;display: inline-block;color: gray;font-style: italic;}
tr:nth-child(odd){background-color: #f2f2f2;}
tr:nth-child(even),li:nth-child(even) {background-color: #fafafa;}
tr:nth-child(5n+0),li:nth-child(5n+0) {background-color: #e9e6e6;}
tr:hover,li:hover{background: #c3e9cb;}
li::before{color: green;}
li{margin-bottom: 1px}
span{display: inline-block;width: 100px}
b{border-radius: 3px;margin: 1px;}
b.GET{background: lightgreen}
b.PUT,b.POST{background: lightsalmon}
b.DELETE{background: orange}
u{text-decoration: none;display: inline-block;min-width: 300px}</style>
<pre><ol>';
        foreach (Yii::$app->urlManager->rules as $rule) {
            echo '<li>';
            if ($rule instanceof yii\rest\UrlRule) {
                echo 'RESTful: ';
                print_r($rule->controller);
            } else {
                echo '<span>';
                if (is_array($rule->verb)) {
                    foreach ($rule->verb as $verb) {
                        echo "<b class='$verb'>$verb</b>";
                    }
                } else {
                    echo $rule->verb;
                }
                echo '</span>';
                echo '<u>', htmlspecialchars($rule->name), '</u>';
                echo ' => ', htmlspecialchars($rule->route), "\n";
            }
            echo '</li>';
        }
        die('</ol></pre>');
    }

    /**
     * 所有模块/控制器 的方法
     */
    public function actionActions()
    {
        echo '<pre>';
        print_r(array_keys($this->getAppRoutes()));
        die;
    }

    public function getAppRoutes($module = null)
    {
        if ($module === null) {
            $module = Yii::$app;
        } elseif (is_string($module)) {
            $module = Yii::$app->getModule($module);
        }
        // $key = [__METHOD__, $module->getUniqueId()];
        $result = [];
        $this->getRouteRecursive($module, $result);

        return $result;
    }

    protected function getRouteRecursive($module, &$result)
    {
        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) !== null) {
                $this->getRouteRecursive($child, $result);
            }
        }

        foreach ($module->controllerMap as $id => $type) {
            $this->getControllerActions($type, $id, $module, $result);
        }

        $namespace = trim($module->controllerNamespace, '\\') . '\\';
        $this->getControllerFiles($module, $namespace, '', $result);
        $all = '/' . ltrim($module->uniqueId . '/*', '/');
        $result[$all] = $all;
    }

    protected function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        if (!is_dir($path)) {
            return;
        }
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $baseName = substr(basename($file), 0, -14);
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                $id = ltrim(str_replace(' ', '-', $name), '-');
                $className = $namespace . $baseName . 'Controller';
                if (strpos($className, '-') === false
                    && class_exists($className)
                    && is_subclass_of($className, 'yii\base\Controller')
                ) {//&& !(new \ReflectionClass($className))->isAbstract()
                    try {
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    } catch (\Exception $e) {
                        echo "$className:", $e->getMessage();
                    }
                }
            }
        }
    }

    protected function getControllerActions($type, $id, $module, &$result)
    {
        $controller = Yii::createObject($type, [$id, $module]);
        $this->getActionRoutes($controller, $result);
        $all = "/{$controller->uniqueId}/*";
        $result[$all] = $all;
    }

    protected function getActionRoutes($controller, &$result)
    {
        $prefix = '/' . $controller->uniqueId . '/';
        foreach ($controller->actions() as $id => $value) {
            $result[$prefix . $id] = $prefix . $id;
        }
        $class = new \ReflectionClass($controller);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                $id = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                $result[$id] = $id;
            }
        }
    }
}
