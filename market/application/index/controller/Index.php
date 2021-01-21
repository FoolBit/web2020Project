<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller 
{
    public function index()
    {
        return $this->redirect('index.php/index/login');
    }

    public function home()
    {
        $active = ['active', '', ''];
        $this->assign('active', $active);
        return $this->fetch();
    }

    public function good_list()
    {
        $active = ['', 'active', ''];
        $this->assign('active', $active);
        return $this->fetch();
    }

    public function detail()
    {
        $active = ['', 'active', ''];
        $this->assign('active', $active);
        $id = input('id');
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function logout()
    {
        $active = ['', '', ''];
        $this->assign('active', $active);
        return $this->fetch();
    }

    public function info()
    {
        $active = ['', '', 'active'];
        $this->assign('active', $active);
        return $this->fetch();
    }

    public function test()
    {
        $good = db('good')->where(['state'=>1])->where(['id'=>5])->find();
        if(!$good)
            return errorResponse('商品id错误');
        
        $time_start = $good['start_time'];
        return successResponse('', ['data'=>$time_start-time()]);
    }

}
