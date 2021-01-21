<?php
namespace app\admin\controller;

use app\admin\model\MyJWT;
use think\Controller;

class Login extends Controller
{
    /**
     * 登入
     */
    public function index()
    {
        if(model('MyJWT')->varify(cookie('auth')))
        {
            $this->success('登入成功', 'index/index');
        }

        return $this->fetch();
    }


    /**
     * 处理登录请求
     */
    public function dologin()
    {
        $username = input('post.username');
        $password = input('post.password');

        if (!$username) {
            $this->error('用户名不能为空');
        }
        if (!$password) {
            $this->error('密码不能为空');
        }

        $admin = model('Admin');
        $info = $admin->getInfoByUsername($username);

        if (!$info) {
            $this->error('用户名或密码错误');
        }
        $md5_salt = config('md5_salt');

        if (md5(md5($password).$md5_salt) != $info['password']) {
            $this->error('用户名或密码错误');
        } else {
            $JWT = model('MyJWT')->issue($info);
            cookie('auth', $JWT, config('life_time'));
            //记录登录信息
            $admin->editInfo($info['id']);
            $this->success('登入成功', 'index/index');
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        cookie('auth',null);
        $this->success('退出成功', 'login/index');
    }

}
