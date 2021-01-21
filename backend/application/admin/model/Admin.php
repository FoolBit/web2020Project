<?php


namespace app\admin\model;

use think\Db;

class Admin extends \think\Model
{

    public $status = array(1 => '无效', 2 => '有效');

    /**
     * 登录时调用
     * @param String $username 用户名
     * @return Array
     */
    public function getInfoByUsername($username)
    {
        $res = $this->field('id,username,password,realname,status')
            ->where(array('username' => $username, 'status' => 1))
            ->find();
        if ($res) {
            $res = $res->data;
        }

        return $res;
    }

    /**
     *
     * @param int $userid 用户ID
     * @return Array
     */
    public function getUserGroups($uid)
    {

        $res = db('admin_group_access')->field('group_id')->where('uid', $uid)->select();

        $user_groups = array();
        if ($res) {
            foreach ($res as $v) {
                $user_groups[] = $v['group_id'];
            }
        };

        return $user_groups;
    }

    public function getUserInfo($where = [], $field = [])
    {
        $list = db('admin')->where($where)->field($field)->select();
        return $list;
    }

    public function getGroupInfo($where = [], $field = [])
    {
        $list = db('admin_group')->where($where)->field($field)->select();
        return $list;
    }

    public function getUserGroup($where = [], $field = [])
    {
        $result =  db('admin_group_access')
            ->alias('aga')
            ->join('admin_group ag', 'aga.group_id = ag.id', 'LEFT')
            ->where($where)
            ->field($field)
            ->select();

        return $result;
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
        return db('admin')->where('username', $username)->update($info);
    }

    public function updateGroupInfo($id, $info)
    {
        $result = $this->checkGroupInfo($info, true);
        if (!$result['state'])
            return false;

        return db('admin_group')->where('id', $id)->update($info);
    }

    public function delUser($uid)
    {
        return db('admin')->where('id', $uid)->delete();
    }

    public function delGroup($id)
    {
        return db('admin_group')->where('id', $id)->delete();
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
        $state = db('admin')->strict(false)->insert($info);
        if (!$state) {
            return buildMsg(false, '注册失败!');
        }

        return buildMsg(true, '');
    }

    public function addGroup($info)
    {
        $msg = [];
        $msg = $this->checkGroupInfo($info);
        if (!$msg['state'])
            return $msg;

        // add
        $state = db('admin_group')->strict(false)->insert($info);
        if (!$state) {
            return buildMsg(false, '添加失败!');
        }

        return buildMsg(true, '');
    }

    // 检测用户发来的数据对不对
    public function checkUsername($username)
    {
        if (db('admin')->where('username', $username)->find()) {
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

    public function checkGroupInfo($info, $update = false)
    {
        $len = strlen($info['name']);
        if ($len <= 0) {
            return buildMsg(false, '请输入组名');
        }

        if (!$update && $this->getGroupInfo(['name' => $info['name']])) {
            return buildMsg(false, '组名重复！');
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
        return db('admin')->where('username', $info['username'])->update(['password' => $pwd]);
    }

    public function update_user_group($uid, $group)
    {
        db('admin_group_access')->where('uid', $uid)->delete();
        foreach ($group as $group_id) {
            $data = [
                'uid' => $uid,
                'group_id' => $group_id
            ];
            db('admin_group_access')->insert($data);
        }
    }
}
