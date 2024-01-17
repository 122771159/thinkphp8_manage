<?php

namespace app\exception\AppException;

use app\enums\ErrorStatusEnum;
use app\enums\StatusCodeEnum;
use app\exception\BaseException;

class JWTMissException extends BaseException
{
    public $success = false;
    public $status = ErrorStatusEnum::JWT_TOKEN_EXPIRED;
    public $statusInfo = [
        "message" => "Token不能为空",
    ];
    // 这里是我自己设置的一个状态码类，StatusCodeEnum::INTERNAL_SERVER_ERROR = 500
    public $httpStatus = StatusCodeEnum::BAD_REQUEST;
}