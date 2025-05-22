<?php

class Html
{
    public static function table(array $data, $keys = [])
    {
        $keys  = $keys ?: array_keys(current($data));
        $html = '';
        // 显示不同的结果
        if (empty($data)) {
            $html .= '无数据';
            return;
        }
        // if (defined('TABLE_DEFINE')) {
        //     $html.= '<ul class="table-structure">';
        //     foreach ($data as $field) {
        //         $style = explode('(', $field['Type'])[0];
        //         $html.= "<li class='$style'>{$field['Field']}<span>{$field['Comment']}</span></li>";
        //     }
        //     $html.= '</ul>';
        //     return;
        // }
        if (count($data) === 1) {
            $data = current($data);
            if (count($data) === 1) {
                $html .= current($data);
            } else {
                $html .= '<ol>';
                foreach ($data as $key => $value) {
                    is_null($value) && $value = '<i>&lt;null></i>';
                    $html .= "<li><label>$key</label>$value</li>";
                }
                $html .= '</ol>';
            }
            return;
        }
        $html .= '<table border="0" cellpadding="3" class="table">';
        $html .= '<thead><tr bgcolor="#dddddd" class="fixed-header"><th class="number">#</th>';
        foreach ($keys as $key) {
            $html .= "<th>$key</th>";
        }
        $html .= '</tr></thead><tbody>';
        foreach ($data as $_key => $_data) {
            $html .= "<tr><td><i>$_key</i></td>";
            foreach ($keys as $key) {
                $value = $_data[$key] ?? null;
                is_null($value) && $value = '<i>&lt;null></i>';
                $html .= "<td>$value</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}