<?php
/**
 * 2017-6-25
 * 以前做这个要了两天，这回还不止两天！！！什么情况
 * 虽然这回优雅了点，但这个数组/字符串操作好多坑，还没填好。。。
 */


//有点问题。。
function array_diff_assoc_recursive1($array1, $array2)
{
    $difference = array();
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if (!empty($new_diff))
                    $difference[$key] = $new_diff;
            }
        } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}

/**
 * 第一个array多出来的东西
 * @return array
 */
function array_diff_assoc_recursive()
{
    $args = func_get_args();
    $diff = array();
    foreach (array_shift($args) as $key => $val) {
        for ($i = 0, $j = 0, $tmp = array($val), $count = count($args); $i < $count; $i++)
            if (is_array($val))
                if (!isset ($args[$i][$key]) || !is_array($args[$i][$key]) || empty($args[$i][$key]))
                    $j++;
                else
                    $tmp[] = $args[$i][$key];
            elseif (!array_key_exists($key, $args[$i]) || $args[$i][$key] !== $val)
                $j++;
        if (is_array($val)) {
            $tmp = call_user_func_array(__FUNCTION__, $tmp);
            if (!empty ($tmp)) $diff[$key] = $tmp;
            elseif ($j == $count) $diff[$key] = $val;
        } elseif ($j == $count && $count) $diff[$key] = $val;
    }

    return $diff;
}

/**
 * real array_merge_recursive
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_merge_recursive_distinct(array &$array1, array &$array2)
{
    $merged = $array1;

    foreach ($array2 as $key => &$value) {
        if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
            $merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
        } else {
            $merged [$key] = $value;
        }
    }

    return $merged;
}

/**
 * 每个array多出来的东西
 * @return array
 */
function array_diff_all()
{
}

/**
 * New 3.++ format ("String" is always terminated by zero byte)
 *
 * File header
 *
 * header:           string
 * ident         constant "MeMpAd"
 * encoding      character, " " means Ansi, "." means UTF-8
 * initial page  number to select at start (max. 5 characters)
 *
 * quick page path:  string (may be empty)
 *
 * For each Page:
 *
 * level:            byte, binary 1..99
 *
 * page title:       string (max 64 characters);
 * if a TAB character is found, the rest of the string
 * contains additional header information, currently the
 * background color code ($nnnnnn)
 *
 * page contents:    string
 *
 *
 * Note: If the "MeMpAd " signature at the beginning of the file is not found, Mempad assumes that this file is encrypted, and asks for the password.
 * 22 Jun 2012
 */
define('SP', chr(0));

class mempad
{
    private $file;
    private $tree;

    /**
     * @param $file
     * @throws Exception
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new Exception('File not found:' . $file);
        }
        $this->file = $file;
    }

    /**
     * @param bool $onlyTree
     * @return mempad
     */
    public function read($onlyTree = false)
    {
        $data = file_get_contents($this->file);
        $tmp = explode(SP, $data);
        unset($data);
//         print_r($tmp);
        $tree = [];
        $pp = [1 => &$tree];
        foreach ($tmp as $i => $v) {
            if ($v && ($_level = ord(substr($v, 0, 1))) < 16) {
                $level = $_level;
                $title = substr($v, 1 - strlen($v));
                $pp[$level][$title] = $onlyTree ? [] : [
                    'content' => $tmp[$i + 1]
                ];
                $pp[$level + 1] = &$pp[$level][$title];
            }
        }

        $this->tree = $tree;

        return $this;
    }

    public function getData()
    {
//        print_r($this->tree);
        return $this->tree;
    }

    /**
     * 按照mempad文件格式写入
     *
     * @param $file
     * @param null|array $tree
     * @param array $info
     * @return bool|int
     */
    public function write($file, $tree = null, $info = [])
    {
        if ($tree === null) {
            $tree = $this->tree;
        }
        isset($info['initialPage']) or $info['initialPage'] = '';
        isset($info['quickPagePath']) or $info['quickPagePath'] = '';
        $data = 'MeMpAd.' . $info['initialPage'] . SP . $info['quickPagePath'];

        // 指针数组
        $pp = [1 => &$tree];
        $level = 1;
        while ($pp) {

            while (list($title, $value) = each($pp[$level])) {

                $data .= SP . chr($level) . $title . SP;

                if (isset($value['content'])) {
                    if (is_array($value['content'])) {
                        $data .= implode("\n--------*--------*---------\n", $value['content']);
                    } else {
                        $data .= $value['content'];
                    }

                    unset($pp[$level][$title]['content']);
                }

                if ($pp[$level][$title]) {
                    uksort($pp[$level][$title], function ($m, $n) {
                        $monTh = array(
                            '01' => '一月',
                            '02' => '二月',
                            '03' => '三月',
                            '04' => '四月',
                            '05' => '五月',
                            '06' => '六月',
                            '07' => '七月',
                            '08' => '八月',
                            '09' => '九月',
                            '10' => '十月',
                            '11' => '十一月',
                            '12' => '十二月',
                        );

                        if (($_m = array_search($m, $monTh)) !== false
                            && ($_n = array_search($n, $monTh)) !== false
                        ) {
                            return ($_m < $_n) ? -1 : 1;
                        }

                        return ($m < $n) ? -1 : 1;

                    });
                    $pp[$level + 1] = $pp[$level][$title];
                    $level++;
                }

            }

            array_pop($pp);
            $level--;
        }
        $data .= SP;
        return file_put_contents($file, $data);
    }
}

class mempadManager extends mempad
{
    public static function merge($files, $mergeFile)
    {
        $trees = [];
        $tree = [];
        try {
            foreach ($files as $file) {
                $mempad = new mempad($file);
                $trees[$file] = $mempad->read(0)->getData();
                $tree = array_merge_recursive_distinct($tree, $trees[$file]);
            }
//            print_r($tree);exit;
//            file_put_contents('test.php', '<?php $trees='.var_export($trees, 1));

            return $mempad->write($mergeFile, $tree);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

mempadManager::merge([
    'G:\www\test\mempad_php\cqiu_diary - bak.lsf',
    'G:\www\test\mempad_php\cqiu_diary.lsf',
], 'G:\www\test\mempad_php\cqiu_diary_merge.lsf');

