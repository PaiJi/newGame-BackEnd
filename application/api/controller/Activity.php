<?php

namespace app\api\controller;

use think\Controller;
use think\facade\Session;
use think\facade\Request;

Class Activity extends Controller
{
    public function addActivity()
    {
        //此处应该有判空检测
        $clubId = Request::get('clubid');
        $Club = new \app\api\model\Club();
        $User = new \app\api\model\User();
        $Activity = new \app\api\model\Activity();
        $loginResult = $User->checkLogin();
        if ($loginResult['loginStatus'] == 0) {
            return json($loginResult);//未登录用户返回错误json
        } else {
            $verifyClubAdminResult = $Club->checkUserIsClubAdmin($loginResult['userId'], $clubId);//检查是否是社团管理员。
            if ($verifyClubAdminResult['isAdmin'] == 1) {
                $addActivityResult = $Activity->addActivity($loginResult['userId'], $clubId);
                if ($addActivityResult['addActivityResult'] == 1) {
                    return json($addActivityResult);//一般返回执行成功json
                } else {
                    return json($addActivityResult);//返回出错的json
                }
            } else {
                return json($verifyClubAdminResult);//非管理员返回json
            }
        }
    }

    public function editActivity()
    {
        //此处应该有判空检测
        $activityId=Request::get('activityId');
        $Club = new \app\api\model\Club();
        $User = new \app\api\model\User();
        $Activity = new \app\api\model\Activity();
        $clubId=$Activity->where('id',$activityId)->value('clubid');
        $loginResult = $User->checkLogin();
        if ($loginResult['loginStatus'] == 0) {
            return json($loginResult);//未登录用户返回错误json
        } else {
            $verifyClubAdminResult = $Club->checkUserIsClubAdmin($loginResult['userId'], $clubId);//检查是否是社团管理员。
            if ($verifyClubAdminResult['isAdmin'] == 1) {
                $editActivityResult = $Activity->editActivity($loginResult['userId'], $clubId,$activityId);
                if ($editActivityResult['editActivityResult'] == 1) {
                    return json($editActivityResult);//一般返回执行成功json
                } else {
                    return json($editActivityResult);//返回出错的json
                }
            } else {
                return json($verifyClubAdminResult);//非管理员返回json
            }
        }
    }

    public function deleteActivity()
    {
        //此处应该有判空检测
        $activityId=Request::get('activityId');
        $Club = new \app\api\model\Club();
        $User = new \app\api\model\User();
        $Activity = new \app\api\model\Activity();
        $clubId=$Activity->where('id',$activityId)->value('clubid');
        $loginResult = $User->checkLogin();
        if ($loginResult['loginStatus'] == 0) {
            return json($loginResult);//未登录用户返回错误json
        } else {
            $verifyClubAdminResult = $Club->checkUserIsClubAdmin($loginResult['userId'], $clubId);//检查是否是社团管理员。
            if ($verifyClubAdminResult['isAdmin'] == 1) {
                $deleteActivityResult = $Activity->deleteActivity($activityId);
                if ($deleteActivityResult['deleteActivityResult'] == 1) {
                    return json($deleteActivityResult);//一般返回执行成功json
                } else {
                    return json($deleteActivityResult);//返回出错的json
                }
            } else {
                return json($verifyClubAdminResult);//非管理员返回json
            }
        }
    }

    public function activityList()
    {
        $Activity = new\app\api\model\Activity();
        $result = $Activity->activityList();
        return json($result);
    }

    public function activityOfClub()
    {
        $clubId=Request::get('clubId');
        $Activity=new \app\api\model\Activity();
        $result=$Activity->where('clubid',$clubId)->where('unavailable',0)->select();
        return json($result);

    }

    public function activityDetail()
    {
        $Activity = new\app\api\model\Activity();
        $activityId=Request::get('activityId');
        $result = $Activity->activityDetail($activityId);
        return json($result);
    }

    public function getMyActivity()
    {
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->getMyActivityList($loginCheck['userId']);
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

    public function MyManagerActivity()
    {
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        $clubId=Request::get("clubId");
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->MyManagerActivity($loginCheck['userId'],$clubId);
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

    public function joinActivity()
    {
        $activityId=Request::get('activityId');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->joinActivity($loginCheck['userId'],$activityId);
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
    public function exitActivity()
    {
        $activityId=Request::get('activityId');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->exitActivity($loginCheck['userId'],$activityId);
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
    public function checkinActivity()
    {
        $activityId=Request::get('activityId');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->checkinActivity($loginCheck['userId'],$activityId);
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
    public function activityApplyList()
    {
        $activityId=Request::get('activityId');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->activityApplyList($activityId);
            return json($result);
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'getActivityApplyListResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }
    public function activityCheckinList()
    {
        $activityId=Request::get('activityId');
        $userStatus = new \app\api\model\User();
        $loginCheck = $userStatus->checkLogin();
        if ($loginCheck['loginStatus'] == '1') {
            $Activity = new \app\api\model\Activity();
            $result = $Activity->activityCheckinList($activityId);
            return json($result);
        };
        if ($loginCheck['loginStatus'] == '0') {
            $result = [
                'getActivityApplyListResultCode' => '0',
                'code' => '42',
                'errMsg' => '请先登录'
            ];
            return json($result);
        }
    }
}