<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;

class User extends Model
{
    public function login()
    {
        $email = Request::post('email');
        $passWord = Request::post('password');
        $validate = new \app\api\validate\User();
        if (!$validate->scene('loginCheck')->check(['email' => $email,'password'=>$passWord])) {
            //验证登录输入是否为邮箱
            return $loginResult = [
                'loginStatus' => '0',
                'errMsg' => $validate->getError()
            ];
        } else {
            //邮箱格式正确，进入登录
            $user = User::where('email', $email)->find();
            if ($user) {
                if (password_verify($passWord, $user->password)) {
                    return $loginResult = [
                        'loginStatus' => '1',
                        'userId'=>$user->id,
                        'errMsg' => ''
                    ];
                } else {
                    return $loginResult = [
                        'loginStatus' => '0',
                        'errMsg' => 'Password dont match record.'
                    ];
                }
            } else {
                return $loginResult = [
                    'loginStatus' => '0',
                    'errMsg' => 'Email dont match record.'
                ];
            };
        }
    }

    public function register(){
        $email = Request::post('email');
        $passWord = Request::post('password');
        $validate = new \app\api\validate\User();
        if (!$validate->scene('email')->check(['email' => $email])) {
            //验证登录输入是否为邮箱
            return $loginResult = [
                'loginStatus' => '0',
                'errMsg' => $validate->getError()
            ];
        } else {
            //邮箱格式正确，进入登录
            $user = User::where('email', $email)->find();
            if ($user) {
                if (password_verify($passWord, $user->password)) {
                    return $loginResult = [
                        'loginStatus' => '1',
                        'errMsg' => ''
                    ];
                } else {
                    return $loginResult = [
                        'loginStatus' => '0',
                        'errMsg' => 'Password dont match record.'
                    ];
                }
            } else {
                return $loginResult = [
                    'loginStatus' => '0',
                    'errMsg' => 'Email dont match record.'
                ];
            };
        }
    }
}
