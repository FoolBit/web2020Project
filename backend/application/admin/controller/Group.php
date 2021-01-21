<?php
namespace app\admin\controller;

use PDO;
use think\Controller;

class Group extends Common 
{
    public function index()
    {
        return phpinfo();
    }

    public function group_info()
    {
        return $this->fetch();
    }

    public function group_edit()
    {
        $id = input('get.id');
        $result = model('Admin')->getGroupInfo(['id'=>$id])[0];
        if(!$result){
            $this->error('操作失败！');
        }
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function group_add()
    {
        return $this->fetch();
    }

    public function updateGroupInfo()
    {
        $info = input();
        $id = $info['id'];
        unset($info['id']);
        return model('Admin')->updateGroupInfo($id, $info);
    }

    public function delGroup()
    {
        $id = input('id');
        return model('Admin')->delGroup($id);
    }

    public function addGroup()
    {
        $info = input();
        return model('Admin')->addGroup($info);
    }
}
