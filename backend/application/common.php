<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use app\admin\model\Role;

function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'parentid', $child = '_child')
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = 0;
            if (isset($data[$pid])) {
                $parentId = $data[$pid];
            }
            if ((string) $root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

function stamp2date($timestamp)
{
    return date('Y-m-d H:i:s', $timestamp);
}

function role_id2name($role_list)
{
    $role_name_list = array();
    foreach ($role_list as $role_id) {
        try {
            $role_name_list[] = Role::get($role_id)->name;
        } catch (Exception $e) {
            continue;
        }
    }

    return $role_name_list;
}

function buildMsg($state, $msg)
{
    $result = [
        'state' => $state,
        'msg' => $msg,
    ];
    return $result;
}

if (!function_exists('array_column')) {
    function array_column($arr2, $column_key)
    {
        $data = [];
        foreach ($arr2 as $key => $value) {
            $data[] = $value[$column_key];
        }
        return $data;
    }
}
