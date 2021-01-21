<?php
namespace app\admin\controller;

use think\Controller;
use Exception;
class User extends Common 
{
    public function index()
    {
        return phpinfo();
    }

    public function user_info()
    {
        return $this->fetch();
    }

    public function user_group()
    {
        return $this->fetch();
    }

    public function user_edit()
    {
        $id = input('get.id');
        $result = model('User')->getUserInfo(['id'=>$id])[0];
        if(!$result){
            $this->error('用户ID错误！');
        }
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function user_add()
    {
        return $this->fetch();
    }

    public function pwd_edit()
    {
        $id = input('get.id');
        $result = model('User')->getUserInfo(['id'=>$id])[0];
        if(!$result){
            $this->error('用户ID错误!');
        }
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function public_edit_info()
    {
        $id = session('user_id');
        $result = model('User')->getUserInfo(['id'=>$id])[0];
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function public_updateUserInfo()
    {
        $info = input();
        $username = session('username');
        return model('User')->updateUserInfo($username, $info);
    }

    public function public_updatePassword()
    {
        $info = input();
        $info['username'] = session('username');
        return model('User')->updatePassword($info);
    }

    public function updateUserInfo()
    {
        $info = input();
        $username = $info['username'];
        unset($info['username']);
        return model('User')->updateUserInfo($username, $info);
    }

    public function delUser()
    {
        $uid = input('id');
        return model('User')->delUser($uid);
    }

    public function addUser()
    {
        $info = input();
        return model('User')->addUser($info);
    }

    public function updatePassword()
    {
        $info = input();
        return model('User')->updatePassword($info);
    }
}
