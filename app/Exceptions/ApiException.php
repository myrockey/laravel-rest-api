<?php

namespace App\Exceptions;

use App\Utils\ResultMsgJson;
use Exception;
use Illuminate\Support\Facades\Log;

class ApiException extends Exception
{
    /**
     * 报告异常
     *
     * @return void
     */
    public function report()
    {
        //
        // 判断异常是否需要自定义报告...
        $data = [
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'code' => $this->getCode(),
            'msg' => $this->getMessage(),
        ];
        Log::error('api接口异常',$data);
        //return false;
    }

    /**
     *渲染异常为 HTTP 响应。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(ResultMsgJson::errorReturn("api接口异常，报错原因:".$this->getMessage()));
    }

}