<?php

namespace app\admin\controller;

use think\Controller;
use think\cache\driver\Redis;

class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }
}
