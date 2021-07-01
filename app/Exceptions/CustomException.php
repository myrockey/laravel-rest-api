<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
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

        //return false;
    }

    /**
     * 渲染异常为 HTTP 响应
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(...);
    }
}