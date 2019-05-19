<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;
use think\helper\Time;
use think\Db;

class Activity extends Model
{
    public function getStatusAttr($value)
    {
        $status = [0 => '未开始', 1 => '报名中', 2 => '已结束'];
        return $status[$value];
    }
    public function getTypeAttr($value)
    {
        $status = [0 => '未选择', 1 => '社内活动', 2 => '公开活动'];
        return $status[$value];
    }
    public function getEnableCheckinAttr($value)
    {
        $status = [0 => '关闭', 1 => '开启'];
        return $status[$value];
    }
    public function addActivity($createUserId, $clubId)
    {
        $activityName = Request::post('activityName');
        $imgUrl = Request::post('imgUrl');
        $intro = Request::post('intro');
        $maxPeople = Request::post('maxPeople');
        $activityType = Request::post('activityType');
        $status = Request::post('status');
        $enable_checkin = Request::post(('enable_checkin'));
        $startTime = Request::post('startTime');
        $endTime = Request::post('endTime');
        //这里应该对所有变量做判空检查
        $activity = new Activity;
        $dbInsertResult = $activity->save([
            'clubid' => $clubId,
            'name' => $activityName,
            'intro' => $intro,
            'imgUrl' => $imgUrl,
            'max_people' => $maxPeople,
            'type' => $activityType,
            'status' => $status,
            'create_user_id' => $createUserId,
            'enable_checkin' => $enable_checkin,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);
        if (($dbInsertResult == 1)) {
            return $addActivityResult = [
                'addActivityResult' => '1',
                'errMsg' => ''
            ];
        } else {
            return $addActivityResult = [
                'addActivityResult' => '0',
                'errMsg' => '添加失败，联系管理员'
            ];
        }
    }

    public function editActivity($createUserId, $clubId, $activityId)
    {
        $activityName = Request::post('activityName');
        $imgUrl = Request::post('imgUrl');
        $intro = Request::post('intro');
        $maxPeople = Request::post('maxPeople');
        $activityType = Request::post('activityType');
        $status = Request::post('status');
        $enable_checkin = Request::post(('enable_checkin'));
        $startTime = Request::post('startTime');
        $endTime = Request::post('endTime');
        //这里应该对所有变量做判空检查
        $activity = new Activity;
        $dbInsertResult = $activity->save([
            'clubid' => $clubId,
            'name' => $activityName,
            'intro' => $intro,
            'imgUrl' => $imgUrl,
            'max_people' => $maxPeople,
            'type' => $activityType,
            'status' => $status,
            'create_user_id' => $createUserId,
            'enable_checkin' => $enable_checkin,
            'start_time' => $startTime,
            'end_time' => $endTime
        ], ['id' => $activityId]);
        if (($dbInsertResult == 1)) {
            return $editActivityResult = [
                'editActivityResult' => '1',
                'errMsg' => ''
            ];
        } else {
            return $editActivityResult = [
                'editActivityResult' => '0',
                'errMsg' => '修改失败，联系管理员'
            ];
        }
    }

    public function deleteActivity($activityId)
    {
        //$activity = Activity::get($activityId);
        $activity = new Activity;
        $dbInsertResult = $activity->save([
            'unavailable' => '1'
        ], ['id' => $activityId]);
        if (($dbInsertResult == 1)) {
            return $editActivityResult = [
                'deleteActivityResult' => '1',
                'errMsg' => ''
            ];
        } else {
            return $editActivityResult = [
                'deleteActivityResult' => '0',
                'errMsg' => '删除失败，联系管理员'
            ];
        }
    }

    public function activityList()
    {
        if (Request::has('option', 'get') && Request::has('value', 'get')) {
            $result = Activity::where(Request::param('option'), Request::param('value'))->select();
        } else {
            $result = Activity::where('unavailable', '0')->select();//只获取没有被软删除的活动
        }
        return $result;
    }

    public function activityDetail($activityId)
    {
        $result = Activity::get($activityId);
        return $result;
    }

    public function getMyActivityList($userId)
    {
        $map1 = [
            ['user_id', '=', $userId]
        ];
        $queryUserMetaResult = Db::table('ng_activity_meta')->alias('meta')->join('activity a', 'meta.activity_id = a.id')
            ->
            where('user_id', $userId)->where('meta.unavailable', '0')->where('a.unavailable','0')
            ->select();
        if ($queryUserMetaResult == null) {
            return $result = ['queryResult' => '0'];
        } elseif ($queryUserMetaResult) {
            return $result = ['queryResult' => '1',
                'queryData' => $queryUserMetaResult
            ];
        }
    }

    public function MyManagerActivity($userId, $clubId)
    {
        $queryUserMeta = new UserMeta;
        $queryUserMetaResult = $queryUserMeta->queryUserMeta($userId, 'clubAdmin', $clubId);
        if ($queryUserMetaResult['queryResult'] == 1) {
            $activityResult = Activity::Where('clubid', $clubId)->where('unavailable',0)->select();
            return $activityResult;
        }
    }

    public function joinActivity($userId, $activityId)
    {
        $activity = new ActivityMeta;
        $queryResult = Db::table('ng_activity_meta')->where(['user_id' => $userId, 'activity_id' => $activityId])->select();
        $activityType=Db::table('ng_activity')->where('id',$activityId)->value('type');
        $targetClubId=Db::table('ng_activity')->where('id',$activityId)->value('clubid');
        $max_people=Db::table('ng_activity')->where('id',$targetClubId)->value('max_people');
        $now_people=Db::table('ng_activity')->where('id',$targetClubId)->value('now_people');
        $userQuery=new UserMeta();
        $userQueryResult=$userQuery->queryUserMeta($userId,'clubMember',$targetClubId);
        if ($queryResult) {
            return $result = [
                'queryResult' => '0',
                'errMsg' => '您已加入该活动！'
            ];
        } else {
            if($activityType==1&&$userQueryResult['queryResult']==1){
                //活动是私有的时候检查权限
                if($max_people==0){
                    $activity->save([
                        'user_id' => $userId,
                        'activity_id' => $activityId
                    ]);
                    $this->regulatePeople($activityId);
                    return $result = [
                        'queryResult' => '1',
                        'errMsg' => ''
                    ];
                }
                $leftNum=$max_people-$now_people;
                if($leftNum>=1){
                    $activity->save([
                        'user_id' => $userId,
                        'activity_id' => $activityId
                    ]);
                    $this->regulatePeople($activityId);
                    return $result = [
                        'queryResult' => '1',
                        'errMsg' => ''
                    ];
                }else{
                    return $result = [
                        'queryResult' => '0',
                        'errMsg' => '活动报名人数已满'
                    ];
                }
            }
            if($activityType==2){
                if($max_people==0){
                    $activity->save([
                        'user_id' => $userId,
                        'activity_id' => $activityId
                    ]);
                    $this->regulatePeople($activityId);
                    return $result = [
                        'queryResult' => '1',
                        'errMsg' => ''
                    ];
                }
                $leftNum=$max_people-$now_people;
                if($leftNum>=1){
                    $activity->save([
                        'user_id' => $userId,
                        'activity_id' => $activityId
                    ]);
                    $this->regulatePeople($activityId);
                    return $result = [
                        'queryResult' => '1',
                        'errMsg' => ''
                    ];
                }else{
                    return $result = [
                        'queryResult' => '0',
                        'errMsg' => '活动报名人数已满'
                    ];
                }
            }
                return $result = [
                    'queryResult' => '0',
                    'errMsg' => '这是一个社团私有活动，请先加入社团才能加入该活动'
                    ];
        }
    }

    public function exitActivity($userId, $activityId)
    {
        $activity = new ActivityMeta;
        $queryResult = Db::table('ng_activity_meta')->where(['user_id' => $userId, 'activity_id' => $activityId])->select();
        if ($queryResult) {
            $activity->save([
                'unavailable' => 1
            ], ['id' => $queryResult['0']['id']]);
            $this->regulatePeople($activityId);
            return $result = [
                'queryResult' => '1',
                'errMsg' => '您已退出该活动！'
            ];
        } else {
            return $result = [
                'queryResult' => '0',
                'errMsg' => '您没有参加这个活动'
            ];
        }
    }

    public function checkinActivity($userId, $activityId)
    {
        $enable_checkin = Activity::where('id', $activityId)->value('enable_checkin');
        if ($enable_checkin == 1) {
            $time_now = time();
            $start_time = Activity::where('id', $activityId)->value('start_time');
            $end_time = Activity::where('id', $activityId)->value('end_time');
            if ($time_now < strtotime($start_time)) {
                return $result = [
                    'queryResult' => '0',
                    'errMsg' => '不要心急，活动还没有开始呢！'
                ];
            }
            if (($time_now >= strtotime($start_time)) && ($time_now <= strtotime($end_time))) {
                $checkin = new ActivityCheckin;
                $checkinStatus = $checkin->where('user_id', $userId)->where('activity_id', $activityId)->find();
                if ($checkinStatus) {
                    return $result = [
                        'queryResult' => '0',
                        'errMsg' => '您已经签到过了，请不要重复操作~'
                    ];
                }
                if ($checkinStatus == null) {
                    $clubId = Activity::where('id', $activityId)->value('clubid');
                    $checkin->save([
                        'club_id' => $clubId,
                        'user_id' => $userId,
                        'activity_id' => $activityId
                    ]);
                    return $result = [
                        'queryResult' => '1',
                        'errMsg' => '签到成功，感谢参加本次活动。'
                    ];
                }

            }
            if ($time_now > strtotime($end_time)) {
                return $result = [
                    'queryResult' => '0',
                    'errMsg' => '活动已结束，下次早点来签到啦！'
                ];
            }
            var_dump($time_now);
        } else {
            return $result = [
                'queryResult' => '0',
                'errMsg' => '该活动不需要签到或者该活动不存在'
            ];
        }
    }

    public function activityApplyList($activityId)
    {
        $queryUserMetaResult = Db::table('ng_activity_meta')->alias('meta')->join('ng_user u', 'meta.user_id = u.id')
            ->
            where('activity_id', $activityId)->where('unavailable',0)
            ->select();
        return $queryUserMetaResult;
    }
    public function activityCheckinList($activityId)
    {
        $queryUserMetaResult = Db::table('ng_activity_checkin')->alias('meta')->join('ng_user u', 'meta.user_id = u.id')
            ->
            where('activity_id', $activityId)
            ->select();
        return $queryUserMetaResult;
    }
}