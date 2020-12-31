<?php

// 任务1：检出git库来看看
if (preg_match('/^(https|git).*\.git$/', $input)) {
    $result = new ScriptResult(1);
    $result->content = 'done!!!';

    return $result;
}
