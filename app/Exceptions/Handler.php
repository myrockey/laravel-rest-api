<?php

namespace App\Exceptions;

use App\Utils\ResultMsgJson;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // 异常处理器的 $dontReport 属性包含了一个不会被日志记录的异常类型的数组。
        // 例如，由 404 错误导致的其他类型的错误就不会被写到日志文件中。您可以按需添加其他异常类型到该数组中：
//        \Illuminate\Auth\AuthenticationException::class,
//        \Illuminate\Auth\Access\AuthorizationException::class,
//        \Symfony\Component\HttpKernel\Exception\HttpException::class,
//        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        /*$this->reportable(function (CustomException $e, $request) {

        });*/

        /*$this->renderable(function (CustomException $e, $request) {
            // 默认情况下， Laravel 异常处理器会自动为您转换异常为 HTTP 响应。
            // 当然，您亦可在异常处理器的 renderable 方法中注册一个特定类型的异常的自定义渲染闭包来实现。Laravel 将会根据闭包的类型提示来确定异常的类：
//            return response()->view('errors.invalid-order', [], 500);
        });*/
    }

    /**
     * 获取默认日志的上下文变量
     *
     * @return array
     */
    protected function context()
    {
        // 此后，每一条异常日志信息都将包含这个信息：
        return array_merge(parent::context(), [
            'global' => 'global error',
        ]);
    }

}
