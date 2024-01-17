<?php

namespace app\enums;

class StatusCodeEnum
{


    const SUCCESS = 200;

    const REDIRECT = 301;

    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;

    const NOT_FOUND = 404;

    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;

    const CUSTOM_CODE_1 = 600;
    const CUSTOM_CODE_2 = 601;
}