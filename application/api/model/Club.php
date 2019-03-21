<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;

class Club extends Model
{
    public function addClub()
    {
        $clubName=Request::post('clubName');
        $imgUrl=Request::post('imgUrl');
        $intro=Request::post('intro');
        $clubBelong=Request::post('clubBelong');
        $clubSort=Request::post('clubSort');
        $clubManager=Request::post('clubAdmin');
        $joinMode=Request::post('joinMode');
        $status=Request::post('status');
        //这里应该对所有变量做判空检查
        $club = Club::where('name', $clubName)->find();
        if($club){
            return $addClubResult=[
                'addClubResult'=>'0',
                'errMsg'=>'已经有重名社团存在了！'
            ];
        }
        else{
            $club =new Club;
            $dbInsertResult = $club->save([
                'name' => $clubName,
                'intro' => $intro,
                //'manager_user' => $clubManager,
                'status' => $status,
                'join_mode' => $joinMode,
                'sort' => $clubSort,
                'belong'=>$clubBelong,
                'img_logo'=>$imgUrl
            ]);
            $dbInsertResultId=$club->id;
            $userMeta=new UserMeta;
            $userMetaAddResult=$userMeta->addUserMeta($clubManager,clubAdmin,$dbInsertResultId);
            if (($dbInsertResult == 1)&&($userMetaAddResult['addUserMetaResult'])) {
                return $addClubResult = [
                    'addClubResult' => '1',
                    'errMsg' => ''
                ];
            } else {
                return $addClubResult = [
                    'addClubResult' => '0',
                    'errMsg' => '添加失败，联系管理员'.$userMetaAddResult['errMsg']
                ];
            }
        }
    }
}