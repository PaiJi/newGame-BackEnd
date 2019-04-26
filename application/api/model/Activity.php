<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;
use think\Db;

class Activity extends Model
{
    public function addActivity($createUserId, $clubId)
    {
        $activityName = Request::post('activityName');
        $imgUrl = Request::post('imgUrl');
        $intro = Request::post('intro');
        $maxPeople = Request::post('maxPeople');
        $activityType = Request::post('activityType');
        $status = Request::post('status');
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
}