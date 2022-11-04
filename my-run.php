<title>Run PHP</title>
<style type="text/css">#e{position: absolute;top:0;right:0;bottom:0;left:0;font-size:16px;}</style>
<div id="e"><?php
    function getCode($file)
    {
        $source = file_get_contents($file);
        if (!empty($_REQUEST['format']) || isset($_REQUEST['ff'])) {
            include 'lib/php-formatter.php';
            $formatter = new Formatter();//配置：['braceNewline' => [T_FUNCTION, T_CLASS]]
            $source = $formatter->format($source);
        }
        return $source;
    }

    !empty($_REQUEST['f'])
    && file_exists($file = "playground/{$_REQUEST['f']}.php")
    && print(htmlspecialchars(getCode($file)))
    or print("&lt;?php\n\nvar_dump(\n    1\n);");
    ?>
</div>
<div style="position: fixed; right: 0; top: 0;max-width: 50%;width: 700px; height: 100%;">
    <form method="post" action="playground.php?act=save_run" target="iframe" style="display:inline;">
        <div style="position: fixed;right: 20px;">
        <input type="text" name="filename" id="filename" value="<?=$_REQUEST['f']??'test'?>">
        <input type="checkbox" id="format" title="format" checked>
        <button onclick="location='?f='+encodeURIComponent(document.getElementById('filename').value)+'&format='+~~document.getElementById('format').checked;return false;" title="Load this file=>">Load</button>
        <textarea name="source" id="source" cols="30" rows="10" style="display:none;"></textarea>
        </div>
    </form>
    <iframe src="" name="iframe" frameborder="0" style="width: 100%;height: 100%;"></iframe>
</div>
<script src="/cqiu/static/ace-builds/src-min/ace.js"></script>
<script src="/cqiu/static/ace-builds/src-min/ext-language_tools.js"></script>
<script>
    // trigger extension
    ace.require("ace/ext/language_tools");
    var e = ace.edit("e");
    e.setTheme("ace/theme/monokai");
    e.getSession().setMode("ace/mode/php");
    // enable autocompletion and snippets
    e.setOptions({
        enableBasicAutocompletion: true,//Ctrl+space
        enableSnippets: true,//tab
        wrap: true,
        enableLiveAutocompletion: false
    });
    document.onkeydown = function (e) {
        if (e.key==='s' && e.ctrlKey) {
            let pre = document.getElementById('format').checked
                ? '<pre style="color:#03b503;margin-top:30px;font: 14px/16px Consolas;white-space: pre-wrap;word-wrap: break-word;">'
                : '';
            document.getElementById('source').value=pre+window.e.getValue();
            document.forms[0].submit();
            return false;
        }
    };
</script>