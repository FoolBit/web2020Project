<?php

/**
 * 后台公共文件
 */

namespace app\index\controller;

use think\Controller;

class Common extends Controller
{

    public function __construct(\think\Request $request = null)
    {

        parent::__construct($request);
        $token = request()->header('Auth');
        //检测JWT
        $result = model('MyJWT')->varify($token);
        if (!$result) {
            $this->error('请登陆', 'login/index');
        }

        $user_id = $result['user_id'];

        //记录日志
        // $this->_addLog();
        $this->assign('username', $result['username']);
    }

    /**
     * 权限检查
     */
    public function _checkAuthor($user_id)
    {
        if ($user_id == 1) {
            return true;
        }

        // $c = strtolower(request()->controller());
        // $a = strtolower(request()->action());
        $c = request()->controller();
        $a = request()->action();

        if (preg_match('/^public_/', $a)) {
            return true;
        }
        if ($c == 'Index' && $a == 'index') {
            return true;
        }

        $name = $c.'/'.$a;
        return model('Auth')->checkAuth($name);

        return false;
    }

    /**
     * 权限检查
     */
    protected function _checkToken()
    {
        $token = cookie("token");
        // 没有token
        if (!$token) {
            return false;
        }

        // 数据库没有这个token信息
        $token_info = db('token')->where("token", $token)->select();
        if (!$token_info) {
            return false;
        }

        $expire_time = $token_info['expire_time'];
        // token过期了
        if ($expire_time < time()) {
            db('token')->where('token', $token)->delete();
            return false;
        }

        // token正常
        session('user_id', $token_info['user_id']);
        session('username', $token_info['username']);
        session('group_id', model('Admin')->getUserGroups());
        return true;
    }

    /**
     * 记录日志
     */
    private function _addLog()
    {

        $data = array();
        $data['querystring'] = request()->query() ? '?' . request()->query() : '';
        $data['m'] = request()->module();
        $data['c'] = request()->controller();
        $data['a'] = request()->action();
        $data['userid'] = session('user_id');
        $data['username'] = session('username');
        $data['ip'] = ip2long(request()->ip());
        $data['time'] = time();
        $arr = array('Index/index', 'Log/index');
        if (!in_array($data['c'] . '/' . $data['a'], $arr)) {
            db('admin_log')->insert($data);
        }
    }
}
