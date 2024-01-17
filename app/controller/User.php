<?php

namespace app\controller;


use app\common\model\AppModel\Role;
use app\exception\BaseException;
use Firebase\JWT\JWT;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use think\helper\Str;
use think\Request;
use app\common\model\AppModel\User as UserModel;

/**
 * @Route("user")
 */
class User
{
    protected $middleware = [
        'userValidate' => ['only' => [
            'login',
            'register',
            'edit_user'
        ]],
        'jwt' => ['except' => [
            'login',
            'register',
            'demo',
            'role'
        ]],
        'auth' => ['only' => [
            'profile',
            'changeRole',
            'add'
        ]]
    ];

    /**
     * @Route ("edit_user",method="POST")
     *
     * @param Request $request
     * @throws BaseException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function edit_user(Request $request)
    {
        $params = $request->param();
        $user = UserModel::find($request->user_info->id);
        if($user->password !== $params['password']){
            throw new BaseException("密码错误");
        }
        $user -> username = $params['username'];
        if(isset($params['topassword']) && $params['topassword'] !== ""){
            $user->password = $params['topassword'];
        }
        $user->save();
        return success("修改成功");
    }

    /**
     *
     * @Route ("change_avatar",method="POST")
     * @throws BaseException
     */
    public function change_avatar(Request $request)
    {
        $file = $request->file('file');

        list($uploadManager,$domain,$token) = initQiNiuYun();
        try {
            list($ret, $err) = $uploadManager->putFile($token, null, $file->getRealPath());
            $fileUrl = $domain . '/' . $ret['key'];
            $fileUrl = 'http://'.$fileUrl;
            $user = UserModel::find($request->user_info->id);
            $user->avatar = $fileUrl;
            $user->save();
            return success(['msg'=>'上传成功','data'=>$fileUrl]);
        }catch (\Exception $e){
            throw new BaseException("上传失败");
        }


    }

    /**
     *
     * @Route ("role")
     */
    public function role(Request $request)
    {
        $role = Role::with('urls')->find(1);
        $jsondata = ($role->urls);

        return json(buildTree($jsondata));
    }

    /**
     *
     * @Route ("register",method="post")
     * @throws BaseException
     */
    public function register(Request $request)
    {


        try {
            $params = $request->param();
            if ((new \app\common\model\AppModel\User)->where('username', $params['username'])->find()) {
                throw new BaseException("此用户名重复");
            }
            $user = UserModel::create($params);
            $user->roles()->save(2);
            return success($user);
        } catch (\Exception $e){
            throw new BaseException("注册失败");
        }


    }

    // 刷新token

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @Route ("refresh",method="post"))
     */
    public function refresh(Request $request)
    {

        Cache::delete($request->header('Authorization'));
        $user = UserModel::find($request->user_info->id);
        $role =  $user->roles[0];
        $perms = unsetFields($role->perms()->select()->toArray());
        $user = [
            'id' => $user->id,
            'username' => $user->username,
        ];

        $key = env('JWT_KEY');
        $payload = [
            'user' => $user,
            'exp' => time() + EXPIRES
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        // 设置权限缓存
        Cache::set("role".$user['id'],$role);
        Cache::set("perms".$user['id'],$perms);
        // 设置token缓存
        Cache::set($token,["id"=>$user['id'],"exp"=>$payload['exp']],EXPIRES);
        // 返回生成的Token给用户
        $return = [
            'token' => $token,
            'user' => $user,
            'exp' => time() + EXPIRES
        ];
        return success($return);
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @Route ("login",method="post")
     */
    public function login(Request $request)
    {
        $user1 = $request->user;
        $params = $request->param();
        $isRememberMe = isset($params['rememberMe']) && $params['rememberMe'];
        $exp_time = $isRememberMe ? 60*60*24*365 :EXPIRES;
        $user_exp_time = $isRememberMe ? time() + 60*60*24*365 : time() + EXPIRES;
//        halt($params);
        $role =  $user1->roles[0];
        $perms = unsetFields($role->perms()->select()->toArray());
        $urls = $role->urls()->select();
        $user = [
            'id' => $user1->id,
            'username' => $user1->username,
            'avatar'=>$user1->avatar,
            'exp'=>$user_exp_time
        ];
        $key = env('JWT_KEY'); // 修改为你的密钥
        $payload = [
            'user' => $user,
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        // 设置权限缓存
        Cache::set("role".$user['id'],$role);
        Cache::set("perms".$user['id'],$perms);
        // 设置token缓存
        Cache::set($token,["id"=>$user['id'],'exp'=> $user_exp_time],$exp_time);

        // 返回生成的Token给用户
        $return = [
            'token' => $token,
            'user' => $user,
            'exp' => $user_exp_time,
            'urls' => buildTree($urls),
            'perms' => $perms,
        ];
        return success($return);
    }

    /**
     * @param Request $request
     * @return string
     * @Route ("profile",method="post")
     */

    public function profile(Request $request)
    {

        return "ok";


    }
    public function demo(Request $request)
    {

        return json(Cache::get("urls"));
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @Route ("logout",method="post")
     */
    public function logout(Request $request)
    {

        Cache::delete($request->header('Authorization'));
        return success("退出登陆成功");
    }

    /**
     * @param $request
     * @Route ("change_role",method="post")
     */
    public function change_role(Request $request)
    {
        $user_id = $request->param("user_id");
        $role_id = $request->param("role_id");
        // 更改这个用户的角色
        if($user_id && $role_id){
            $user = UserModel::find($user_id);
            // 关联表中属于这个user的全部删除
            $user->roles()->detach();
            $user->roles()->attach($role_id);
            return success("修改成功");
        }else{
            return Fail("修改失败");
        }
    }
    /**
     * @Route ("add",method="POST")
     *
     */
    public function add()
    {
        return 'add';
    }
}