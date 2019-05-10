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
        's' => 'server',
    ];
    $runtime = number_format(1000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])) . '<small>ms</small>';
    $memory = sprintf('%.3f MB', memory_get_peak_usage() / 1048576);
    $traces = [];
    strpos($_GET['_t'], '_') === false && $_settings['_'] = 0;
    strpos($_GET['_t'], 'x') === false && ($traces['files'] = get_included_files()) && $_settings['x'] = 0;
    strpos($_GET['_t'], 'T') === false ? $_settings['T'] = 0 : $traces['constants'] = get_defined_constants();
    strpos($_GET['_t'], 'F') === false ? $_settings['F'] = 0 : $traces['functions'] = get_defined_functions()['user'];
    strpos($_GET['_t'], 'C') === false ? $_settings['C'] = 0 : $traces['classes'] = get_declared_classes();
    strpos($_GET['_t'], 'g') === false ? $_settings['g'] = 0 : $traces['get'] = $_GET;
    strpos($_GET['_t'], 'p') === false ? $_settings['p'] = 0 : $traces['post'] = $_POST;
    strpos($_GET['_t'], 's') === false ? $_settings['s'] = 0 : $traces['server'] = $_SERVER;
    ($logs = _log()) && $traces['vars'] = $logs;
    // echo '<pre>';
    // print_r($traces);
    // echo '</pre>';

    //@todo 优化一下下面的前端实现代码
    ?>
<style>
#debugBar{padding:0;position:fixed;bottom:0;right:0;font-size:14px;width:100%;z-index:999999;color:#000;text-align:left;font-family:'微软雅黑',serif}
#debugBar_tab{padding:0;display:none;background:white;margin:0;height:250px}
#debugBar_tab_tit{height:30px;padding:6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px}
span.trace-title{color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700}
li.trace-info{border-bottom:1px solid #EEE;font-size:14px;padding:0 12px}
#debugBar_tab_cont{padding:0;overflow:auto;height:212px;line-height:24px}
#debugBar_close{padding:0;display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor:pointer}
#debugBar_open{padding:0;height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer}
.setting label{display:block;width:100px;margin-left:20px}
.setting input{margin:0 3px}
</style>
<div id="debugBar">
    <div id="debugBar_tab">
        <div id="debugBar_tab_tit">
            <span class="trace-title"><?= $runtime, ' | ', $memory?></span>
            <?php foreach ($traces as $key => $value) { ?>
                <span class="trace-title"><?php echo $key ?></span>
            <?php } ?>
        </div>
        <div id="debugBar_tab_cont">
            <div style="display:none;" class="setting">
                <?php foreach ($settings as $key => $name) { ?>
                    <label><input type="checkbox" onclick="trigger(this)"
                                  data-key="<?= $key ?>"<?php if ($_settings[$key]) {
                            echo ' checked';
                        } ?>><?= $name ?></label>
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
                                if($key==='vars')echo '<pre>';
                                echo htmlentities(print_r($val, true), ENT_COMPAT, 'utf-8');
                                if($key==='vars')echo '</pre>';
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
    <div id="debugBar_close"><span>✕</span></div>
</div>
<div id="debugBar_open">
    <div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px"><?php echo $runtime; ?></div>
    <span style="background: #2ba230; display: inline-block; color: #fff; font-size: 27px; border-top-left-radius: 8px; padding: 0 3px;">☯</span>
</div>

<script type="text/javascript">
    function trigger(obj) {
        var key = obj.getAttribute('data-key')
            , cookie = document.cookie.match(/_t=(\w+)/)
            , _t = (cookie && typeof cookie[1] !== 'undefined') ? cookie[1] : '_';
        document.cookie = '_t=' + (obj.checked ? _t + key : (key === '_' ? '' : _t.replace(new RegExp(key, 'g'), '')));
    }

    (function () {
        var $id = function(id){return document.getElementById(id)}
            , tab_tit = $id('debugBar_tab_tit').getElementsByTagName('span')
            , tab_cont = $id('debugBar_tab_cont').getElementsByTagName('div')
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
                open.click();
            }
        };
    })();
</script>
    <?php
});
