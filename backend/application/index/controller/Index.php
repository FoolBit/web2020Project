<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller 
{
    public function index()
    {
        return redirect('index.php/admin/login');
    }

}
