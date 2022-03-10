<title>Run PHP</title>
<style type="text/css">#e{position: absolute;top:0;right:0;bottom:0;left:0;font-size:16px;}</style>
<div id="e">&lt;pre style="color:green">&lt;?php
var_dump(
    1
);
</div>
<div style="position: fixed; right: 0; top: 0;max-width: 50%;width: 700px; height: 100%;">
    <form method="post" action="playground.php?act=save_run" target="iframe" style="display:inline;">
        <input type="text" name="filename" id="filename" value="test" style="float:right">
        <textarea name="source" id="source" cols="30" rows="10" style="display:none;"></textarea>
    </form>
    <iframe src="" name="iframe" frameborder="0" style="width: 100%;height: 100%;"></iframe>
</div>
<script src="https://cdn.bootcss.com/ace/1.4.9/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/ace/1.4.9/ext-language_tools.min.js"></script>
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