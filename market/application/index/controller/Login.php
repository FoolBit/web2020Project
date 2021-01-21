<?php
namespace app\index\controller;

use app\admin\model\MyJWT;
use think\Controller;

class Login extends Controller
{
    /**
     * 登入
     */
    public function index()
    {
        return $this->fetch();
    }


    /**
     * 处理登录请求
     */
    public function dologin()
    {
        $username = input('username');
        $password = input('password');

        $res = [
            "code"  =>  0,
            'msg'   =>  '',
            'token' =>  ''
        ];

        if (!$username) {
            return errorResponse('用户名不能为空');
        }
        if (!$password) {
            return errorResponse('密码不能为空');
        }

        $user = model('User');
        $info = $user->getUserInfo(['username'=>$username]);

        if (!$info) {
            return errorResponse('用户名或密码错误');
        }

        $info = $info[0];
        $md5_salt = config('md5_salt');
        if (md5(md5($password).$md5_salt) != $info['password']) {
            return errorResponse('用户名或密码错误');
        } else {
            $JWT = model('MyJWT')->issue($info);
            //记录登录信息
            $user->editInfo($info['id']);
            return successResponse([], ['token'=>$JWT]);
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
