<?php

namespace app\common\model\AppModel;

use app\common\model\BaseModel;

class Role extends BaseModel
{
    // 角色-权限关联
    public function urls()
    {
        return $this->belongsToMany(Url::class, 'role_url');
    }
    public function perms()
    {
        return $this->belongsToMany(Perm::class,'role_perm');
    }
}