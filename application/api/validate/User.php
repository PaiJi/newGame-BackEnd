<?php
/**
 * Created by PhpStorm.
 * User: Harold
 * Date: 2019/2/1
 * Time: 15:32
 */

namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username' => 'require|alphaNum|max:24',
        'email' => 'require|email|max:255',
        'password'=>'require|max:255'
    ];

    protected $message = [
        'name.require' => '名称必须',
        'name.max' => '名称最多不能超过25个字符',
        'age.number' => '年龄必须是数字',
        'age.between' => '年龄只能在1-120之间',
        'email.require' => '邮箱为必填',
        'email.email'=>'邮箱格式错误',
        'email.max'=>'邮箱地址最大长度超出'
    ];
    protected $scene = [
        'loginCheck' => ['email','password'],
        'username'=>['username']
    ];

}