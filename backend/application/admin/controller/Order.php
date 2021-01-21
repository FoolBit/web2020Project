<?php
namespace app\admin\controller;

use think\Controller;

class Order extends Common 
{
    public function index()
    {
        return $this->fetch();
    }

    public function delOrder()
    {
        $id = input('id');
        return db('order')->where(['id'=>$id])->delete();
    }

    public function delAll()
    {
        return db('order')->delete(true);
    }
}
