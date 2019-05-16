<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;
use think\Db;

class Club extends Model
{

    public function users()
    {
        return $this->hasManyThrough('User', 'UserMeta', 'meta_value', 'id', 'id');
    }

    public function getStatusAttr($value)
    {
        $status = [0 => '已注销', 1 => '正常运营', 2 => '筹备中', 3 => '运营异常'];
        return $status[$value];
    }

    public function getJoinModeAttr($value)
    {
        $join_mode = [0 => '不允许新成员', 1 => '人工审核', 2 => '仅限邀请', 3 => '无限制'];
        return $join_mode[$value];
    }

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

    public function updateClub()
    {
        $clubId = Request::post('clubId');
        $clubName = Request::post('clubName');
        $imgUrl = Request::post('imgUrl');
        $intro = Request::post('intro');
        $clubBelong = Request::post('clubBelong');
        $clubSort = Request::post('clubSort');
        $clubManager = Request::post('clubAdmin');
        $joinMode = Request::post('joinMode');
        $status = Request::post('status');
        //这里应该对所有变量做判空检查
        $club = Club::where('id', $clubId)->find();
        //这里检查一下这个用户是否是这个社团的管理员
        if ($club) {
            $club = new Club;
            $dbInsertResult = $club->save([
                'name' => $clubName,
                'intro' => $intro,
                'status' => $status,
                'join_mode' => $joinMode,
                'sort' => $clubSort,
                'belong' => $clubBelong,
                'img_logo' => $imgUrl
            ], ['id' => $clubId]);
            if ($dbInsertResult == 1) {
                return $updateClubResult = [
                    'updateClubResult' => '1',
                    'errMsg' => ''
                ];
            } else {
                return $updateClubResult = [
                    'updateClubResult' => '0',
                    'errMsg' => '更新社团信息失败，联系管理员'
                ];
            }

        } else {
            return $updateClubResult = [
                'updateClubResult' => '0',
                'errMsg' => '指定社团信息不存在？请检查输入。'
            ];
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
        $club=new Club();
        $queryUserMetaResult = $userMeta->where('user_id', $userId)->where('meta_key','clubMember')->select();
        foreach ($queryUserMetaResult as $item){
            $clubResult=$club->where('id',$item['meta_value'])->find();
            $item['clubInfo']=$clubResult;
        }
        return $result=['queryResult'=>'1','data'=>$queryUserMetaResult];
    }

    public function getMyAdminClubList($userId)
    {
        $userMeta = new UserMeta;
        $queryUserMetaResult = Db::table('ng_user_meta')->alias('meta')->join('club c', 'meta.meta_value = c.id')
            ->
            where('user_Id', $userId)->where('meta_key', 'clubAdmin')
            ->select();
        //$queryUserMetaResult = UserMeta::whereOr([$map1, $map2])->select();
        if ($queryUserMetaResult == null) {
            return $result = ['queryResult' => '0'];
        } elseif ($queryUserMetaResult) {
            return $result = ['queryResult' => '1',
                'queryData' => $queryUserMetaResult
            ];
        }
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

    public function checkUserIsClubAdmin($userId, $clubId)
    {
        $userMeta = new UserMeta;
        $queryUserMetaResult = $userMeta->queryUserMeta($userId, 'clubAdmin', $clubId);
        if ($queryUserMetaResult['queryResult'] == 1) {
            return $result = ['isAdmin' => '1'];
        } else {
            return $result = ['isAdmin' => '0'];
        }
    }

    public function updateQuestion($targetClubId, $questionData)
    {
        foreach ($questionData as $item) {
            if ($item['type'] == 'radio' || $item['type'] == 'checkbox') {
                $item['answer'] = json_encode($item['answer']);
            }
            $question = Question::get($item['id']);
            if ($question) {
                $questionQuery = new Question();
                $questionQuery->save([
                    'club_id' => $targetClubId,
                    'type' => $item['type'],
                    'msg' => $item['msg'],
                    'answer' => $item['answer'],
                    'required' => $item['required'],
                    'sort' => $item['sort']
                ], ['id' => $item['id']]);
            } else {
                $questionQuery = new Question();
                $questionQuery->save([
                    'club_id' => $targetClubId,
                    'type' => $item['type'],
                    'msg' => $item['msg'],
                    'answer' => $item['answer'],
                    'required' => $item['required'],
                    'sort' => $item['sort']
                ]);
            }
        }

        return $result = ['queryResult' => '1'];
    }

    public function submitApplyForm($userId, $targetClubId, $applyForm)
    {
        $clubQuery = new UserMeta();
        $clubQueryResult = $clubQuery->where('user_id', $userId)->where('meta_key', 'clubMember')->where('meta_value',$targetClubId)->find();
        if ($clubQueryResult) {
            return $result = ['submitApplyFormResultCode' => '0', 'errMsg' => '您已经是社团成员，表单不会被记录'];
        };
        $applyQuery = new Apply();
        $applyQueryResult = $applyQuery->where('user_id', $userId)->where('club_id', $targetClubId)->find();
        if ($applyQueryResult) {
            return $result = ['submitApplyFormResultCode' => '0', 'errMsg' => '您已经有一份申请正在处理中，请不要重新提交内容'];
        }
        $clubModeQueryResult = Club::get($targetClubId)->value('join_mode');
        if ($clubModeQueryResult == '1') {
            $applyQuery = new Apply();
            $applyQuery->save([
                'club_id' => $targetClubId,
                'user_id' => $userId,
                'status' => 1
            ]);
            $applyId = $applyQuery->id;
            foreach ($applyForm as $item) {
                if ($item['type'] == 'checkbox') {
                    $item['answer'] = json_encode($item['answer']);
                }
                $applyContentQuery = new ApplyContent();
                $applyContentQuery->save([
                    'apply_id' => $applyId,
                    'question_id'=>$item['question_id'],
                    'type' => $item['type'],
                    'question' => $item['question'],
                    'answer' => $item['answer']
                ]);
            }
            return $result = ['submitApplyFormResultCode' => '1', 'errMsg' => ''];
        }else{
            return $result = ['submitApplyFormResultCode' => '0', 'errMsg' => '这个社团不需要提交表单'];
        }
    }
    public function getApplyList($clubId){
        $apply=new Apply();
        $user=new User();
        $applyList=$apply->where('club_id',$clubId)->select();
        foreach ($applyList as $item){
            $queryResult=$user::where($item['user_id'])->field(['password','last_login','create_time','admin'],true)->find();
            $item['userInfo']=$queryResult;
        }
        return $applyList;

    }
    public function applyContent($applyId){
        $applyContent=new ApplyContent();
        $applyContentResult=$applyContent->where('apply_id',$applyId)->select();
        return $applyContentResult;
    }
    public function handleApply($applyId,$handleContent,$userId,$clubId){
        $apply=new Apply();
        if($handleContent=='true'){
            $apply->save([
                'status'=>'2'
            ],['id'=>$applyId]);
            $meta=new UserMeta();
            $meta->addUserMeta($userId,'clubMember',$clubId);
            return $handleApplyResult=['handleApplyResultCode'=>'1'];
        }
        if($handleContent=='false'){
            $apply->save([
                'status'=>'0'
            ],['id'=>$applyId]);
            return $handleApplyResult=['handleApplyResultCode'=>'1'];
        }
        return $handleApplyResult=['handleApplyResultCode'=>'0'];
    }
}