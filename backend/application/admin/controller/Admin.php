<?php
namespace app\admin\controller;

use think\Controller;
use Exception;
class Admin extends Common 
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
        $result = model('Admin')->getUserInfo(['id'=>$id])[0];
        if(!$result){
            $this->error('用户ID错误！');
        }
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function user_group_edit()
    {
        $id = input('get.id');
        $result = model('Admin')->getUserInfo(['id'=>$id])[0];
        if(!$result){
            $this->error('用户ID错误!');
        }
        $user_groups = model('Admin')->getUserGroup(['aga.uid'=>$id], ['name']);
        $user_groups = array_map(
            function ($group) {
                $group = $group['name'];
                return $group;
            },
            $user_groups
        );

        $groups = model('Admin')->getGroupInfo([], ['id', 'name']);
        $groups = array_map(
            function ($group) use($user_groups){
                if(in_array($group['name'], $user_groups)){
                    $group['checked'] = 'checked';
                } else {
                    $group['checked'] = '';
                }
                return $group;
            },
            $groups
        );

        $this->assign('result', $result);
        $this->assign('list', $groups);
        return $this->fetch();
    }

    public function user_add()
    {
        return $this->fetch();
    }

    public function pwd_edit()
    {
        $id = input('get.id');
        $result = model('Admin')->getUserInfo(['id'=>$id])[0];
        if(!$result){
            $this->error('用户ID错误!');
        }
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function public_edit_info()
    {
        $id = session('user_id');
        $result = model('Admin')->getUserInfo(['id'=>$id])[0];
        $this->assign('result', $result);
        return $this->fetch();
    }

    public function public_updateUserInfo()
    {
        $info = input();
        $username = session('username');
        return model('Admin')->updateUserInfo($username, $info);
    }

    public function public_updatePassword()
    {
        $info = input();
        $info['username'] = session('username');
        return model('Admin')->updatePassword($info);
    }

    public function updateUserInfo()
    {
        $info = input();
        $username = $info['username'];
        unset($info['username']);
        return model('Admin')->updateUserInfo($username, $info);
    }

    public function delUser()
    {
        $uid = input('id');
        if($uid==1)
        {
            return false;
        }
        return model('Admin')->delUser($uid);
    }

    public function addUser()
    {
        $info = input();
        return model('Admin')->addUser($info);
    }

    public function updatePassword()
    {
        $info = input();
        return model('Admin')->updatePassword($info);
    }

    public function updateUserGroup()
    {
        $data = input();
        $id = model('Admin')->getUserInfo(['username'=>$data['username']], ['id']);
        if(!$id){
            return false;
        }
        $id = $id[0]['id'];
        db('admin_group_access')->where(['uid'=>$id])->delete();

        if(isset($data['group']))
        {
            $result = [];
            foreach ($data['group'] as $gid => $v) {
                $tmp = db('admin_group_access')->where(['id', $gid])->find();
                if(!$tmp)
                    continue;
                $data = ['uid'=>$id, 'group_id'=>$gid];
                try{
                $result = db('admin_group_access')->insert($data);
                } catch (Exception $e){
                    continue;
                }
            }
            return var_dump($result);
        }
        return true;
    }
}
