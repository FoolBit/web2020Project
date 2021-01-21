<?php


namespace app\admin\model;

use think\Db;

class Shop extends \think\Model
{

    public $status = array(1 => '无效', 2 => '有效');

    public function getShopInfo($where = [], $field = [])
    {
        $list = db('good')->where($where)->field($field)->select();
        return $list;
    }

    public function checkShopInfo($info)
    {
        if (!isset($info['name'])) {
            return buildMsg(false, '商品名称不可为空！');
        }

        if (!isset($info['amount'])) {
            return buildMsg(false, $info['amount']);
        }
        if (filter_var($info['amount'], FILTER_VALIDATE_INT) === false || (int)$info['amount'] < 0) {
            return buildMsg(false, $info['amount']);
        }

        if (filter_var($info['sold'], FILTER_VALIDATE_INT) === false || (int)$info['sold'] < 0) {
            return buildMsg(false, '起售数量错误');
        }

        if (!isset($info['price'])) {
            return buildMsg(false, '价格错误');
        }

        if (!is_numeric($info['price']) || $info['price'] < 0) {
            return buildMsg(false, '价格错误');
        }


        if ($info['start_time'] === FALSE || $info['start_time'] == -1) {
            return buildMsg(false, '起售时间错误');
        }

        if ($info['state'] != 1 && $info['state'] != 2) {
            return buildMsg(false, '状态错误');
        }

        return buildMsg(true, '');
    }

    public function updateShopInfo($id, $info)
    {
        $m = db('good');
        $result = $m->where(['id' => $id])->strict(false)->update($info);
        if (!$result) {
            return buildMsg(false, '更新失败');
        }
        return buildMsg(true, '');
    }

    public function upload($id)
    {
        $redis_ip = config('redis_ip');
        $redis_port = config('redis_port');
        $redis = new \Redis;
        $redis->connect($redis_ip, $redis_port);
        $result = db('good')->where(['id' => $id])->find();
        $num = $result['amount'];
        for ($i = 0; $i < $num; $i++) {
            $redis->lPush($id, true);
        }
    }

    public function download($id)
    {
        $redis_ip = config('redis_ip');
        $redis_port = config('redis_port');
        $redis = new \Redis;
        $redis->connect($redis_ip, $redis_port);
        $redis->delete($id);
    }
}
