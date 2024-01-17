<?php

namespace app\controller;

use app\Request;


class Demo
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        list($uploadManager,$domain,$token) = initQiNiuYun();
        list($ret, $err) = $uploadManager->putFile($token, null, $file->getRealPath());
        $fileUrl = $domain . '/' . $ret['key'];
        if ($err !== null) {
            // 文件上传失败
            return $err->message();
        } else {
            // 文件上传成功，返回七牛云的文件信息
            return json($fileUrl);
        }

    }
}