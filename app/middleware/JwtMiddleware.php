<?php
namespace app\middleware;

use app\enums\ErrorStatusEnum;
use app\exception\AppException\JWTMissException;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Cache;
use think\Request;
use think\Response;

class JwtMiddleware
{
    /**
     * @throws JWTMissException
     */
    public function handle(Request $request, \Closure $next)
    {

        $token = $request->header('Authorization'); // 从请求头获取token，假设token存储在Authorization头部


        if (!!!$token) {

            throw new JWTMissException();
        }

        try {
            $key = env('JWT_KEY');

            $decoded = JWT::decode($token,new Key($key,'HS256'));

            $user_info = ((array)$decoded)['user'];
            $request->user_info = $user_info;

            $cache_user = Cache::get($token);
            if(!isset($cache_user)){
                throw new JWTMissException("非法的token或Token过期",ErrorStatusEnum::JWT_TOKEN_ILLEGAL);
            }else{
                // 没过期
                if((($cache_user['exp']-time()) <= 600)){
                    // 刷新token
                    $exp_time = EXPIRES;
                    $user_exp_time = time() + EXPIRES;
                    $key = env('JWT_KEY');
                    $payload = [
                        'user' => [
                            'id' => $user_info->id,
                            'username' => $user_info->username,
                            'avatar'=>$user_info->avatar,
                            'exp'=>$user_exp_time
                        ],
                    ];

                    $_token = JWT::encode($payload, $key, 'HS256');
                    Cache::set($_token,["id"=>$user_info->id,'exp'=> $user_exp_time],$exp_time);
                    header('Authorization: '.$_token);
                    return $next($request);
                }else{
                    return $next($request);
                }

            }

        } catch (ExpiredException $e){
            if(Cache::has($token)){
                Cache::delete($token);
            }
            throw new JWTMissException("Token过期",ErrorStatusEnum::JWT_TOKEN_EXPIRED);
        }catch (\Exception $e) {

            throw new JWTMissException("Token无效",ErrorStatusEnum::JWT_TOKEN_INVALID);
        }

    }
}