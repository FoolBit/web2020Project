<?php


namespace app\index\model;

use think\Db;
use think\Model;

class User extends Model
{
    public function getUserInfo($where=[], $field=[])
    {
        return db('user')->where($where)->field($field)->select();
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
}
