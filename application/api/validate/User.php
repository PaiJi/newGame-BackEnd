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
    //校验规则
    protected $rule = [
        'nickname' => 'require|alphaNum|max:24',
        'email' => 'require|email|max:255',
        'password'=>'require|max:255',
        'phone'=>'require|mobile|max:13',
        'gender'=>'require|in:boy,girl',
        'realname'=>'require|chs|min:1|max:12'
    ];

    //校验规则对应的提示信息
    protected $message = [
        'nickname.require' => '昵称是一个必填项唷',
        'nickname.max' => '昵称最多不能超过25个字符',
        'nickname.alphaNum'=>'昵称只能包含字母和数字',
        'age.number' => '年龄必须是数字',
        'age.between' => '年龄只能在1-120之间',
        'email.require' => '邮箱为必填',
        'email.email'=>'邮箱格式错误',
        'email.max'=>'邮箱地址最大长度超出',
        'password.require'=>'密码是一个必填项唷',
        'password.max'=>'真的吗你的密码有255这么长？！',
        'phone.reqiure'=>'手机号码是一个必填项哟！',
        'phone.mobile'=>'不知道怎么的格式好像不太对',
        'phone.max'=>'哎呀，手机号码好长',
        'gender.require'=>'请让我们知道您的性别喵',
        'gender.in'=>'性别不太对劲...',
        'realname.require'=>'真名是一个必填项唷',
        'realname.chs'=>'应该是中文姓名desu？',
        'realname.min'=>'没有这么短的姓名吧？',
        'realname.max'=>'请不要填写玛丽苏笔名!'
    ];
    //校验场景
    protected $scene = [
        'loginCheck' => ['email','password'],
        'registerCheck'=>['email','password','nickname','realname','phone','gender']
    ];

}