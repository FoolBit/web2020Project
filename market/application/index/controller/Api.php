<?php

namespace app\index\controller;

use think\Controller;

class Api extends Controller
{
    public function getUsername()
    {
        $token = request()->header('Auth');
        if ($token == "null") {
            return errorResponse('请登录！');
        }
        $info = model('MyJWT')->varify($token);
        // return var_dump( $token);
        return successResponse('', ['username' => $info['username']]);
    }

    public function getNews()
    {
        $goods = db('good')->where(['state' => 1])->order('updatetime desc')->limit(3)->select();
        $goods = array_map(
            function ($good) {
                $good['url'] = url('index/index/detail', ['id' => $good['id']]);
                if ($good['image']) {
                    $good['image'] = config('img_path') . $good['image'];
                } else {
                    $good['image'] = config('deafult_img');
                }
                return $good;
            },
            (array)$goods
        );
        return successResponse('', ['data' => $goods]);
    }

    public function getRecommand()
    {
        $goods = db('good')->where(['state' => 1])->where(['recommand' => 1])->limit(3)->select();
        $goods = array_map(
            function ($good) {
                $good['url'] = url('index/index/detail', ['id' => $good['id']]);
                if ($good['image']) {
                    $good['image'] = config('img_path') . $good['image'];
                } else {
                    $good['image'] = config('deafult_img');
                }
                return $good;
            },
            (array)$goods
        );
        return successResponse('', ['data' => $goods]);
    }

    public function getGoods()
    {
        $goods = db('good')->where(['state' => 1])->select();
        $goods = array_map(
            function ($good) {
                $good['url'] = url('index/index/detail', ['id' => $good['id']]);
                if ($good['image']) {
                    $good['image'] = config('img_path') . $good['image'];
                } else {
                    $good['image'] = config('deafult_img');
                }
                return $good;
            },
            (array)$goods
        );
        return successResponse('', ['data' => $goods]);
    }

    public function getGood()
    {
        $good = db('good')->where(['state' => 1])->where(['id' => input('id')])->find();
        if ($good['image']) {
            $good['image'] = config('img_path') . $good['image'];
        } else {
            $good['image'] = config('deafult_img');
        }
        return successResponse('', ['data' => $good]);
    }

    public function getRestTime()
    {
        $good = db('good')->where(['state' => 1])->where(['id' => input('id')])->find();
        if (!$good)
            return errorResponse('商品id错误');
        $time_start = $good['start_time'];
        return successResponse('', ['data' => $time_start - time()]);
    }

    public function getLink()
    {
        $id = input('id');
        $good = db('good')->where(['state' => 1])->where(['id' => input('id')])->find();
        $time_start = $good['start_time'];
        if ($time_start >= time()) {
            return errorResponse('', ['data' => $time_start - time()]);
        }

        return successResponse('', ['data' => $good['skid']]);
    }

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
        $data_part = array_slice((array)$data, ($page - 1) * $limit, $limit);

        $res = [
            'code' => 0,
            'msg' => "",
            'count' => count($data),
            'data' => $data_part
        ];

        return json($res);
    }

    public function secKill()
    {
        $num = input('num');
        $skid = input('link');
        $gid = db('good')->where(['skid' => $skid])->field('id')->find();
        if (!$gid) {
            return errorResponse('商品信息错误');
        }
        $gid = $gid['id'];

        $redis = new \Redis;
        $redis->connect(config('redis_ip'), config('redis_port'));

        for ($i = 0; $i < $num; $i++) {
            $result = $redis->lPop($gid);
            if (!$result) {
                for ($j = 0; $j < $i; $j++) {
                    $redis->lPush($gid, true);
                }
                return errorResponse('商品数量不足！');
            }
        }

        $data = ['uid' => $this->get_uid(), 'gid' => $gid, 'num' => $num];
        $url = config('order_url') .'?' . http_build_query($data);
        file_get_contents($url);

        return successResponse();
    }

    public function get_uid()
    {
        $token = request()->header('Auth');
        $result = model('MyJWT')->varify($token);
        $user_id = $result['user_id'];
        return $user_id;
    }

    public function getOrder()
    {
        $id = $this->get_uid();
        $orders = db('order')->where(['user_id' => $id])->select();

        $orders = array_map(
            function ($order) {
                if($order['state']==1){
                    $order['state']="成功";
                } else {
                    $order['state']="失败";
                };

                $order['name']=db('good')->where(['id'=>$order['good_id']])->find()['name'];
                return $order;
            },
            (array)$orders
        );

        return successResponse('', ['data'=>$orders]);
    }
}
