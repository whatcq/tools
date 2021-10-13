<?php

// 任务1：检出git库来看看
if (preg_match('/^(https|git):.*\.git$/', $input)) {
    $result = new ScriptResult(1);
    todo("cd ../../../7788 && git clone --depth=1 $input && echo \$_");//只能用相对路径？待解决
    $result->content = ' <-- back task doing...';

    return $result;
}
