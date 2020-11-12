<?php
/**
 * 简单分析php程序的状况
 * - use _COOKIE['_t']/ajax click=>document.cookie='_t=_';
 * - print vars,trace,sqls
 *
 * Usage：
 * include('php-analysis.php');
 *
 * Author: Cqiu
 * Date: 2017-10-28
 */
!empty($_COOKIE['_t']) && $_GET['_t'] = $_COOKIE['_t'];
if (!isset($_GET['_t'])
    || (isset($_SERVER['HTTP_REQUEST_TYPE']) && $_SERVER['HTTP_REQUEST_TYPE'] === 'ajax')
    || array_search('XMLHttpRequest', getallheaders()) === 'X-Requested-With'
) {
    return;
}

/**
 * 打印4测试
 * - 无参数：返回所有
 * - 一个普通值参数：直接打印
 * - 数组|对象|多参数：print_r
 * @todo 界面上ajax 注释/删除 掉测试代码
 */
function _log()
{
    static $logs = [];
    if (!func_num_args()) {
        return $logs;
    }

    static $files = [];
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    if (!isset($files[$caller['file']])) {
        $files[$caller['file']] = file($caller['file']);
    }

    preg_match('#\((.*)\)#i', $files[$caller['file']][$caller['line'] - 1], $params);

    $logs[$caller['file'] . ': ' . $caller['line'] . ": " . $params[1]] = func_num_args() > 1
        ? var_export(func_get_args(), 1)
        : var_export(func_get_arg(0), 1);
}

register_shutdown_function(function () {
    $_settings = $settings = [
        '_' => '<b>Pinned</b>',
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
    strpos($_GET['_t'], '_') === false && $_settings['_'] = 0;
    strpos($_GET['_t'], 'x') === false && ($traces['files'] = get_included_files()) && $_settings['x'] = 0;
    strpos($_GET['_t'], 'T') === false ? $_settings['T'] = 0 : $traces['constants'] = get_defined_constants();
    strpos($_GET['_t'], 'F') === false ? $_settings['F'] = 0 : $traces['functions'] = get_defined_functions()['user'];
    strpos($_GET['_t'], 'C') === false ? $_settings['C'] = 0 : $traces['classes'] = get_declared_classes();
    strpos($_GET['_t'], 'g') === false ? $_settings['g'] = 0 : $traces['get'] = $_GET;
    strpos($_GET['_t'], 'p') === false ? $_settings['p'] = 0 : $traces['post'] = $_POST;
    strpos($_GET['_t'], 'c') === false ? $_settings['c'] = 0 : $traces['cookie'] = $_COOKIE;
    strpos($_GET['_t'], 'S') === false ? $_settings['S'] = 0 : $traces['session'] = $_SESSION;
    strpos($_GET['_t'], 's') === false ? $_settings['s'] = 0 : $traces['server'] = $_SERVER;
    ($logs = _log()) && $traces['vars'] = $logs;
    !empty($GLOBALS['_']) && $traces['vars2'] = $GLOBALS['_'];
    // echo '<pre>';
    // print_r($traces);
    // echo '</pre>';

    //@todo 优化一下下面的前端实现代码
    ?>
<style>
small{font-size: 60%}
#debugBar{padding:0;position:fixed;bottom:0;right:0;font-size:14px;width:100%;z-index:999999;color:#000;text-align:left;font-family:'微软雅黑',serif}
#debugBar_tab{padding:0;display:none;background:white;margin:0;height:250px}
#debugBar_tab_tit{height:30px;padding:6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px;flex-grow: 1;cursor: n-resize;}
span.trace-title{text-transform:capitalize;color:#000;padding-right:12px;height:20px;line-height:20px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700}
li.trace-info{border-bottom:1px solid #EEE;font-size:14px;padding:0 12px}
li.trace-info pre{font-family: 'Courier New'}
#debugBar_tab_cont{padding:0;overflow:auto;height:212px;line-height:24px}
#debugBar_close{padding:0;display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor:pointer}
#debugBar_open{padding:0;height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer;z-index: 99999;}
.setting label{display:block;width:100px;margin-left:20px}
.setting input{margin:0 3px}
#debugBarSetting u{margin: 5px;padding: 3px 5px;background: #78e778;border-radius: 5px;}
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
                    <u data-key="<?= $key?>" class="<?= $_settings[$key] ? '' : 'off'?>"><?= $name ?></u>
                <?php } ?>
            </div>
            <?php foreach ($traces as $key => $info) { ?>
                <div style="display:none;">
                    <ol style="padding: 0; margin:0">
                        <?php
                        if (is_array($info)) {
                            foreach ($info as $k => $val) {
                                echo '<li class="trace-info">',
                                (/*is_numeric($k) ? '' :*/$k . ' : ');
                                if ($key === 'vars')echo '<pre>';
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
    <div id="debugBar_close"><span title="ctrl+q  显示/隐藏面板&#10;alt+,  增高面板&#10;alt+.  缩小面板">✕</span></div>
</div>
<div id="debugBar_open">
    <div style="background:#2ba230;color:#FFF;padding:0 6px 0 0;float:right;line-height:30px;font-size:14px"><?php echo $runtime; ?></div>
    <span style="background: #2ba230; display: inline-block; color: #fff; font-size: 27px; border-top-left-radius: 8px; padding: 0 3px;">☯</span>
</div>

<script type="text/javascript">
    (function () {

        Array.prototype.forEach.call(document.getElementById('debugBarSetting').children, function (obj, index) {
            obj.onclick = function () {
                obj.classList.toggle('off')
                var key = obj.getAttribute('data-key')
                    , cookie = document.cookie.match(/_t=(\w+)/)
                    , _t = (cookie && typeof cookie[1] !== 'undefined') ? cookie[1] : '_';
                document.cookie = '_t=' + (!obj.classList.contains('off')
                        ? _t + key
                        : (key === '_' ? '' : _t.replace(new RegExp(key, 'g'), ''))
                );

            }
        });

        var $id = function(id){return document.getElementById(id)}
            , dom_tab_tit = $id('debugBar_tab_tit')
            , tab_tit = dom_tab_tit.children
            , dom_tab_cont = $id('debugBar_tab_cont')
            , tab_cont = dom_tab_cont.children
            , open = $id('debugBar_open')
            , close = $id('debugBar_close').children[0]
            , trace = $id('debugBar_tab')
            , cookie = document.cookie.match(/debugBarSetting=(\d\|\d)/)
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
                        tab_tit[j].style.color = '#999';
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

        document.onkeydown = function (event) {
            var a = window.event.keyCode;
            if ((a === 81) && (event.ctrlKey)) {//Ctrl+q
                if(open.style.display === 'block')open.click();
                else close.click();
                return;
            }
            var h = trace.clientHeight
                ,ht = dom_tab_tit.clientHeight - 6;
            if ((a === 188) && (event.altKey)) {//alt+,
                trace.style.height = (h + 100) + 'px';
                dom_tab_cont.style.height = (h + 100 - ht) + 'px';
                return;
            }
            if ((a === 190) && (event.altKey)) {//alt+.
                trace.style.height = (h - 100) + 'px';
                dom_tab_cont.style.height = (h - 100 - ht) + 'px';
                return;
            }
        };

        dom_tab_tit.onmousedown = function (e) {
            document.onmousemove = function (e) {
                e.preventDefault();
                var h = document.body.offsetHeight - e.clientY + 30
                  ,ht = dom_tab_tit.clientHeight - 6;
                trace.style.height = h + 'px';
                dom_tab_cont.style.height = (h - ht) + 'px';
            };
            document.onmouseup = function (e) {
                document.onmousemove = null;
            };
        };

    })();
</script>
    <?php
});

// trace err
function err($msg){
	$msg = htmlspecialchars($msg);
	$traces = debug_backtrace();
	if (ob_get_contents()) ob_end_clean();
    function _err_highlight_code($code)
    {
        if (preg_match('/\<\?(php)?[^[:graph:]]/i', $code)) {
            return highlight_string($code, TRUE);
        } else {
            return preg_replace('/(&lt;\?php&nbsp;)+/i', "", highlight_string("<?php " . $code, TRUE));
        }
    }

    function _err_getsource($file, $line)
    {
        if (!(file_exists($file) && is_file($file))) {
            return '';
        }
        $data = file($file);
        $count = count($data) - 1;
        $start = $line - 5;
        if ($start < 1) {
            $start = 1;
        }
        $end = $line + 5;
        if ($end > $count) {
            $end = $count + 1;
        }
        $returns = array();
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $line) {
                $returns[] = "<div id='current'>" . $i . ".&nbsp;" . _err_highlight_code($data[$i - 1], TRUE) . "</div>";
            } else {
                $returns[] = $i . ".&nbsp;" . _err_highlight_code($data[$i - 1], TRUE);
            }
        }
        return $returns;
}?><!DOCTYPE html><html lang="zh-cn"><head><meta name="robots" content="noindex, nofollow, noarchive" /><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title><?php echo $msg;?></title><style>body{padding:0;margin:0;word-wrap:break-word;word-break:break-all;font-family:Courier,Arial,sans-serif;background:#EBF8FF;color:#5E5E5E;}div,h2,p,span{margin:0; padding:0;}ul{margin:0; padding:0; list-style-type:none;font-size:0;line-height:0;}#body{margin:0 auto;}#main{width:95%;margin:13px auto 0 auto;padding:0 0 35px 0;}#contents{margin:13px auto 0 auto;background:#FFF;padding:8px 0 0 9px;}#contents h2{display:block;background:#CFF0F3;font:bold 20px;padding:12px 0 12px 30px;margin:0 10px 22px 1px;}#contents ul{padding:0 0 0 18px;font-size:0;line-height:0;}#contents ul li{display:block;padding:0;color:#8F8F8F;background-color:inherit;font:normal 14px Arial, Helvetica, sans-serif;margin:0;}#contents ul li span{display:block;color:#408BAA;background-color:inherit;font:bold 14px Arial, Helvetica, sans-serif;padding:0 0 10px 0;margin:0;}#oneborder{width:auto;font:normal 14px Arial, Helvetica, sans-serif;border:#EBF3F5 solid 4px;margin:0 30px 20px 30px;padding:10px 20px;line-height:110%;}#oneborder span{padding:0;margin:0;}#oneborder #current{background:#CFF0F3;}code{font-family:Courier,Arial,sans-serif;}</style></head><body><div id="main"><div id="contents"><h2><?php echo $msg?></h2><?php foreach($traces as $trace){if(is_array($trace)&&!empty($trace["file"])){$souceline = _err_getsource($trace["file"], $trace["line"]);if($souceline){?><ul><li><span><?php echo $trace["file"];?> on line <?php echo $trace["line"];?> </span></li></ul><div id="oneborder"><?php foreach($souceline as $singleline)echo $singleline;?></div><?php }}}?></div></div><div style="clear:both;padding-bottom:50px;" /></body></html><?php
    exit;
}
