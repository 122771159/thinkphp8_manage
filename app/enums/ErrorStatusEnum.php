<?php

namespace app\enums;

class ErrorStatusEnum
{
    const JWT_TOKEN_EXPIRED = -10010;
    // jwt 失效
    const JWT_TOKEN_INVALID = -10011;
    // jwt 非法
    const JWT_TOKEN_ILLEGAL = -10012;

    // 用于获取所有状态码的数组
//    public static function getAllStatusCodes(): array
//    {
//        return [
//            self::SUCCESS,
//            self::REDIRECT,
//            self::BAD_REQUEST,
//            self::UNAUTHORIZED,
//            self::FORBIDDEN,
//            self::NOT_FOUND,
//            self::INTERNAL_SERVER_ERROR,
//            self::NOT_IMPLEMENTED,
//            self::BAD_GATEWAY,
//            self::SERVICE_UNAVAILABLE,
//            self::CUSTOM_CODE_1,
//            self::CUSTOM_CODE_2,
//        ];
//    }
}