<?php

namespace app\middleware;

use app\common\model\AppModel\Role;
use app\common\model\AppModel\Url;
use app\exception\AppException\AuthNoPermissionException;
use app\exception\AppException\JWTMissException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use think\helper\Str;
use think\Request;
use think\Response;
class AuthMiddleware
{
    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws AuthNoPermissionException
     * @throws DbException
     */
    // 使用这个中间件前提，必须使用jwt中间件为前提条件，反之不需要
    public function handle(Request $request, \Closure $next)
    {
        // 获取当前请求的URL
        $url = $request->baseUrl();

        if(Cache::get("urls")){
            Cache::push("urls",$url);
        }else{
            Cache::set("urls",[$request->url()]);
        }
        // 如果是更改权限的请求，判断是不是超级管理员，如果是，则直接放行
        if(Str::contains($url,"change_role") && $request->user_info->id != 1){
            throw new AuthNoPermissionException();
        }elseif (Str::contains($url,"change_role") && $request->user_info->id == 1){
            return $next($request);
        }



        // 通过缓存获取当前用户的角色信息，角色信息已经在登陆的时候自动缓存
//        $role = Cache::get("role".$request->user_info->id);
        $perms = Cache::get("perms".$request->user_info->id);

        foreach ($perms as $u){
            if ($u['url'] == $url) {
                return $next($request);
            }
        }

        throw new AuthNoPermissionException();


    }
}