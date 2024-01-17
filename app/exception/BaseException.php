<?php

namespace app\exception;

use app\enums\StatusCodeEnum;

class BaseException extends \Exception
{
    public $success = false;
    public $status = 1;
    public $statusInfo = [
        "message" => "服务器正忙",
    ];
    // 这里是我自己设置的一个状态码类，StatusCodeEnum::INTERNAL_SERVER_ERROR = 500
    public $httpStatus = StatusCodeEnum::INTERNAL_SERVER_ERROR;
    public function __construct($message=null, $code=1, $httpStatus=500)
    {


        if(isset($message)){
            $this->statusInfo = [
                "message" => $message,
            ];
        }
        if(isset($code)){
            if (!is_numeric($code)) {
                $code = 1;
            }
            $this->status = $code;
        }
        if(isset($httpStatus)){
            if (!is_numeric($httpStatus)) {
                $httpStatus = 500;
            }
            $this->httpStatus = $httpStatus;
        }



        parent::__construct($message==null?$this->statusInfo['message']:$message, $httpStatus);
    }
//    public function getError()
//    {
//        return ;
//    }
}