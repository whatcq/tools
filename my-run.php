<title>Run PHP</title>
<style type="text/css">#e{position: absolute;top:0;right:0;bottom:0;left:0;font-size:16px;}</style>
<div id="e"><?php
    function getCode($file)
    {
        $source = file_get_contents($file);
        if (isset($_REQUEST['format']) || isset($_REQUEST['ff'])) {
            include 'lib/php-formatter.php';
            $formatter = new Formatter();//配置：['braceNewline' => [T_FUNCTION, T_CLASS]]
            $source = $formatter->format($source);
        }
        return $source;
    }

    !empty($_REQUEST['f'])
    && file_exists($file = "playground/{$_REQUEST['f']}.php")
    && print(htmlspecialchars(getCode($file)))
    or print("&lt;pre style=\"color:green\">&lt;?php\nvar_dump(\n    1\n);");
    ?>
</div>
<div style="position: fixed; right: 0; top: 0;max-width: 50%;width: 700px; height: 100%;">
    <form method="post" action="playground.php?act=save_run" target="iframe" style="display:inline;">
        <input type="text" name="filename" id="filename" value="<?=$_REQUEST['f']??'test'?>" style="position: fixed;right: 20px;">
        <textarea name="source" id="source" cols="30" rows="10" style="display:none;"></textarea>
    </form>
    <iframe src="" name="iframe" frameborder="0" style="width: 100%;height: 100%;"></iframe>
</div>
<script src="http://localhost:9090/cqiu/static/ace-builds/src-min/ace.js"></script>
<script src="http://localhost:9090/cqiu/static/ace-builds/src-min/ext-language_tools.js"></script>
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
            document.getElementById('source').value=window.e.getValue();
            document.forms[0].submit();
            return false;
        }
    };
</script>