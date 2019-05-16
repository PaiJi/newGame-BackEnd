<?php
/**
 * Created by PhpStorm.
 * User: Harold
 * Date: 2019/3/19
 * Time: 20:18
 */

namespace app\api\controller;

use app\api\model\Question;
use think\Controller;
use think\facade\Session;
use think\facade\Request;

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
        $User = new \app\api\model\User();
        $loginResult = $User->checkLogin();
        if ($loginResult['loginStatus'] == 0) {
            return json($loginResult);
        } else {
            $verifyAdminResult = $User->verifyAdmin($loginResult['userId']);
            if ($verifyAdminResult['isAdmin'] == 1) {
                $addClubResult = $Club->addClub();
                if ($addClubResult['addClubResult'] == 1) {
                    return json($addClubResult);
                } else {
                    return json($addClubResult);
                }
            } else {
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

    public function updateClub()
    {
        $Club = new \app\api\model\Club();
        $User = new \app\api\model\User();
        $loginResult = $User->checkLogin();
        if ($loginResult['loginStatus'] == 0) {
            return json($loginResult);
        } else {
            $verifyAdminResult = $User->verifyAdmin($loginResult['userId']);
            if ($verifyAdminResult['isAdmin'] == 1) {
                $updateClubResult = $Club->updateClub();
                if ($updateClubResult['updateClubResult'] == 1) {
                    return json($updateClubResult);
                } else {
                    return json($updateClubResult);
                }
            } else {
                return json($verifyAdminResult);
            }
        }
    }

    public function clubList()
    {
        $Club = new\app\api\model\Club();
        $result = $Club->clubList();
        return json($result);
    }

    public function getClubDetail()
    {
        $Club = new \app\api\model\Club();
        $result = $Club->getclubDetail();
        return json($result);
    }

    public function getMyClubList()
    {
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $club = new \app\api\model\Club();
            $result = $club->getMyClubList($loginCheck['userId']);
            return json($result);
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'getMyClubListResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }

    public function getMyAdminClubList()
    {
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $club = new \app\api\model\Club();
            $result = $club->getMyAdminClubList($loginCheck['userId']);
            return json($result);
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'getMyClubListResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }


    public function joinclub()
    {
        $user = new \app\api\model\User();
        $loginCheck = $user->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Club = new \app\api\model\Club();
            $result = $Club->joinClub();
            if ($result['joinClubResultCode'] == '0') {
                return json($result);
            }
            if ($result['joinClubResultCode'] == '1') {
                return json($result);
            }
        }
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'joinClubResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }

    }
    public function exitClub()
    {

    }

    public function getClubContact()
    {
        //这里应该鉴权，之后看看有没有好的方案来解决这个
        $club = new \app\api\model\Club();
        $result = $club->getClubContact();
        return json($result);
    }
    public function getQuestion(){
        $targetClubId = Request::get('clubId');
        $list = Question::where('club_id',$targetClubId)
            ->where('status',1)
            ->order('sort')
            ->select();
        //$list=Question::get('135');
        return $list;
    }
    public function updateQuestion(){
        $targetClubId=Request::get('clubId');
        $questionData=Request::post('data');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $club = new \app\api\model\Club();
            $result = $club->updateQuestion($targetClubId,$questionData);
            return json($result);
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'getMyActivityListResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }
    public function submitApplyForm()
    {
        $targetClubId = Request::get('clubId');
        $applyForm=Request::post('answerList');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $club = new \app\api\model\Club();
            $result = $club->submitApplyForm($loginCheck['userId'],$targetClubId, $applyForm);
            return json($result);
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'submitApplyFormResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }

    public function getApplyList(){
        $targetClubId=Request::get('clubId');
        $Club=new \app\api\model\Club();
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $verifyClubAdminResult = $Club->checkUserIsClubAdmin($loginCheck['userId'], $targetClubId);
            if ($verifyClubAdminResult['isAdmin'] == 1){
                $result = $Club->getApplyList($targetClubId);
                return json($result);
            }else{
                $result=['getApplyListResultCode'=>'0','errMsg'=>'您不是该社团管理员'];
                return json($result);
            }
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'getApplyListResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }
    public function applyContent()
    {
        $targetClubId=Request::get('clubId');
        $applyId=Request::get('applyId');
        $Club=new \app\api\model\Club();
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $verifyClubAdminResult = $Club->checkUserIsClubAdmin($loginCheck['userId'], $targetClubId);
            if ($verifyClubAdminResult['isAdmin'] == 1){
                $result = $Club->applyContent($applyId);
                return json($result);
            }else{
                $result=['getApplyContentResultCode'=>'0','errMsg'=>'您不是该社团管理员'];
                return json($result);
            }
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'applyContentResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }
}