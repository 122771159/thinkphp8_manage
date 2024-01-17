<?php

namespace app\exception\AppException;

use app\enums\StatusCodeEnum;
use app\exception\BaseException;

class AuthNoPermissionException extends BaseException
{
    public $success = false;
    public $status = 1;
    public $statusInfo = [
        "message" => "您没有权限访问此资源",
    ];
    // 这里是我自己设置的一个状态码类，StatusCodeEnum::INTERNAL_SERVER_ERROR = 500
    public $httpStatus = StatusCodeEnum::BAD_REQUEST;
}