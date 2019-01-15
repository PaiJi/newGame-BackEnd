<?php
namespace app\api\model;

use think\Model;
use think\facade\Request;

class User extends Model
{
    public function login()
    {
        $userName=Request::post('username');
        $passWord=Request::post('password');
        return 'really';
    }
}
