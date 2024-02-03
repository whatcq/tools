<?php

/* @var $input string */

// 任务：下载网页
if (preg_match('#^http://z.ludajiaoyu.cn/.*#', $input)) {
    $result = new ScriptResult(1);
    todo(fixCmd(<<<CMD
cd ../../../cqiu/111/output-directory && \
wget -np -p -nc -k --content-disposition \
--header="Cookie: online-uuid=F5D0183E-644D-A8D2-3693-26C8F7BBBF8A; PHPSESSID=0gahec5q6o07v9el52m8ch0t00; REMEMBERME=Qml6XFVzZXJcQ3VycmVudFVzZXI6ZFhObGNsODFZM0Z6ZG04d2VtTkFaV1IxYzI5b2J5NXVaWFE9OjE3Mzg0NjQ5ODE6MGNhMDhmMzg3YzlmYTFiNzljYzc0ZTM5OTcxZDQ5YjM0YTdlY2I1ZjNlODllMWI1OTJjNGNhYmQyNDQ3N2ZkMA%3D%3D" \
--user-agent="Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36 Edg/116.0.1938.76" \
$input
CMD
    ));//只能用相对路径？待解决
    $result->content = ' <-- back task doing...';

    return $result;
}
