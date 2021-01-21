<?php
namespace app\admin\controller;

use think\Controller;

class Api extends Common 
{
    public function getUserInfo()
    {
        $data = model('Admin')->getUserInfo();
        $data = array_map(
            function ($item) {
                $item['lastloginip'] = long2ip($item['lastloginip']);
                $item['lastlogintime'] = date("Y-m-d H:i", $item['lastlogintime']);
                return $item;
            },
            $data
        );

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];

        return json($res);
    }

    public function getCustomInfo()
    {
        $data = model('User')->getUserInfo();
        $data = array_map(
            function ($item) {
                $item['lastloginip'] = long2ip($item['lastloginip']);
                $item['lastlogintime'] = date("Y-m-d H:i", $item['lastlogintime']);
                return $item;
            },
            $data
        );

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];

        return json($res);
    }

    public function getGroupInfo()
    {
        $data = model('Admin')->getGroupInfo();

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];

        return json($res);
    }

    public function getUserGroup()
    {
        $data = model('Admin')->getUserInfo();
        $data = array_map(
            function ($user) {
                $groups = model('Admin')
                    ->getUserGroup(
                        ['aga.uid' => $user['id']],
                        ['name']
                    );
                $groups = array_map(
                    function ($group) {
                        $group = $group['name'];
                        return $group;
                    },
                    $groups
                );
                $user['group'] = implode(',', $groups);
                // $user['group'] = $groups;
                return $user;
            },
            $data
        );

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];
        return json($res);
    }

    public function getLogInfo()
    {
        $data = db('admin_log')->select();

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);
        $data_part = array_map(
            function($log){
                $log['ip'] = long2ip($log['ip']);
                $log['time'] = date('Y-m-d H:i:s',$log['time']);
                return $log;
            }
            , $data_part
        );
        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];

        return json($res);
    }

    public function getOrderInfo()
    {
        $data = db('order')->select();

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);
        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];

        return json($res);
    }
 
    public function getMenuInfo()
    {
        $data = model('Menu')->getMenuInfo();

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];
        return json($res);
    }

    public function getShopInfo()
    {
        $data = model('Shop')->getShopInfo();

        $page = input('get.page');
        $limit = input('get.limit');
        $data_part = array_slice((array)$data, ($page-1)*$limit, $limit);

        $data_part = array_map(
            function($good){
                $good['start_time'] = date('Y-m-d H:i:s',$good['start_time']);
                $good['price'] = round($good['price'], 2);
                return $good;
            }
            , $data_part
        );

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];
        return json($res);
    }
}
