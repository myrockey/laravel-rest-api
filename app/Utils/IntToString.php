<?php
namespace App\Utils;

/**
 * 为了解决雪花id转json传前端损失精度的问题
 * 
 * @param array $data    原数据
 * @param array 需转换的key
 */
class IntToString
{
    public static function intToString($data, $keys = array())
    {
        if (count($data) > 0) {
            if (count($data) == count($data, 1)) {
                $data = [$data];
            }
            foreach ($data as $i => $d) {
                foreach ($d as $k => $v) {
                    if (in_array($k, $keys)) {
                        $data[$i][$k] = (string)$v;
                    }
                }
            }
        }
        return $data;
    }
}