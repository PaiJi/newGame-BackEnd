<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;

class User extends Model
{
    public function getAdminAttr($value)
    {
        $status = [0 => '普通用户', 1 => '系统管理员'];
        return $status[$value];
    }
    public function getGenderAttr($value)
    {
        $status = ['boy' => '男', 'girl' => '女'];
        return $status[$value];
    }
    public function getDepartmentAttr($value)
    {

        $status = [0 => '未选择', 1 => '智能与电子工程学院',2=>'计算机与软件学院',3=>'信息与商务管理学院',4=>'数字艺术与设计学院',5=>'外国语学院',6=>'健康医疗科技学院',7=>'高等职业技术学院',8=>'国际教育学院',9=>'继续教育学院'];
        return $status[$value];
    }
    public function getMajorAttr($value)
    {
        $status = [0 => '未选择', 1 => '电子信息工程'];
        return $status[$value];
    }
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
        $age=Request::post('age');
        $qq=Request::post('qq');
        $wechat=Request::post('wechat');
        $department=Request::post('department');
        $major=Request::post('major');

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
                    'phone' => $phone,
                    'age'=>$age,
                    'qq'=>$qq,
                    'wechat'=>$wechat,
                    'department'=>$department,
                    'major'=>$major
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
    public function checkLogin(){
        $userId=Session::get('userId');
        //var_dump($userId);
        if($userId==null){
            return $loginResult=[
                'loginStatus'=>'0',
                'errMsg'=>'用户未登录'
            ];
        }
        else{
            return $loginResult=[
                'loginStatus'=>'1',
                'userId'=>$userId,
                'errMsg'=>''
            ];
        }
    }
    public function verifyAdmin($userId){
        $userInfo = User::where('id', $userId)->find();
        if($userInfo['admin']==1){
            return $verifyResult=[
                'isAdmin'=>'1'
            ];
        }
        else{
            return $verifyResult=[
                'isAdmin'=>'0',
                'errMsg'=>'用户权限不足，非法操作'
            ];
        }
    }
    public function whoAmI(){
        $loginResult=$this->checkLogin();
        if($loginResult['loginStatus']==0){
            return $result=[
                'LoginStatus'=>'0',
                'errMsg'=>'您好像没没有登录哦'
            ];
        }elseif ($loginResult['loginStatus']==1){
            $userInfo=User::where('id',$loginResult['userId'])->hidden(['password'])->find();
            return $userInfo;
        }

    }
}
