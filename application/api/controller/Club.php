<?php
/**
 * Created by PhpStorm.
 * User: Harold
 * Date: 2019/3/19
 * Time: 20:18
 */

namespace app\api\controller;

use think\Controller;
use think\facade\Session;

Class Club extends Controller
{
    protected $beforeActionList = [
        'disableCorb' => ['except' => 'index']
    ];

    public function disableCorb()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS");
        header("Access-Control-Allow-Headers:Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    public function addClub()
    {
        $Club = new \app\api\model\Club();
        $User=new \app\api\model\User();
        $loginResult=$User->checkLogin();
        if($loginResult['loginStatus']==0){
            return json($loginResult);
        }else{
            $verifyAdminResult=$User->verifyAdmin($loginResult['userId']);
            if($verifyAdminResult['isAdmin']==1){
                $addClubResult=$Club->addClub();
                if($addClubResult['addClubResult']==1){
                    return json($addClubResult);
                }else{
                    return json($addClubResult);
                }
            }
            else{
                return json($verifyAdminResult);
            }
        }

//        if ($loginResult['loginStatus']) {
//            Session::set('userId', $loginResult['userId']);
//            return json($loginResult);
//        } else {
//            return json($loginResult);
//        }
    }

}