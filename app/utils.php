<?php

/**
 * 日期格式转换
 * @param $time
 * @return false|string
 */
function toTime($time){
    return date('Y-m-d', strtotime($time));
}

/**
 * 转换质保状态
 * @param $time2
 * @return null|string
 */
function toStatus($time2){
    switch ($time2 >= time()){
        case true:
            return "在保";
            break;
        case false:
            return "过保";
            break;
        default:
            return null;
            break;
    }
}

/**
 * 转换合同服务类型
 * @param $type
 * @return null|string
 */
function toType($type){
    switch ($type){
        case "0":
            return "故障处理";
            break;
        case "1":
            return "巡检";
            break;
        case "2":
            return "应急保障";
            break;
        case "3":
            return "远程协助";
            break;
        case "4":
            return "其他";
            break;
        default:
            return null;
            break;
    }
}

/**
 * @destination 判断角色是否含有权限
 * @param $perm 权限
 * @param $role 角色
 * @return true or false
 */
function permCheck($perm, $role){
    return $role->hasPermission($perm->name)? true:false;
}

/**
 * 判断值的类型
 * @param $var
 * @return string
 */
function myGetType($var)
{
    if (is_array($var)) return "array";
    if (is_bool($var)) return "boolean";
    if (is_float($var)) return "float";
    if (is_int($var)) return "integer";
    if (is_null($var)) return "NULL";
    if (is_numeric($var)) return "numeric";
    if (is_object($var)) return "object";
    if (is_resource($var)) return "resource";
    if (is_string($var)) return "string";
    return "unknown type";
}

/**
 * 优化in_array()，并实现多维数组求差集
 * 原理是将多维数组递归转化为字符串后查找。
 * in_array is too slow when array is large
 */
function arrayToString($arr){
    if (is_array($arr)){
        return implode(',', array_map('arrayToString', $arr));
    }
    return $arr;
}

function array_trim($arrs){
    foreach ($arrs as $k => $arr ){
        if(empty($arr)){
            unset($arrs[$k]);
        }
    }
    return $arrs;
}

function myGetMulDiff($old, $new){
    $new_str = arrayToString($new);
    foreach ($old as $k=>$o){
        $old_str = arrayToString($o);
        $diff[$k] = false === strpos($new_str, $old_str) ? $o : [];
    }
    $diff = array_trim($diff);
    return $diff;
}
