<?php


namespace app\index\model;

use think\Db;
use think\Model;

class Auth extends Model
{
    
    public function checkAuth($rule_name)
    {
        $rule_names = $this->get_rule_names();
        if(!$rule_names){
            return false;
        }
        return in_array($rule_name, $rule_names);
    }

    public function get_rule_names()
    {
        $group_ids = session('group_id');
        if(!$group_ids){
            return null;
        }
        // $group_ids = [2,3];
        $rules_list = db('admin_group')->where('id', 'in', implode(',', $group_ids))->field('rules')->select();
        $rules = [];
        foreach($rules_list as $item){
            $rules = array_merge($rules, explode(',', $item['rules']));
        }
        $rules = array_unique($rules);

        $rule_names_list = db('menu')
            ->where('id', 'in', implode(',', $rules))
            ->field(['concat(c, "/", a)'=>'name'])
            ->select();
        $rule_names = [];
        foreach($rule_names_list as $rule_name){
            $rule_names[]=$rule_name['name'];
        }

        return $rule_names;
    }


}
