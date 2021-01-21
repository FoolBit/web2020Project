<?php
namespace app\admin\controller;

use think\Controller;

class Shop extends Common 
{
    public function shop_info()
    {
        return $this->fetch();
    }

    public function shop_add()
    {
        return $this->fetch();
    }

    public function shop_edit()
    {
        $id = input('id');
        $result = model('Shop')->getShopInfo(['id'=>$id])[0];
        if($result['state']==2){
            $result['checked2'] = "checked";
            $result['checked1'] = "";
        } else {
            $result['checked1'] = "checked";
            $result['checked2'] = "";
        }

        if($result['recommand']==2){
            $result['recommand2'] = "checked";
            $result['recommand1'] = "";
        } else {
            $result['recommand1'] = "checked";
            $result['recommand2'] = "";
        }

        // if(!$result['image']){
        //     $result['image'] = '__STATIC__/image/default.jpg';
        // }

        $this->assign('id', $id);
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function updateShopInfo()
    {
        $info = input();
        // return var_dump($info['amount']);
        // return var_dump(filter_var($info['amount'], FILTER_VALIDATE_INT));
        $info['price'] = round((float)$info['price'], 2);
        $info['amount'] = (int)$info['amount'];
        $info['sold'] = (int)$info['sold'];
        $info['start_time'] = strtotime($info['start_time']);
        $result = model('Shop')->checkShopInfo($info);
        if(!$result['state']){
            return $result;
        }

        // $old = db('good')->where(['id'=>$info['id']])->find();
        if($info['state'] == 1){
            $info['updatetime'] = time();
        }

        $result = model('Shop')->updateShopInfo($info['id'], $info);
        return $result;
    }

    public function addShop()
    {
        $info = input();
        $info['price'] = round((float)$info['price'], 2);
        $info['amount'] = (int)$info['amount'];
        $info['sold'] = (int)$info['sold'];
        $info['start_time'] = strtotime($info['start_time']);
        $result = model('Shop')->checkShopInfo($info);
        if(!$result['state']){
            return $result;
        }

        $result = db('good')->insert($info);
        return $result;
    }

    public function delShop()
    {
        $id = input('id');
        return db('good')->where(['id'=>$id])->delete();
    }

    public function upload()
    {
        $id = input('id');
        model('Shop')->upload($id);
        $md5_salt = config('md5_salt');
        $skid = md5(md5(time()).$md5_salt);
        db('good')->where(['id'=>$id])->strict(false)->update(['state'=>1, 'skid'=>$skid]);
        return true;
    }

    public function download()
    {
        $id = input('id');
        model('Shop')->download($id);
        db('good')->where(['id'=>$id])->strict(false)->update(['state'=>2, 'skid'=>'']);
        return true;
    }
}
