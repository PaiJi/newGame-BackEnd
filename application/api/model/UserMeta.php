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
    public function queryUserMetaByMultiKey($userId, $metaKey, $subMetaKey)
    {
        $map1 = [
            ['user_id', '=', $userId],
            ['meta_key', '=', $metaKey],
        ];

        $map2 = [
            ['user_id', '=', $userId],
            ['meta_key', '=', $subMetaKey],
        ];

        $queryUserMetaResult = Db::table('ng_user_meta')->alias('meta')->join('club c', 'meta.meta_value = c.id')
            ->
            whereOr([$map1, $map2])
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

    public function queryUserMeta($userId, $metaKey, $meta_value)
    {
        $queryUserMetaResult = UserMeta::where(['user_id' => $userId, 'meta_key' => $metaKey, 'meta_value' => $meta_value])->find();
        if ($queryUserMetaResult == null) {
            return $result = ['queryResult' => '0'];
        } elseif ($queryUserMetaResult) {
            return $result = ['queryResult' => '1',
                'queryData' => $queryUserMetaResult
            ];
        }

    }
}