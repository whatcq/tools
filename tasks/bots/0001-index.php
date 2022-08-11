<?php

if (strpos($text, '开始成语接龙') !== false) $_SESSION['mode'] = '成语接龙';

return; // 一定要，否则默认会return true;
