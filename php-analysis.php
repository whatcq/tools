<?php
/**
 * 简单分析php程序的状况
 * @todo use _COOKIE['debug']/ajax click=>document.cookie='_t=_';
 * @todo print vars,trace,sqls
 * Author: Cqiu
 * Date: 2017-10-28
 */

/*
 * Usage：
 * include('php-analysis.php');
 */
isset($_COOKIE['_t']) && $_GET['_t'] = $_COOKIE['_t'];
if (!isset($_GET['_t'])) {
    return;
}

register_shutdown_function(function () {
    $_settings = $settings = [
        'x' => 'x files',
        'T' => 'constants',
        'F' => 'functions',
        'C' => 'classes',
        'g' => 'get',
        'p' => 'post',
        's' => 'server',
    ];
    $stats = [
        'runtime' => number_format(1000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])) . 'ms',
        'memory'  => sprintf('%.3f MB', memory_get_peak_usage() / 1048576),
        // 'files'     => strpos($_GET['_t'], 'x')!==false ? 'x' : get_included_files(),
        // 'constants' => strpos($_GET['_t'], 'T')===false ? 'T' : get_defined_constants(),
        // 'functions' => strpos($_GET['_t'], 'F')===false ? 'F' : get_defined_functions()['user'],
        // 'classes'   => strpos($_GET['_t'], 'C')===false ? 'C' : get_declared_classes(),
        // 'get'   => strpos($_GET['_t'], 'g')===false ? 'g' : $_GET,
        // 'post'   => strpos($_GET['_t'], 'p')===false ? 'p' : $_POST,
        // 'server'   => strpos($_GET['_t'], 's')===false ? 's' : $_SERVER,
    ];
    strpos($_GET['_t'], 'x') === false && ($stats['files'] = get_included_files()) && $_settings['x'] = 0;
    strpos($_GET['_t'], 'T') === false ? $_settings['T'] = 0 : $stats['constants'] = get_defined_constants();
    strpos($_GET['_t'], 'F') === false ? $_settings['F'] = 0 : $stats['functions'] = get_defined_functions()['user'];
    strpos($_GET['_t'], 'C') === false ? $_settings['C'] = 0 : $stats['classes'] = get_declared_classes();
    strpos($_GET['_t'], 'g') === false ? $_settings['g'] = 0 : $stats['get'] = $_GET;
    strpos($_GET['_t'], 'p') === false ? $_settings['p'] = 0 : $stats['post'] = $_POST;
    strpos($_GET['_t'], 's') === false ? $_settings['s'] = 0 : $stats['server'] = $_SERVER;
    // echo '<pre>';
    // print_r($stats);
    // echo '</pre>';
    $trace = &$stats;
    ?>
<style>
#debubBar{padding:0;position:fixed;bottom:0;right:0;font-size:14px;width:100%;z-index:999999;color:#000;text-align:left;font-family:'微软雅黑'}
#debubBar_tab{padding:0;display:none;background:white;margin:0;height:250px}
#debubBar_tab_tit{padding:0;height:30px;padding:6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px}
span.trace-title{color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700}
li.trace-info{border-bottom:1px solid #EEE;font-size:14px;padding:0 12px}
#debubBar_tab_cont{padding:0;overflow:auto;height:212px;padding:0;line-height:24px}
#debubBar_close{padding:0;display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor:pointer}
#debubBar_open{padding:0;height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer}
.setting label{display:block;width:100px;margin-left:20px}
.setting input{margin:0 3px}
</style>
<div id="debubBar">
    <div id="debubBar_tab">
        <div id="debubBar_tab_tit">
            <span class="trace-title">Setting</span>
            <?php foreach ($trace as $key => $value) { ?>
                <span class="trace-title"><?php echo $key ?></span>
            <?php } ?>
        </div>
        <div id="debubBar_tab_cont">
            <div style="display:none;" class="setting">
                <?php foreach ($settings as $key => $name) { ?>
                    <label><input type="checkbox" onclick="trigger(this)"
                                  data-key="<?= $key ?>"<?php if ($_settings[$key]) {
                            echo ' checked';
                        } ?>><?= $name ?></label>
                <?php } ?>
            </div>
            <?php foreach ($trace as $info) { ?>
                <div style="display:none;">
                    <ol style="padding: 0; margin:0">
                        <?php
                        if (is_array($info)) {
                            foreach ($info as $k => $val) {
                                echo '<li class="trace-info">' . (/*is_numeric($k) ? '' :*/
                                        $k . ' : ') . htmlentities(print_r($val, true), ENT_COMPAT,
                                        'utf-8') . '</li>';
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
    <div id="debubBar_close"><span>✕</span></div>
</div>
<div id="debubBar_open">
    <div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px"><?php echo $stats['runtime']; ?></div>
    <span style="background: #2ba230; display: inline-block; color: #fff; font-size: 27px; border-top-left-radius: 8px; padding: 0 3px;">☯</span>
</div>

<script type="text/javascript">
    function trigger(obj) {
        console.log()
        var key = obj.getAttribute('data-key')
            , cookie = document.cookie.match(/_t=(\w+)/)
            , _t = (cookie && typeof cookie[1] != 'undefined') ? cookie[1] : '_';
        if (obj.checked) {
            document.cookie = '_t=' + _t + key;
        } else {
            document.cookie = '_t=' + _t.replace(new RegExp(key, 'g'), '');
        }
    }

    (function () {
        var tab_tit = document.getElementById('debubBar_tab_tit').getElementsByTagName('span');
        var tab_cont = document.getElementById('debubBar_tab_cont').getElementsByTagName('div');
        var open = document.getElementById('debubBar_open');
        var close = document.getElementById('debubBar_close').children[0];
        var trace = document.getElementById('debubBar_tab');
        var cookie = document.cookie.match(/debubBarSetting=(\d\|\d)/);
        var history = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0, 0];
        open.onclick = function () {
            trace.style.display = 'block';
            this.style.display = 'none';
            close.parentNode.style.display = 'block';
            history[0] = 1;
            document.cookie = 'debubBarSetting=' + history.join('|')
        }
        close.onclick = function () {
            trace.style.display = 'none';
            this.parentNode.style.display = 'none';
            open.style.display = 'block';
            history[0] = 0;
            document.cookie = 'debubBarSetting=' + history.join('|')
        }
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
                    document.cookie = 'debubBarSetting=' + history.join('|')
                }
            })(i)
        }
        parseInt(history[0]) && open.click();
        tab_tit[history[1]].click();
    })();
</script>
    <?php
});