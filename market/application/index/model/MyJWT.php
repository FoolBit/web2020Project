<?php

namespace app\index\model;

use think\Db;
use \Firebase\JWT\JWT;

class MyJWT extends \think\Model
{
    public function issue($info)
    {
        $key = config('key');
        $t = time();
        $payload = array(
            "iss" => config('iss'),
            "aud" => config('aud'),
            "iat" => $t,
            "nbf" => $t,
            "exp" => $t + config('life_time'),
            "username" => $info['username'],
            "user_id" => $info['id'],
        );
        $token = JWT::encode($payload, $key);
        return $token;
    }

    public function varify($token)
    {
        if(!$token)
            return null;
        $key = config('key');
        $decoded = (array)JWT::decode($token, $key, ['HS256']);
        $ok = true;
        $ok = $ok && ($decoded['iss'] == config('iss'));
        $ok = $ok && ($decoded['aud'] == config('aud'));
        $ok = $ok && ($decoded['iat'] <= time());
        $ok = $ok && ($decoded['exp'] > time());
        if(!$ok)
            return null;
        else {
            session('user_id', $decoded['user_id']);
            session('username', $decoded['username']);
            return $decoded;
        }
    }
}
