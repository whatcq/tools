<?php
//默认任务：tell me how to do!

$output = $input;
$output .= <<<'EOF'

// 任务：
if(preg_match('/^$/', $input)){
    $result = new ScriptResult(1);
    $result->content = 'done!!!';

    return $result;
}

EOF;

return '<--';
