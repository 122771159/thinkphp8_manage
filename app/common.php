<?php


use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use think\response\Json;
const EXPIRES = 7200;
function getError($e): Json
{
//    halt($e);
    return json(["success" => $e->success,"status" => $e->status,"statusInfo" => $e->statusInfo],$e->httpStatus);
}

function success($data): Json
{
    return json(["success" => true,"status" => 0,"data" => $data],\app\enums\StatusCodeEnum::SUCCESS);
}
function Fail($data): Json
{
    return json(["success" => false,"status" => 1,"statusInfo" => $data],\app\enums\StatusCodeEnum::BAD_REQUEST);
}
// 构建树结构
function buildTree($menuList, $parentId = null) {

    $tree = [];
    foreach ($menuList as $menu) {
        unset($menu['pivot']);
        if ($menu['parentid'] === $parentId) {
            $children = buildTree($menuList, $menu['id']);
            if ($children) {
                $menu['children'] = $children;
            }else{
                $menu['children'] = [];
            }
            $tree[] = $menu;
        }
    }
    return $tree;
}
// 销毁指定的字段
function unsetFields($data,$fields=['pivot','id'])
{
    //判断$data是不是数组类型
    if (is_array($data) && count(array_filter(array_keys($data), 'is_string')) > 0) {
        foreach ($fields as $field){
            unset($data[$field]);
        }
        return $data;
    } else {
        $res = [];
        foreach ($data as $d){
            foreach ($fields as $field){
                unset($d[$field]);

            }
            $res[] = $d;
        }
        return $res;
    }

}

// 七牛云初始化
/**
 * @return array [$uploadManager, $domain, $token]
 *
 */
function initQiNiuYun()
{
    $accessKey = 'K-z0byoZRFwU-k20ArCqG4GuDAteU6AhcdQrdzZt';
    $secretKey = 'lhvXNYrAgg8Tk8niMVcRHIV5GxRVkw7w64zZQ0CS';
    $bucket = 'myvueapp';
    $auth = new Auth($accessKey, $secretKey);
    $bucketManager = new BucketManager($auth);
    $domains = $bucketManager->domains($bucket);
    $uploadManager = new UploadManager();
    $domain = reset($domains)[0];
    $token = $auth->uploadToken($bucket);
    return [$uploadManager, $domain, $token];
}
