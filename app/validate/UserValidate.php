<?php

namespace app\validate;

use think\Validate;

class UserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'username|用户名' => 'require|min:6|max:25',
        'password|密码' => 'require|min:6|max:25',
        'topassword|要修改的密码' => 'min:6|max:25'
    ];

    // 错误提示
//    protected $message = [
//        'username.require' => '用户名不能为空',
//        'password.require' => '密码不能为空',
//
//    ];

    // 验证场景
    protected $scene = [
        'login'  => ['username', 'password'],
        'register' =>  ['username', 'password'],
        'edit' =>['username', 'password','topassword'],
    ];

}