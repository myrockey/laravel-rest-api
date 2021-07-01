<?php

namespace App\Http\Controllers;

use App\Events\ActionLog;
use App\Exceptions\ApiException;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Jobs\TestQueueJob;
use App\Models\User;
use App\Utils\ResultMsgJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class TestController extends BasicController
{
    //
    public function index(Request $request) {
        event(new ActionLog('用户1', '1', '测试监听事件开始'));
        echo "index is run";
        event(new ActionLog('用户1', '1', '测试监听事件结束'));
    }

    /**
     * 测试队列
     */
    public function testQueue(Request $request) {

        $userId = $request->get('id',0);
        $userInfo = User::find($userId);
        //var_dump($userInfo);die;
        TestQueueJob::dispatch($userInfo);

        // 如果你希望有条件地分派任务，可以使用 dispatchIf 和 dispatchUnless 方法:
        //TestQueueJob::dispatchIf($userInfo ? true : false, $userInfo);
        //TestQueueJob::dispatchUnless($userInfo ? true : false, $userInfo);

        // 如果你希望有条件地执行队列任务，可以在分发任务时使用 delay 方法 。例如，让我们指定调度任务在 10 分钟后他被调度后才执行，在这之前它将是无效的：
        //TestQueueJob::dispatch($userInfo)->delay(now()->addMinutes(10));

        // 响应发送到浏览器后的调度
        //TestQueueJob::dispatchAfterResponse();
        // 你可以 dispatch 一个闭包，并将 afterResponse 方法链到帮助程序上，在响应发送到浏览器后执行闭包：
        //dispatch(function () {Mail::to('taylor@laravel.com')->send(new WelcomeMessage);})->afterResponse();

        // 如果您想要立即 (同步地) 调度任务，您可以使用 dispatchSync 方法。当使用此方法时，任务将不会排队，并将立即运行在当前进程：
        //TestQueueJob::dispatchSync($userInfo);

        // 任务链允许您指定一组应在主任务成功执行后按顺序运行的排队任务。
        //如果序列中的一个任务失败，其余的任务将不会运行。要执行一个排队的任务链，你可以使用 Bus facade 提供的 chain 方法：
        /*Bus::chain([
            new TestQueueJob($userInfo),
            new OptimizePodcast,
            new ReleasePodcast,
        ])->dispatch();*/
        // 除了链接作业任务实例，你还可以链接闭包：
        /*Bus::chain([
            new TestQueueJob($userInfo),
            new OptimizePodcast,
            function () {
                Podcast::update(...);
            },
        ])->dispatch();*/

        // 链式连接 & 队列
        // 如果你想指定应该用于已连接任务的默认连接和队列，可以使用 allOnConnection 和 allOnQueue 方法。
        //这些方法指定了应该使用的队列连接和队列名称，除非队列任务被显式地分配了一个不同的连接 / 队列:
        //TODO: 注意：allOnConnection 有问题
        /*Bus::chain([
            new TestQueueJob($userInfo),
            //new OptimizePodcast,
            //new ReleasePodcast,
        ])->catch(function (\Throwable $e) {
            // 链式中的作业失败...
            return ResultMsgJson::errorReturn($e->getMessage());
        })->dispatch()->allOnConnection('database')->allOnQueue('default');*/


        // 自定义队列和连接
        // 调度到特定队列 processing
        TestQueueJob::dispatch($userInfo)->onQueue('processing');
        // 发送到特定连接 database
        TestQueueJob::dispatch($userInfo)->onConnection('database');

        return ResultMsgJson::successReturn();
    }


    public function testMiddleware (Request $request) {

        return ResultMsgJson::successReturn();
    }

    public function testCsrf (Request $request) {

        return ResultMsgJson::successReturn();
    }

    /**
     *  #自定义 HTTP 错误页面
        #Laravel 创建了可以轻松显示各种 HTTP 状态码的自定义错误页面。例如，如果您想要自定义 HTTP 状态码为 404 的错误页面，您只需创建 resources/views/errors/404.blade.php 文件。该文件用于您的应用产生的所有 404 错误。
        #该目录中的视图应与它们所对应的 HTTP 状态码。由 abort 函数引发的 HttpException 实例将作为 exception 变量传递给视图：
        <h2>{{ $exception->getMessage() }}</h2>
        #您可以使用 vendor:publish Artisan 命令发布 Laravel 错误页面模板。一旦您发布了模板，您就可以像下面这样自定义它们：
        php artisan vendor:publish --tag=laravel-errors
     */
    public function testError (Request $request) {

        // HTTP 异常 abort抛出异常
//        abort(403, 'Unauthorized action.');
        throw new ApiException(654321);

            try {
                // 校验值...
                throw new \Exception(123456);
            } catch (\Throwable $e) {
                //report($e); // 有时，您可能需要报告异常，但不终止当前请求的处理。report 辅助函数允许您在不渲染错误页面的情况下快速报告异常：

                return ResultMsgJson::errorReturn();
            }

        return ResultMsgJson::successReturn();
    }

}
