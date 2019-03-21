<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;

class User extends Model
{
    public function login()
    {
        $email = Request::post('email');
        $passWord = Request::post('password');
        $validate = new \app\api\validate\User();
        if (!$validate->scene('loginCheck')->check(['email' => $email, 'password' => $passWord])) {
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
                        'userId' => $user->id,
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

    public function register()
    {
        $email = Request::post('email');
        //$passWord = Request::post('password');
        $passWord = password_hash(Request::post('password'), PASSWORD_DEFAULT);
        $phone = Request::post('phone');
        $gender = Request::post('gender');
        $realName = Request::post('realname');
        $nickname = Request::post('nickname');

        $validate = new \app\api\validate\User();
        if (!$validate->scene('registerCheck')->check(['email' => $email, 'password' => $passWord, 'nickname' => $nickname, 'realname' => $realName, 'phone' => $phone, 'gender' => $gender])) {
            //验证所有传入是否符合规则
            return $registerResult = [
                'registerStatus' => '0',
                'errMsg' => $validate->getError()
            ];
        } else {
            //查看是否已注册
            $user = User::where('email', $email)->find();
            if ($user) {
                return $registerResult = [
                    'registerStatus' => '0',
                    'errMsg' => '咦？这个邮箱已经在记录里啦，请尝试登录或者找回密码'
                ];
            } else {
                $user = new User;
                $dbinsertResult = $user->save([
                    'username' => $realName,
                    'nickname' => $nickname,
                    'password' => $passWord,
                    'email' => $email,
                    'gender' => $gender,
                    'phone' => $phone
                ]);
                if ($dbinsertResult == 1) {
                    return $registerResult = [
                        'registerStatus' => '1',
                        'errMsg' => ''
                    ];
                } else {
                    return $registerResult = [
                        'registerStatus' => '0',
                        'errMsg' => 'Something wrong happen.'
                    ];
                }
            };
        }
    }
}
