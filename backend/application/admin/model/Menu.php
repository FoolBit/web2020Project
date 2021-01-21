<?php

/**
 *
 * @file   Menu.php
 * @date   2016-9-1 15:48:53
 * @author Zhenxun Du<5552123@qq.com>
 * @version    SVN:$Id:$
 */

namespace app\admin\model;

use think\Model;

class Menu extends Model
{
    /**
     * 我的菜单
     * @param type $display
     * @return array
     */
    public function getMyMenu($display = null)
    {
        $where = array();
        $user_id = session('user_id');
        $group_id = session('group_id');
        if ($user_id != 1) {
            $res = db('admin_group')
                ->field('rules')
                ->where('id', 'in', $group_id)
                ->select();
            if (!$res) {
                return false;
            }
            $tmp = '';
            foreach ($res as $k => $v) {
                $tmp .= $v['rules'] . ',';
            }

            $menu_ids = trim($tmp, ',');
            $where['id'] = ['in', $menu_ids];
        }


        if ($display) {
            $where['display'] = $display;
        }

        $res = db('menu')->where($where)->order('listorder asc')->select();

        return $res;
    }

    public function getMenuInfo()
    {
        $data_all = db('menu')->order('id')->select();

        $data = [];
        foreach ($data_all as $item) {
            $key = $item['id'];
            $data[$key] = $item;
        };

        $data = array_map(
            function ($item) use ($data) {
                $pid = $item['parentid'];
                $item['totalname'] = $item['name'];
                while ($pid != 0) {
                    $item['totalname'] = $data[$pid]['name'] . '-' . $item['totalname'];
                    $pid = $data[$pid]['parentid'];
                }
                if ($item['icon'])
                    $item['totalicon'] = '<i class="iconfont">&#' . $item['icon'] . ';</i>';
                return $item;
            },
            $data
        );

        // array_multisort(array_column($data,'totalname'),SORT_ASC,$data);
        $keysValue = [];
        foreach ($data as $k => $v) {
            $keysValue[$k] = $v['totalname'];
        };
        array_multisort($keysValue, SORT_ASC, $data);
        
        $data_ = [];
        foreach ($data as $item) {
            $key = $item['id'];
            $data_[$key] = $item;
        };

        return $data_;
    }
}
