<?php
// minipad2-api.php 竟然耗费了3h:utf16le! array函数，报错调试
$dataPath = 'D:\mysoft\minipad2\data';
$fileIdx = $dataPath . '/minipad2.idx';
$fileDat = $dataPath . '/minipad2.dat';
$fileBmk = $dataPath . '/minipad2.bmk';

$data = explode("\r\n", utf16_to_utf8(file_get_contents($fileIdx)));

$version = array_shift($data);
array_shift($data); //empty line

$_id = 32;//16;//9;//

$idx = [];
$offset = 0;
$fields = ['id', 'parent', 'type', 'length', 'title', 'm', 'n', 'i', 'timeC', 'timeU', 'timeA'];
foreach ($data as $line) {
    $record = explode("\t", $line);
//    $tmpArray = [];
//    foreach ($record as $i => $value) {
//        $tmpArray[isset($fields[$i]) ? $fields[$i] : $i] = $value;
//    }
//    $idx[$tmpArray['id']] = $tmpArray + ['offset' => $offset];
//    $offset += $tmpArray['length'];
    $idx[$record[0]] = [
        'parent'=>$record[1],
        'type'=>$record[2],
        'length'=>$record[3],
        'title'=>$record[4],
        'offset'=>$offset,
    ];
    $offset += $record[3];
//	break;
}
//var_dump($idx);exit;
/*
$bmk = [];
$p = explode("\r\n", utf16_to_utf8(file_get_contents($fileBmk)));
foreach ($p as $line) {

    list($id, $tmp) = explode('=', $line);
    $bmk[$id] = explode(',', $tmp);
}*/

//var_dump($idx[$_id]);exit;
//$dat = utf16_to_utf8(file_get_contents($fileDat));
//exit;
$dat = file_get_contents($fileDat, null, null, $idx[$_id]['offset'] * 2, $idx[$_id]['length'] * 2);
var_dump(
    $idx[$_id]
);
$idx[$_id]['offset'] and $dat = "\xFF\xFE" . $dat;
echo utf16_to_utf8($dat);
// ================
/**
 * utf8字符转换成Unicode字符
 *
 * @param  string $utf8_str Utf-8字符
 * @return string      Unicode字符
 */
function utf8_str_to_unicode($utf8_str)
{
    $unicode = 0;
    $unicode = (ord($utf8_str[0]) & 0x1F) << 12;
    $unicode |= (ord($utf8_str[1]) & 0x3F) << 6;
    $unicode |= (ord($utf8_str[2]) & 0x3F);
    return dechex($unicode);
}

/**
 * Unicode字符转换成utf8字符
 *
 * @param  string $unicode_str Unicode字符
 * @return string       Utf-8字符
 */
function unicode_to_utf8($unicode_str)
{
    $utf8_str = '';
    $code = intval(hexdec($unicode_str));
    // 这里注意转换出来的code一定得是整形，这样才会正确的按位操作
    $ord_1 = decbin(0xe0 | ($code >> 12));
    $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
    $ord_3 = decbin(0x80 | ($code & 0x3f));
    $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
    return $utf8_str;
}

// ============
/**
 * 将16位utf16转化为utf8
 *
 * @param string $str
 * @return string
 */
function utf16_to_utf8($str)
{
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);
    if ($c0 == 0xFE && $c1 == 0xFF) {
        $be = true;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $be = false;
    } else {
        return $str;
    }
    $str = substr($str, 2);
    $len = strlen($str);
    $dec = '';
    if ($be) {
        for ($i = 0; $i < $len; $i += 2) {
            $c = ord($str[$i]) << 8 | ord($str[$i + 1]);
            if ($c >= 0x0001 && $c <= 0x007F) {
                $dec .= chr($c);
            } else if ($c > 0x07FF) {
                $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
                $dec .= chr(0x80 | (($c >> 6) & 0x3F));
                $dec .= chr(0x80 | (($c >> 0) & 0x3F));
            } else {
                $dec .= chr(0xC0 | (($c >> 6) & 0x1F));
                $dec .= chr(0x80 | (($c >> 0) & 0x3F));
            }
        }
    } else {
        for ($i = 0; $i < $len; $i += 2) {
            $c = ord($str[$i + 1]) << 8 | ord($str[$i]);
            if ($c >= 0x0001 && $c <= 0x007F) {
                $dec .= chr($c);
            } else if ($c > 0x07FF) {
                $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
                $dec .= chr(0x80 | (($c >> 6) & 0x3F));
                $dec .= chr(0x80 | (($c >> 0) & 0x3F));
            } else {
                $dec .= chr(0xC0 | (($c >> 6) & 0x1F));
                $dec .= chr(0x80 | (($c >> 0) & 0x3F));
            }
        }

    }
    return $dec;
}
