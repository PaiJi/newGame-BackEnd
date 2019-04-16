<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;

class Club extends Model
{
    public function addClub()
    {
        $clubName = Request::post('clubName');
        $imgUrl = Request::post('imgUrl');
        $intro = Request::post('intro');
        $clubBelong = Request::post('clubBelong');
        $clubSort = Request::post('clubSort');
        $clubManager = Request::post('clubAdmin');
        $joinMode = Request::post('joinMode');
        $status = Request::post('status');
        //这里应该对所有变量做判空检查
        $club = Club::where('name', $clubName)->find();
        if ($club) {
            return $addClubResult = [
                'addClubResult' => '0',
                'errMsg' => '已经有重名社团存在了！'
            ];
        } else {
            $club = new Club;
            $dbInsertResult = $club->save([
                'name' => $clubName,
                'intro' => $intro,
                'status' => $status,
                'join_mode' => $joinMode,
                'sort' => $clubSort,
                'belong' => $clubBelong,
                'img_logo' => $imgUrl
            ]);
            $dbInsertResultId = $club->id;
            $userMeta = new UserMeta;
            $userMetaAddResult = $userMeta->addUserMeta($clubManager, 'clubAdmin', $dbInsertResultId);
            if (($dbInsertResult == 1) && ($userMetaAddResult['addUserMetaResult'])) {
                return $addClubResult = [
                    'addClubResult' => '1',
                    'errMsg' => ''
                ];
            } else {
                return $addClubResult = [
                    'addClubResult' => '0',
                    'errMsg' => '添加失败，联系管理员' . $userMetaAddResult['errMsg']
                ];
            }
        }
    }

    public function clubList()
    {
        //$club=new Club;
        if (Request::has('option', 'get') && Request::has('value', 'get')) {
            $result = Club::where(Request::param('option'), Request::param('value'))->select();
        } else {
            $result = Club::all();
        }
        return $result;
    }

    public function getMyClubList($userId)
    {
        $userMeta = new UserMeta;
        $queryUserMetaResult = $userMeta->queryUserMetaByMultiKey($userId, 'clubMember', 'clubAdmin');
        return $queryUserMetaResult;
    }

    public function getClubDetail()
    {
        $clubId = Request::get('clubid');
        $result = Club::get($clubId);
        return $result;
    }

    public function joinClub()
    {
        $targetClubId = Request::get('clubid');
        $userId = Session::get('userId');
        $userMeta = new UserMeta;
        $queryUserMetaResult = $userMeta->queryUserMeta($userId, 'clubMember', $targetClubId);
        if ($queryUserMetaResult['queryResult'] == 1) {
            return $result = ['joinClubResultCode' => '0', 'code' => '42', 'errMsg' => '您已经是俱乐部成员了！'];
        } elseif ($queryUserMetaResult['queryResult'] == 0) {
            $clubJoinMode = Db::table('ng_club')->where('id', $targetClubId)->value('join_mode');
            if ($clubJoinMode == '2') {
                return $result = ['joinClubResultCode' => '0',
                    'clubRequest' => '2',
                    'errMsg' => '需要邀请才可以加入俱乐部哦'];
                //这里还差一段逻辑来判断如果有邀请怎么处理。
            }
            if ($clubJoinMode == '0') {
                return $result = ['joinClubResultCode' => '0', 'clubRequest' => '0', 'errMsg' => '俱乐部暂时不纳新。'];
            }
            if ($clubJoinMode == '3') {
                //执行加入社团逻辑
                $joinClubResult = $userMeta->addUserMeta($userId, 'clubMember', $targetClubId);
                if ($joinClubResult['addUserMetaResult'] == 1) {
                    return $result = ['joinClubResultCode' => '1', 'clubRequest' => '3', 'errMsg' => ''];
                } else {
                    return $joinClubResult;
                }
            }
            if ($clubJoinMode == '1') {
                //是填表逻辑
                echo '加入模式1';
            }
        }
    }

    public function getClubContact()
    {
        $targetClubId = Request::get('clubid');
        $club = $this->get($targetClubId);
        $club = UserMeta::where('meta_value', $targetClubId)->select();
        $result = [];
        foreach ($club as $item) {
            //var_dump($item->user->hidden(['user'=>['username','phone','email']])->toArray()) ;
            $tempArray = [
                'username' => $item->user['username'],
                'nickname' => $item->user['nickname'],
                'phone' => $item->user['phone'],
                'clubRole' => $item['meta_key']
            ];
            //var_dump($tempArray);
            //array_push($tempArray,$item->user->visible(['profile'=>['address','phone','email']])->toArray());
            //$tempArray[0]['clubRole']=$item['meta_key'];
            //array_push($tempArray,$item['meta_key']);
            array_push($result, $tempArray);
        }
        return $result;
    }
}