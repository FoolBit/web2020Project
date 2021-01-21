<?php


namespace app\admin\model;

use think\Db;

class User extends \think\Model
{

    public $status = array(1 => '无效', 2 => '有效');


    public function getUserInfo($where = [], $field = [])
    {
        $list = db('user')->where($where)->field($field)->select();
        return $list;
    }


    /**
     * 登陆更新
     * @param int $id id
     * @param array $data 更新的数据
     */
    public function editInfo($id, $data = array())
    {
        $data['lastlogintime'] = time();
        $data['lastloginip'] = ip2long(request()->ip());

        // allowField,过滤数组中的非数据表字段数据
        $res = $this->allowField(true)->save($data, ['id' => $id]);
        return $res;
    }

    public function updateUserInfo($username, $info)
    {
        if (!$this->checkUserInfo($info)['state'])
            return false;
        return db('user')->where('username', $username)->update($info);
    }

    public function delUser($uid)
    {
        return db('user')->where('id', $uid)->delete();
    }

    public function addUser($info)
    {
        $msg = [];
        $msg = $this->checkUsername($info['username']);
        if (!$msg['state'])
            return $msg;
        $msg = $this->checkPassword($info);
        if (!$msg['state'])
            return $msg;
        $msg = $this->checkUserInfo($info);
        if (!$msg['state'])
            return $msg;

        // regist
        $md5_salt = config('md5_salt');
        $info['password'] = md5(md5($info['password']) . $md5_salt);
        $state = db('user')->strict(false)->insert($info);
        if (!$state) {
            return buildMsg(false, '注册失败!');
        }

        return buildMsg(true, '');
    }

    // 检测用户发来的数据对不对
    public function checkUsername($username)
    {
        if (db('user')->where('username', $username)->find()) {
            return buildMsg(false, "用户名已存在");
        } else return buildMsg(true, '');
    }

    public function checkPassword($info)
    {
        if ($info['password'] != $info['repassword']) {
            return buildMsg(false, "两次输入密码不同!");
        }
        $len = strlen($info['password']);
        if ($len < 6 || $len > 16) {
            return buildMsg(false, '密码长度不正确');
        }
        return buildMsg(true, '');
    }

    public function checkUserInfo($info)
    {
        // mobile
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $info['mobile']);
        if (!$result) {
            return buildMsg(false, '手机号不正确!');
        }

        // email
        if (!filter_var($info['email'], FILTER_VALIDATE_EMAIL)) {
            return buildMsg(false, '邮箱格式不正确!');
        }

        return buildMsg(true, '');
    }

    public function updatePassword($info)
    {
        if (!$this->checkPassword($info)) {
            return false;
        }

        $md5_salt = config('md5_salt');
        $pwd = md5(md5($info['password']) . $md5_salt);
        return db('user')->where('username', $info['username'])->update(['password' => $pwd]);
    }
}
