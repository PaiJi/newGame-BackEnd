<?php
namespace app\api\controller;

class User
{
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
}
