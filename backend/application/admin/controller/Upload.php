<?php

namespace app\admin\controller;

use think\Controller;

class Upload extends Common
{
    public function upload()
    {
        $res = [
            'code' => 0,
            'msg'  => ''
        ];
        // 获取表单上传文件 例如上传了001.jpg
        $id = input('id');
        $file = request()->file('file');
        $img_path = config('img_path');
        $info = $file->validate(['ext' => 'jpg,png,gif'])->move($img_path);

        if ($info) {
            $path = $info->getSaveName();
            $result = db('good')->where(['id' => $id])->setField('image', $path);
            if (!$result) {
                $res['code'] = 1;
                $res['msg'] = '数据库更新失败';
            }
        } else {
            // 上传失败获取错误信息
            $res['code'] = 1;
            $res['msg'] = '获取文件失败';
        }
        return $res;
    }
}
