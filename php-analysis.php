<?php
/**
 * 简单分析php程序的状况
 *
 * Author: Cqiu
 * Date: 2017-10-28
 */

/*
 * Usage：
 * include('php-analysis.php');
 */
if (!isset($_GET['_t'])) return;

register_shutdown_function(function () {
    $stats = [
        'runtime'   => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3).' s',
        'memory'    => sprintf('%.3f MB', memory_get_peak_usage() / 1048576),
        'files'     => strpos($_GET['_t'], 'x')!==false ? 'x' : get_included_files(),
        'constants' => strpos($_GET['_t'], 'T')===false ? 'T' : get_defined_constants(),
        'functions' => strpos($_GET['_t'], 'F')===false ? 'F' : get_defined_functions()['user'],
        'classes'   => strpos($_GET['_t'], 'C')===false ? 'C' : get_declared_classes(),
        'get'   => strpos($_GET['_t'], 'g')===false ? 'g' : $_GET,
        'post'   => strpos($_GET['_t'], 'p')===false ? 'p' : $_POST,
        'server'   => strpos($_GET['_t'], 's')===false ? 's' : $_SERVER,
    ];
    // echo '<pre>';
    // print_r($stats);
    // echo '</pre>';
    $trace = &$stats;
    ?>
 <style>
    #think_page_trace{padding: 0;position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:'微软雅黑';}
    #think_page_trace_tab{padding: 0;display: none;background:white;margin:0;height: 250px;}
    #think_page_trace_tab_tit{padding: 0;height:30px;padding: 6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px}
    span.trace-title{color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700}
    li.trace-info{border-bottom:1px solid #EEE;font-size:14px;padding:0 12px}
    #think_page_trace_tab_cont{padding: 0;overflow:auto;height:212px;padding:0;line-height: 24px}
    #think_page_trace_close{padding: 0;display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor:pointer;}
    #think_page_trace_open{padding: 0;height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer;}
</style>
<div id="think_page_trace">
    <div id="think_page_trace_tab">
        <div id="think_page_trace_tab_tit">
            <?php foreach ($trace as $key => $value) {?>
            <span class="trace-title"><?php echo $key ?></span>
            <?php }?>
        </div>
        <div id="think_page_trace_tab_cont">
            <?php foreach ($trace as $info) {?>
            <div style="display:none;">
                <ol style="padding: 0; margin:0">
                    <?php
                    if (is_array($info)) {
                        foreach ($info as $k => $val) {
                            echo '<li class="trace-info">' . (/*is_numeric($k) ? '' :*/ $k.' : ') . htmlentities(print_r($val,true), ENT_COMPAT, 'utf-8') . '</li>';
                        }
                    } else {
                        echo $info;
                    }
                    ?>
                </ol>
            </div>
            <?php }?>
        </div>
    </div>
    <div id="think_page_trace_close"><span>✕</span></div>
</div>
<div id="think_page_trace_open">
    <div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px"><?php echo $stats['runtime'];?></div>
    <span style="background: #2ba230; display: inline-block; color: #fff; font-size: 27px; border-top-left-radius: 8px; padding: 0 3px;">☯</span>
</div>

<script type="text/javascript">
    (function(){
        var tab_tit  = document.getElementById('think_page_trace_tab_tit').getElementsByTagName('span');
        var tab_cont = document.getElementById('think_page_trace_tab_cont').getElementsByTagName('div');
        var open     = document.getElementById('think_page_trace_open');
        var close    = document.getElementById('think_page_trace_close').children[0];
        var trace    = document.getElementById('think_page_trace_tab');
        var cookie   = document.cookie.match(/thinkphp_show_page_trace=(\d\|\d)/);
        var history  = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0,0];
        open.onclick = function(){
            trace.style.display = 'block';
            this.style.display = 'none';
            close.parentNode.style.display = 'block';
            history[0] = 1;
            document.cookie = 'thinkphp_show_page_trace='+history.join('|')
        }
        close.onclick = function(){
            trace.style.display = 'none';
            this.parentNode.style.display = 'none';
            open.style.display = 'block';
            history[0] = 0;
            document.cookie = 'thinkphp_show_page_trace='+history.join('|')
        }
        for(var i = 0; i < tab_tit.length; i++){
            tab_tit[i].onclick = (function(i){
                return function(){
                    for(var j = 0; j < tab_cont.length; j++){
                        tab_cont[j].style.display = 'none';
                        tab_tit[j].style.color = '#999';
                    }
                    tab_cont[i].style.display = 'block';
                    tab_tit[i].style.color = '#000';
                    history[1] = i;
                    document.cookie = 'thinkphp_show_page_trace='+history.join('|')
                }
            })(i)
        }
        parseInt(history[0]) && open.click();
        tab_tit[history[1]].click();
    })();
</script>

    <?php
});