<?php

namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;

class UserMeta extends Model
{
    protected $pk = 'meta_id';

    public function addUserMeta($userId, $metaKey, $metaValue)
    {
        $meta = new UserMeta;
        $addUserMetaResult = $meta->save([
            'user_id' => $userId,
            'meta_key' => $metaKey,
            'meta_value' => $metaValue
        ]);
        if ($addUserMetaResult == 1) {
            return $result = [
                'addUserMetaResult' => '1',
                'errMsg' => ''
            ];
        } else {
            return $result = [
                'addUserMetaResult' => '0',
                'errMsg' => '没有成功写入用户角色表数据'
            ];
        }
    }
}