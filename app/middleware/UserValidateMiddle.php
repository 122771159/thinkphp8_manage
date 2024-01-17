<?php

namespace app\middleware;

use app\common\model\AppModel\User;
use app\enums\StatusCodeEnum;
use app\exception\AppException\ParameterException;
use app\exception\AppException\UserMissException;
use app\validate\UserValidate;

use Exception;
use think\exception\ValidateException;
use think\helper\Str;

class UserValidateMiddle
{

    /**
     * @throws ParameterException
     * @throws UserMissException
     */
    public function handle($request, \Closure $next)
    {
        $baseUrl = $request->baseUrl();
        // 获取请求参数
        $params = $request->param();

        try {
            // 验证用户信息，指定验证类为UserValidate，验证场景为login，并检查参数是否通过验证
            if(Str::contains($baseUrl,'login')){
                validate(UserValidate::class)->scene("login")->check($params);
            }
           if(Str::contains($baseUrl,'edit_user')){
               validate(UserValidate::class)->scene("edit")->check($params);
           }
        } catch (ValidateException $e) {

            throw new ParameterException($e->getMessage(),1,StatusCodeEnum::BAD_REQUEST);
        }
        // 在数据库中查询
        if(Str::contains($baseUrl,'login')){
            $exist = (new User())->check($params['username'], $params['password']);
            if(!$exist){
                throw new UserMissException('用户不存在',1,StatusCodeEnum::BAD_REQUEST);
            }else{

                $request->user = $exist;
            }
        }




        return $next($request);
    }
}