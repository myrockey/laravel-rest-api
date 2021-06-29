<?php

namespace App\Jobs;

use App\Jobs\Middleware\RateLimited;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 队列任务
 * Class TestQueueJob
 * @package App\Jobs
 */
class TestQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务尝试次数
     *
     * @var int
     */
    public $tries = 25;

    /**
     * 任务失败前允许的最大异常数
     *
     * @var int
     */
    public $maxExceptions = 3;

    // 注意：必须安装 pcntl PHP 扩展名才能指定任务超时。
    /**
     * 在超时之前任务可以运行的秒数
     *
     * @var int
     */
    //public $timeout = 120;

    /**
     * 如果任务的模型不再存在，则删除该任务
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    protected $user;

    /**
     * Create a new job instance.
     * TestQueueJob constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        // 因为要加载的 Model 关联关系也会被序列化，导致序列化的任务字符串可能会变得非常大。为了防止关系被序列化，您可以在设置属性值时调用模型上的 withoutRelations 方法。这个方法会返回一个没有加载关系的模型实例：
        $this->user = $user->withoutRelations();
    }

    /**
     * 获取一个可以被传递通过的中间件任务
     *
     * @return array
     */
    public function middleware()
    {
        return [new RateLimited];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::info(json_encode($this->user));
    }

    /**
     * 任务未能处理
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // 给用户发送失败通知, 等等...
        Log::error('TestQueueJob failed :'.$exception->getMessage());
    }
}
