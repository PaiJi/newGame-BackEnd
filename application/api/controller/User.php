<?php
namespace app\api\controller;

use think\Controller;
use think\facade\Session;

class User extends Controller
{
    protected $beforeActionList=[
        'disableCorb'=>['except'=>'index']
    ];
    public function disableCorb(){
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
        header("Access-Control-Allow-Headers:Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }
    public function index()
    {
        return '<section><h1>newGame,故事的起点</h1><p>您访问的是newGame API接口，文档访问云雀平台，感谢使用。beta1.0</p></section> <style>section{position:absolute;bottom:50px;right:70px;color:#dedede;text-align:right;}body{background-color:#171717;}</style>';
    }

    public function login()
    {
        $User=new \app\api\model\User();
        $loginResult=$User->login();
        if ($loginResult['loginStatus']) {
            Session::set('userId', $loginResult['userId']);
            return json($loginResult);
        } else {
            return json($loginResult);
        }
    }
    public function register(){
        $User=new \app\api\model\User();
        $registerResult=$User->register();
        if ($registerResult['registerStatus']) {
            //Session::set('userId', $loginResult['userId']);
            return json($registerResult);
        } else {
            return json($registerResult);
        }
    }
    public function getUserInfo(){
        $userId=Session::get('userId');
        if($userId==null){
            $result=[
                'loginStatus'=>'false'
            ];
            return json($result);
        }if($userId){
            $user=new \app\api\model\User();
            $userInfo=$user->whoAmI();
            $result=[
                'loginStatus'=>'true',
                'userInfo'=>$userInfo
            ];
            return json($result);
        }
    }
    public function logout(){
        $user=new \app\api\model\User();
        $loginStatus=$user->checkLogin();
        if($loginStatus['loginStatus']==0){
             $result=[
                'execResult'=>'0',
                'errMsg'=>'您好像没没有登录哦,所以我要注销什么呢？'
            ];
             return json($result);
        }
        if($loginStatus['loginStatus']==1){
            Session::delete('userId');
             $result=[
                'execResult'=>'1',
                'errMsg'=>'注销成功！'
            ];
             return json($result);
        }
    }
}
