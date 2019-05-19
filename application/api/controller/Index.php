<?php

namespace app\api\controller;

use think\Controller;
use think\facade\Request;
use think\facade\Session;

class Index extends Controller
{
    public function index()
    {
        return '<section><h1>newGame,故事的起点</h1><p>您访问的是newGame API接口，文档访问云雀平台，感谢使用。beta1.0</p></section> <style>section{position:absolute;bottom:50px;right:70px;color:#dedede;text-align:right;}body{background-color:#171717;}</style>';
    }

    public function systemPowerSwitch()
    {
        $handleValue = Request::get('handleValue');
        $User = new \app\api\model\User();
        $setting = new \app\api\model\Setting();
        $loginResult = $User->checkLogin();
        if ($loginResult['loginStatus'] == 0) {
            return json($loginResult);//未登录用户返回错误json
        } else {
            $verifyAdminResult = $User->verifyAdmin($loginResult['userId']);//检查是否是管理员。
            if ($verifyAdminResult['isAdmin'] == 1) {
                $result = $setting->systemPowerSwitch($handleValue);
                if ($result['switchResult'] == 1) {
                    return json($result);//一般返回执行成功json
                } else {
                    return json($result);//返回出错的json
                }
            } else {
                $result = ['switchResult' => '0', 'errMsg' => '权限不足，非法操作'];
                return json($result);//非管理员返回json
            }
        }
    }
    public function getSystemStatus()
    {
        $setting = new \app\api\model\Setting();
        return json($setting->checkSystemOnline());
    }
}