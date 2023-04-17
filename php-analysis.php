<?php
/**
 * 简单分析php程序的状况
 * - use _COOKIE['_t']/ajax click=>document.cookie='_t=_';//start debug bar
 * - _err('msg');//trace
 * - ?_trace=1 //trace
 * - _log($var1, $var2, ...);//log vars
 * Usage：
 * define('LOG_TO', __DIR__ . '/debug.log');
 * define('LOG_TO', 'admin@domain.com');
 * define('LOG_TO', 0);//default php log
 * include('php-analysis.php');
 * 或：(防止不小心提交到代码库)
 * file_exists($debugFile = 'path/to/php-analysis.php') && include($debugFile);
 *
 * Author: Cqiu
 * Date: 2017-10-28
 */

//---------------------------------
// pjax 注释/删除 掉测试代码
if (
    !empty($_GET['_t'])
    && in_array($_GET['_t'], ['comment', 'delete'])
    && !empty($_GET['target'])
    && !empty($_GET['line'])
) {
    file_exists($file = $_GET['target']) or die('alert("文件不存在")');

    $line = $_GET['line'];
    $data = file($file);
    substr(ltrim($data[$line - 1]), 0, 4) === '_log' or die('alert("有误 or 已处理")');

    if ('comment' === $_GET['_t']) {
        $data[$line - 1] = '//' . $data[$line - 1];
    } else {
        unset($data[$line - 1]);
    }
    file_put_contents($file, implode("", $data));
    die('alert("处理成功")');
}
//---------------------------------
### trace err(from speedphp)
function _err($msg, array $traces)
{
    $msg = htmlspecialchars($msg);
    if (ob_get_contents()) ob_end_clean();
    function _err_highlight_code($code)
    {
        if (preg_match('/\<\?(php)?[^[:graph:]]/i', $code)) {
            return highlight_string($code, TRUE);
        }
        return str_replace('&lt;?php&nbsp;', '', highlight_string("<?php $code", TRUE));
    }

    function _err_getsource($file, $line)
    {
        if (!is_file($file)) {
            return '';
        }
        $fp = fopen($file, 'r');
        $p = max($line - 5, 1);
        $returns = [];
        $i = 1;
        while ($i++ < $p) fgets($fp, 4096);
        while ($p < $line + 5) {
            if (false === $codeLine = fgets($fp, 4096)) break;
            $returns[] = $p !== $line
                ? "<i>$p</i> " . _err_highlight_code($codeLine)
                : "<div class='current'><i>$p</i>" . _err_highlight_code($codeLine) . "</div>";
            $p++;
        }
        return $returns;
    }
?><!DOCTYPE html><html lang="zh-cn"><head><meta name="robots" content="noindex, nofollow, noarchive" /><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title><?php echo $msg;?></title><style>body{padding:0 100px;margin:0;word-wrap:break-word;word-break:break-all;font-family:Courier,Arial,sans-serif;background:#EBF8FF;color:#5E5E5E;}div,h2,p,span{margin:0; padding:0;}ul{margin:0; padding:0; list-style-type:none;font-size:0;line-height:0;}#contents{margin:13px auto 0 auto;background:#FFF;padding:8px 0 8px 9px;}#contents h2{display:block;background:#CFF0F3;font:bold 20px Arial;padding:12px 0 12px 30px;margin:0 10px 22px 1px;}#contents ol{padding:0 0 0 18px;font-size:0;line-height:0;}#contents ol li{padding:0;color:#8F8F8F;background-color:inherit;font:normal 14px Arial, Helvetica, sans-serif;margin:0;}#contents ol li summary{color:#408BAA;background-color:inherit;font:bold 14px Arial, Helvetica, sans-serif;padding:0 0 10px 0;margin:0;}.code-block{width:auto;font:normal 14px Arial, Helvetica, sans-serif;border:#EBF3F5 solid 4px;margin:0 30px 20px 0;padding:10px 20px;line-height:110%;}.code-block span{padding:0;margin:0;}.code-block .current{background:#CFF0F3;}code{font-family:Courier,Arial,sans-serif;}.code-block i{color:#bebebe;margin-right: 10px;font-size: 80%;}#contents a{color:green;}</style></head><body><div id="contents"><h2><?php echo $msg?></h2><ol><?php foreach($traces as $trace){if(is_array($trace)&&!empty($trace['file'])){$sourceLine = _err_getsource($trace['file'], $trace['line']);if($sourceLine){?><li><details <?= strpos($trace['file'], DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)?'':' open'?>><summary><?php echo "<a href=\"ide://open?url=file://{$trace['file']}&line={$trace['line']}\">{$trace['file']}:{$trace['line']}";?></a></summary><div class="code-block"><?php foreach($sourceLine as $singleLine)echo $singleLine;?></div></details></li><?php }}}?></ol></div></body></html><?php
    exit;
}

if (!empty($_COOKIE['_trace']) || !empty($_REQUEST['_trace'])) {
    set_error_handler(function ($level, $message, $file, $line) {
        _err($message, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    });
    set_exception_handler(function ($exception) {
        _err($exception->getMessage(), array_merge([[
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]], $exception->getTrace()));
    });
    unset($_GET['_trace']);
}
//---------------------------------
/**
 * 打印4测试
 * - 无参数：返回所有
 * - 一个普通值参数：直接打印
 * - 数组|对象|多参数：print_r
 * 通过界面 注释/删除 掉测试代码
 */
function _log()
{
    static $logs = [];
    if (!func_num_args()) {
        return $logs;
    }

    static $files = [];
    $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $caller = array_shift($bt);
    if (!isset($files[$caller['file']])) {
        $files[$caller['file']] = file($caller['file']);
    }

    // 这个请您别换行:)
    preg_match('#_log\((.*)\);#i', $files[$caller['file']][$caller['line'] - 1], $params);

    $key = $caller['file'] . ': ' . $caller['line'] . ": " . $params[1] . ': ' . microtime(1);
    $logs[$key] = func_num_args() > 1
        ? var_export(func_get_args(), 1)
        : var_export(func_get_arg(0), 1);
}

//---------------------------------
$debugOptions = empty($_COOKIE['_t']) ? (isset($_GET['_t']) ? ($_GET['_t'] ? $_GET['_t'] : '_') : null) : $_COOKIE['_t'];

register_shutdown_function(function () use ($debugOptions) {
    $logs = _log();
    if ($logs && defined('LOG_TO')) {
        $logString = '';
        foreach ($logs as $where => $log) {
            $logString .=  "\n$where\n" . print_r($log, true);
        }
        if (empty(LOG_TO)) error_log($logString);
        elseif (strpos(LOG_TO, '@')) error_log($logString, 1, LOG_TO);
        else error_log($logString, 3, LOG_TO);
    }

    if (!$debugOptions
        || (isset($_SERVER['HTTP_REQUEST_TYPE']) && $_SERVER['HTTP_REQUEST_TYPE'] === 'ajax')
        || array_search('XMLHttpRequest', getallheaders()) === 'X-Requested-With'
        || false !== stripos(implode('', headers_list()), 'Content-Type: application/json')
    ) {
        return;
    }

    $selectPanels = array_flip(str_split($debugOptions));
    $settings = [
        '_' => '<b title="选上则每个页面都显示本调试面板">Pinned</b>',
        'e' => 'TRACE',
        'x' => 'x files',
        'T' => 'constants',
        'F' => 'functions',
        'C' => 'classes',
        'g' => 'get',
        'p' => 'post',
        'c' => 'cookie',
        'S' => 'session',
        's' => 'server',
    ];
    $runtime = number_format(1000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])) . '<small>ms</small>';
    $memory = sprintf('%.3f', memory_get_peak_usage() / 1048576);
    $traces = [];
    isset($selectPanels['x']) or $traces['files'] = get_included_files();
    isset($selectPanels['T']) && $traces['constants'] = get_defined_constants();
    isset($selectPanels['F']) && $traces['functions'] = get_defined_functions()['user'];
    isset($selectPanels['C']) && $traces['classes'] = get_declared_classes();
    isset($selectPanels['g']) && $traces['get'] = $_GET;
    isset($selectPanels['p']) && $traces['post'] = $_POST;
    isset($selectPanels['c']) && $traces['cookie'] = isset($_COOKIE) ? $_COOKIE : [];
    isset($selectPanels['S']) && $traces['session'] = isset($_SESSION) ? $_SESSION : [];
    isset($selectPanels['s']) && $traces['server'] = $_SERVER;
    $logs && $traces['vars'] = $logs;
    empty($GLOBALS['_']) or $traces['vars2'] = $GLOBALS['_'];
?>
<style>
small{font-size: 60%}
#debugBar{padding:0;position:fixed;bottom:0;right:0;font-size:14px;width:100%;z-index:999999;color:#000;text-align:left;font-family:'微软雅黑',serif}
#debugBar_tab{padding:0;display:none;background:white;margin:0;height:250px}
#debugBar_tab_tit{background: #3c8dbc !important;background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #67a8ce)) !important;text-shadow: -1px -1px 1px #000, 1px 1px 1px #fff;height:30px;font:bold 16px/30px Georgia;padding:0 12px;background: #dadada;flex-grow: 1;cursor: n-resize;}
span.trace-title{text-transform:capitalize;color:#000;padding-right:12px;height:20px;line-height:20px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700}
#debugBar_tab_cont li{border-bottom:1px solid #EEE;font-size:14px;padding:0 12px}
#debugBar_tab_cont li pre{font-family: 'Courier New',serif;background: #e4e2e2;margin: 0;padding: 1px 12px;border-radius: 5px;line-height: 16px;}
#debugBar_tab_cont{padding:0;overflow:auto;height:220px;line-height:24px}
#debugBar_close{padding:0;display:none;text-align:right;height:15px;position:absolute;top:6px;right:12px;cursor:pointer}
#debugBar_open{padding:0;height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer;z-index: 99999;}
.setting label{display:block;width:100px;margin-left:20px}
.setting input{margin:0 3px}
#debugBarSetting {padding: 10px;}
#debugBarSetting u{margin: 5px;padding: 3px 5px;background: #78e778;border-radius: 5px;cursor: pointer;}
#debugBarSetting u.off{text-decoration: none;text-underline: none;background: #d2d6de}
</style>
<div id="debugBar">
    <div id="debugBar_tab">
        <div id="debugBar_tab_tit">
            <span class="trace-title"><?= $runtime, ' - ', $memory?><small>MB</small></span>
            <?php foreach ($traces as $key => $value) { ?>
                <span class="trace-title"><?php echo $key ?></span>
            <?php } ?>
        </div>
        <div id="debugBar_tab_cont">
            <div style="display:none;" id="debugBarSetting">
                <?php foreach ($settings as $key => $name) { ?>
                    <u data-key="<?= $key?>"<?= !empty($selectPanels[$key]) ? '' : ' class="off"'?>><?= $name ?></u>
                <?php } ?>
            </div>
            <?php foreach ($traces as $key => $info) { ?>
                <div style="display:none;">
                    <ol style="padding: 0; margin:0">
                        <?php
                        if (is_array($info)) {
                            foreach ($info as $k => $val) {
                                echo '<li>';
                                if ($key === 'vars') {
                                    list($file, $line) = explode(': ', $k);
                                    $fileEscape = urlencode($file);
                                    echo "<a href=\"ide://open?url=file://{$file}&line={$line}\">{$k}</a>"
                                        . "<button onclick=\"loadJS('?_t=comment&target=$fileEscape&line=$line')\">//</button>"
                                        . "<button onclick=\"loadJS('?_t=delete&target=$fileEscape&line=$line')\">×</button>"
                                        . '<pre>';
                                }
                                else echo $k . ' : ';
                                echo htmlentities(print_r($val, true), ENT_COMPAT, 'utf-8');
                                if ($key === 'vars')echo '</pre>';
                                echo '</li>';
                            }
                        } else {
                            echo $info;
                        }
                        ?>
                    </ol>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="debugBar_close"><span title="alt+q  显示/隐藏面板&#10;alt+,  增高面板&#10;alt+.  缩小面板&#10;alt+o  ide打开选中文本的文件">✕</span></div>
</div>
<div id="debugBar_open">
    <div style="background:#2ba230;color:#FFF;padding:0 6px 0 0;float:right;line-height:30px;font-size:14px"><?php echo $runtime; ?></div>
    <span style="background: #2ba230; display: inline-block; color: #fff; font-size: 27px; border-top-left-radius: 8px; padding: 0 3px;">☯</span>
</div>

<script type="text/javascript">
    function loadJS(url, success) {
        var domScript = document.createElement('script');
        domScript.src = url;
        success = success || function () {
        };
        domScript.onload = domScript.onreadystatechange = function () {
            if (!this.readyState || 'loaded' === this.readyState || 'complete' === this.readyState) {
                success();
                this.onload = this.onreadystatechange = null;
                this.parentNode.removeChild(this);
            }
        }
        document.getElementsByTagName('head')[0].appendChild(domScript);
    }
    (function () {
        var $id = function(id){return document.getElementById(id)}

        Array.prototype.forEach.call($id('debugBarSetting').children, function (obj, index) {
            obj.onclick = function () {
                obj.classList.toggle('off');
                var key = obj.getAttribute('data-key')
                    , cookie = document.cookie.match(/_t=(\w+)/)
                    , _t = (cookie && typeof cookie[1] !== 'undefined') ? cookie[1] : '_';
                if (key === 'e') {
                    document.cookie = '_trace=1' + (obj.classList.contains('off') ? ';expires=Thu,01-Jan-1970 00:00:01 GMT' : '');
                    //return;
                }
                document.cookie = '_t=' + (
                    obj.classList.contains('off')
                        ? (key === '_' ? '' : _t.replace(new RegExp(key, 'g'), ''))
                        : _t + key
                );
            }
        });

        var dom_tab_tit = $id('debugBar_tab_tit')
            , tab_tit = dom_tab_tit.children
            , dom_tab_cont = $id('debugBar_tab_cont')
            , tab_cont = dom_tab_cont.children
            , open = $id('debugBar_open')
            , close = $id('debugBar_close').children[0]
            , trace = $id('debugBar_tab')
            , cookie = document.cookie.match(/debugBarSetting=(\d\|\d)/) // 0,1-开闭|x-select tab index
            , history = (cookie && typeof cookie[1] !== 'undefined' && cookie[1].split('|')) || [0, 0];
        open.onclick = function () {
            trace.style.display = 'block';
            this.style.display = 'none';
            close.parentNode.style.display = 'block';
            history[0] = 1;
            document.cookie = 'debugBarSetting=' + history.join('|')
        };
        close.onclick = function () {
            trace.style.display = 'none';
            this.parentNode.style.display = 'none';
            open.style.display = 'block';
            history[0] = 0;
            document.cookie = 'debugBarSetting=' + history.join('|')
        };
        for (var i = 0; i < tab_tit.length; i++) {
            tab_tit[i].onclick = (function (i) {
                return function () {
                    for (var j = 0; j < tab_cont.length; j++) {
                        tab_cont[j].style.display = 'none';
                        tab_tit[j].style.color = '#fff';
                    }
                    tab_cont[i].style.display = 'block';
                    tab_tit[i].style.color = '#000';
                    history[1] = i;
                    document.cookie = 'debugBarSetting=' + history.join('|')
                }
            })(i)
        }
        parseInt(history[0]) && open.click();
        if (typeof tab_tit[history[1]] !== 'undefined') tab_tit[history[1]].click();
        else tab_tit[0].click();

        document.onkeydown = function (e) {
            if (e.key === 'o' && e.altKey) {
                let file = window.getSelection() + '';
                if (!file) return false;
                window.open(`ide://open?url=file://${file}&line=50`);
                return;
            }
            if (e.key === 'q' && e.altKey) {
                if (open.style.display === 'block') open.click();
                else close.click();
                return;
            }
            var h = trace.clientHeight
                , ht = dom_tab_tit.clientHeight - 6;
            if (e.key === ',' && e.altKey) {//alt+,
                trace.style.height = (h + 100) + 'px';
                dom_tab_cont.style.height = (h + 100 - ht) + 'px';
                return;
            }
            if (e.key === '.' && e.altKey) {//alt+.
                trace.style.height = (h - 100) + 'px';
                dom_tab_cont.style.height = (h - 100 - ht) + 'px';
                return;
            }
        };

        dom_tab_tit.onmousedown = function (e) {
            var clientHeight = document.documentElement.clientHeight < window.screen.height
                ? document.documentElement.clientHeight
                : document.body.clientHeight //DOM有错时这个正常些
                , mouseOffset = e.clientY - (clientHeight - trace.clientHeight)
                , ht = dom_tab_tit.clientHeight - 2;
            document.onmousemove = function (e) {
                e.preventDefault();
                var h = clientHeight - e.clientY + mouseOffset;
                trace.style.height = h + 'px';
                dom_tab_cont.style.height = (h - ht) + 'px';
            };
            document.onmouseup = function () {
                document.onmousemove = null;
            };
        };

    })();
</script>
    <?php
});
unset($debugOptions, $_GET['_t']); //clear invoke
