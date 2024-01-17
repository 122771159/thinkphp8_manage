<?php

namespace app\common\model\AppModel;


use app\common\model\BaseModel;
use think\model\concern\SoftDelete;


class User extends BaseModel
{
    // 权限-角色关联
    use SoftDelete;
    protected $deleteTime = "delete_time";
    function check($username, $password)
    {

        return self::where(['username' => $username, 'password' => $password])->find();
    }
// 用户-角色关联
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

}