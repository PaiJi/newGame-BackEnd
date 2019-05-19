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
    public function updateInfo()
    {
        $phone = Request::post('phone');
        $gender = Request::post('gender');
        $realName = Request::post('realname');
        $nickname = Request::post('nickname');
        $age = Request::post('age');
        $qq = Request::post('qq');
        $wechat = Request::post('wechat');
        $department = Request::post('department');
        $major = Request::post('major');
        $user = new \app\api\model\User();
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $updateResult = $user->save([
                'gender' => $gender,
                'age' => $age,
                'phone' => $phone,
                'qq' => $qq,
                'wechat' => $wechat,
                'department' => $department,
                'major' => $major,
                'username' => $realName,
                'nickname' => $nickname
            ], ['id' => $loginCheck['userId']]);
            if ($updateResult == 1) {
                $result = ['updateInfoResultCode' => '1',
                    'errMsg' => ''];
                return json($result);
            } else {
                $result = ['updateInfoResultCode' => '0',
                    'errMsg' => '本次操作失败，最大的可能是userID未获取'];
                return json($result);
            }
        }
        if ($loginCheck['loginStatus'] == '0') {
            $result = ['updateInfoResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'];
            return json($result);
        }
    }
        }
    }
}
