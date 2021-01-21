<?php
namespace app\admin\controller;

use think\Controller;

class Menu extends Common 
{
    public function menu_info()
    {
        return $this->fetch();
    } 

    public function menu_edit()
    {
        $id = input('get.id');
        // return $id;
        $data = model('Menu')->getMenuInfo();
        $item = $data[$id];
        if($item['display']==1){
            $item['checked1']="checked";
            $item['checked2']="";
        } else {
            $item['checked2']="checked";
            $item['checked1']="";
        }
        $this->assign('result', $item);


        return $this->fetch();
    }

    public function menu_add()
    {
        return $this->fetch();
    }

    public function menu_access()
    {
        $mid = input('id');
        $menu = db('menu')->where(['id'=>$mid])->find();
        $groups = model('Admin')->getGroupInfo();

        $groups = array_map(
            function($group) use($mid){
                $group['checked'] = "";
                $rules = explode(',', $group['rules']);
                if(in_array($mid, $rules)){
                    $group['checked'] = "checked";
                }
                return $group;
            },
            $groups
        );

        $this->assign('list', $groups);
        $this->assign('menu', $menu);
        return $this->fetch();
    }

    public function updateMenuInfo()
    {
        $info = input();
        $id = $info['id'];
        $parentid = $info['parentid'];
        $result = db('menu')->where(['id'=>$parentid])->find();
        if($parentid!=0 && !$result){
            return buildMsg(false, '父节点不存在');
        }

        unset($info['id']);
        $result = db('menu')->strict(false)->where(['id'=>$id])->update($info);
        if(!$result){
            return buildMsg(false, '修改失败!');
        }

        return buildMsg(true, '');
    }

    public function delMenu()
    {
        $id = input('id');
        return db('menu')->where(['id'=>$id])->delete();
    }

    public function addMenu()
    {
        $info = input();
        $parentid = $info['parentid'];
        $result = db('menu')->where(['id'=>$parentid])->find();
        if($parentid!=0 && !$result){
            return buildMsg(false, '父节点不存在');
        }

        if(!$info['c']||!$info['a']||!$info['display']){
            return buildMsg(false, '信息填写不完整!');
        }

        $result = db('menu')->insert($info);
        if(!$result){
            return buildMsg(false, "添加失败!");
        }

        return buildMsg(true, '');
    }

    public function updateMenuAccess()
    {
        $mid = input('mid');
        $groups_ = model('Admin')->getGroupInfo();
        $groups = [];
        foreach ($groups_ as $group) {
            $key = $group['id'];
            $group['rules'] = explode(',', $group['rules']);
            $groups[$key] = $group;
        }
        $gid = array_keys($groups);

        $gid_on = array();
        if(isset(input()['group']))
            $gid_on = array_keys(input()['group']);
        $gid_off = array_diff($gid, $gid_on);

        // add rules
        foreach ($gid_on as $gid) {
            $group = $groups[$gid];
            if(!in_array($mid, $group['rules'])){
                $groups[$gid]['rules'][]=$mid;
            }
        }

        // remove rules
        foreach ($gid_off as $gid) {
            $group = $groups[$gid];
            if(in_array($mid, $group['rules'])){
                $groups[$gid]['rules'] = array_diff($group['rules'], array($mid));
            }
        }

        // update
        foreach ($groups as $group) {
            $gid = $group['id'];
            $rules = trim(implode(',', $group['rules']),",");
            $result = db('admin_group')->where(['id'=>$gid])->setField('rules', $rules);
        }

        return true;
    }
}
