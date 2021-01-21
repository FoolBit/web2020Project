<?php
namespace app\index\controller;

use think\Controller;

class Log extends Common 
{
    public function index()
    {
        return $this->fetch();
    }

    public function delLog()
    {
        $id = input('id');
        return db('admin_log')->where(['id'=>$id])->delete();
    }

    public function delAll()
    {
        return db('admin_log')->delete(true);
    }
}
