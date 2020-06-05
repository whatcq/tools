<?php
/**
 * Simple redis client by socket(no php-redis needed)
 * Author: wuzhc2016@163.com
 * 2017-09-12
 */
class RedisClient
{
    private $_socket = null;

    public function __construct($ip, $port)
    {
        $this->_socket = stream_socket_client(
            "tcp://{$ip}:{$port}",
            $errno,
            $errStr,
            1,
            STREAM_CLIENT_CONNECT
        );
        if (!$this->_socket) {
            throw new Exception($errStr);
        }
    }

    /**
     * 执行redis命令
     * @param $command
     * @return array|bool|string
     */
    public function exec($command)
    {
        // 拼装发送命令格式
        $command = $this->_execCommand($command);

        // 发送命令到redis
        fwrite($this->_socket, $command);

        // 解析redis响应内容
        return $this->_parseResponse();
    }

    /**
     * 将字符改为redis通讯协议格式
     * 例如mget name age 格式化为 *3\r\n$4\r\nmget\r\n$4\r\nname\r\n$3\r\nage\r\n
     * @param $command
     * @return bool|string
     */
    private function _execCommand($command)
    {
        $line = '';
        $crlf = "\r\n";
        $params = explode(' ', $command);
        if (empty($params)) {
            return $line;
        }

        // 参数个数
        $line .= '*' . count($params) . $crlf;

        // 各个参数拼装
        foreach ((array)$params as $param) {
            $line .= '$' . mb_strlen($param, '8bit') . $crlf;
            $line .= $param . $crlf;
        }

        return $line;
    }

    /**
     * 解析redis回复
     * @return array|bool|string
     * @throws Exception
     */
    private function _parseResponse()
    {
        $line = fgets($this->_socket);
        $type = $line[0];
        $msg = mb_substr($line, 1, -2, '8bit');

        switch ($type) {
            // 状态回复
            case '+':
                if ($msg == 'OK' || $msg == 'PONG') {
                    return true;
                } else {
                    return $msg;
                }
            // 错误回复
            case '-':
                throw new Exception($msg);
            // 整数回复
            case ':':
                return $msg;
            // 批量回复
            case '$': // $后面跟数据字节数(长度)
                $line = fread($this->_socket, (int)$msg + 2); // 数据字节数 + (\r\n)两个字节
                return mb_substr($line, 0, -2, '8bit'); // 去除最后两个字节
            // 多条批量回复
            case '*': // *表示后面有多少个参数
                $data = [];
                for ($i = 0; $i < $msg; $i++) {
                    $data[] = $this->_parseResponse();
                }
                return $data;
        }
    }
}

/* demo
$client = new RedisClient('127.0.0.1', 6379);
$client->exec('set name wuzhc');
echo '<pre>';
echo $res = $client->exec('dbsize');
// var_dump($res);
*/
