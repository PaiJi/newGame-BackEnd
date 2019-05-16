<?php


namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;
use think\model\Collection;
use think\db;


class Apply extends Model
{
    public function getStatusAttr($value)
    {
        $status = [0=>'未通过',1=>'待审核',2=>'通过'];
        return $status[$value];
    }
}