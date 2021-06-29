<?php

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Redis;

/**
 * 任务中间件
 * Class RateLimited
 * @package App\Http\Middleware
 */
class RateLimited
{
    /**
     * 让队列任务慢慢执行
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        Redis::throttle('key')
            ->block(0)->allow(1)->every(5)
            ->then(function () use ($job, $next) {
                // 获取锁 ...

                $next($job);
            }, function () use ($job) {
                // 无法获取锁 ...

                $job->release(5);
            });

        // 例如，使用 throttle 方法，您可以将给定类型的作业限制为每 60 秒只运行 10 次。如果无法获得锁，通常应将任务释放回队列，以便稍后重试：
        //注意： 将一个已被限流的任务释放回队列仍然会增加该任务的 attempts 的总数。
        /*Redis::throttle('key')->allow(10)->every(60)->then(function ($job, $next) {
            // 任务逻辑...
            $next($job);
        }, function ($job) {
            // 无法获得锁...

            return $job->release(10);
        });*/

        // 你可以指定可以同时处理给定任务的 worker 的最大数量。当队列作业正在修改一个每次只能修改一个任务的资源时，这是很有用的。
        //例如，使用 funnel 方法，你可以限制一个给定类型的任务一次只能由一个 worker 处理：
        /*Redis::funnel('key')->limit(1)->then(function ($job, $next) {
            // 任务逻辑...
            $next($job);
        }, function ($job) {
            // 无法获得锁...

            return $job->release(10);
        });*/

    }
}
