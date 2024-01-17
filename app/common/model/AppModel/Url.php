<?php

namespace app\common\model\AppModel;

use app\common\model\BaseModel;

class Url extends BaseModel
{
// 权限-角色关联
// 设置json类型字段
    protected $json = ['meta'];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_url');
    }
    public function getIsshowAttr($value)
    {
        if(isset($value)&&$value!='0'){
            return true;
        }else{
            return false;
        }
    }
}