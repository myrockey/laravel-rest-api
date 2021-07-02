<?php

namespace App\Console;

use App\Jobs\TestQueueJob;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * 定时任务配置
     * 命令： * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // 除了调用闭包这种方式来调度外，你还可以调用 可调用对象 。可调用对象是简单的 PHP 类，包含一个 __invoke 方法：
        $schedule->call(new RecordLogs)->everyMinute()->description('testSchedule')->withoutOverlapping();
        // command命令
        $schedule->command('command:InterfaceLimitServer')->everyMinute()->description('InterfaceLimitServer')->withoutOverlapping();
        $schedule->command('command:InterfaceLimitClient')->everyTwoMinutes()->description('InterfaceLimitClient')->withoutOverlapping();
        // 队列任务
        //  分发任务到「REDIS_QUEUE」队列及「redis」连接...
        $schedule->job(new TestQueueJob(User::find(1)), env('REDIS_QUEUE'), 'redis')->everyFiveMinutes()->description('TestQueueJob')->withoutOverlapping();
        // shell命令调度
        $schedule->exec("echo ".rand(1,1000))->everyMinute()->description('TestShell')->withoutOverlapping()
            ->appendOutputTo(base_path('storage/logs/test123.log'), true);
        // 在每周一 13:00 执行...
//        $schedule->call(function () {
//            //
//        })->weekly()->mondays()->at('13:00');
//
//        // 在每个工作日 8:00 到 17:00 之间的每小时周期执行...
//        $schedule->command('foo')
//            ->weekdays()
//            ->hourly()
//            ->timezone('America/Chicago')
//            ->between('8:00', '17:00');
//        //  方法可以用于限制任务在每周的指定日期执行。举个例子，您可以在让一个命令每周日和每周三每小时执行一
//        $schedule->command('emails:send')
//            ->hourly()
//            ->days([0, 3]);
//
//        // 不仅如此，你还可以使用 Illuminate\Console\Scheduling\Schedule 类中的常量来设置任务在指定日期运行：
//        $schedule->command('emails:send')
//            ->hourly()
//            ->days([Schedule::SUNDAY, Schedule::WEDNESDAY]);
//        //
//        $schedule->command('emails:send')
//            ->hourly()
//            ->unlessBetween('23:00', '4:00');
//        // 环境限制
//        $schedule->command('emails:send')
//            ->daily()
//            ->environments(['staging', 'production']);
//        // 条件限制 when 方法可根据闭包返回结果来执行任务。换言之，若给定的闭包返回 true，若无其他限制条件阻止，任务就会一直执行：
//        $schedule->command('emails:send')->daily()->when(function () {
//            return true;
//        });
//        // skip 可看作是 when 的逆方法。若 skip 方法返回 true，任务将不会执行：
//        $schedule->command('emails:send')->daily()->skip(function () {
//            return true;
//        });
//        // 任务只运行在一台服务器上
//        $schedule->command('report:generate')
//            ->fridays()
//            ->at('17:00')
//            ->onOneServer();
//        // 默认情况下，计划同时运行的多个任务将根据它们在 schedule 方法中定义的顺序执行。若你有长期运行的任务，这可能导致后续任务比预期时间更晚启动。如果你想在后台运行任务，以便它们可以同时运行，则可以使用 runInBackground 方法：
//        $schedule->command('analytics:report')
//            ->daily()
//            ->runInBackground();
//        // 当应用处于 维护模式 时，Laravel 的队列任务将不会运行。因为我们不想调度任务干扰到服务器上可能还未完成的维护项目。不过，如果你想强制任务在维护模式下运行，你可以使用 evenInMaintenanceMode 方法：
//        $schedule->command('emails:send')->evenInMaintenanceMode();
//
//        // 输出到文件
//        $schedule->command('emails:send')
//            ->daily()
//            ->sendOutputTo($filePath);
//        // 追加到文件
//        $schedule->command('emails:send')
//            ->daily()
//            ->appendOutputTo($filePath);
//        // 发送到邮箱
//        $schedule->command('report:generate')
//            ->daily()
//            ->sendOutputTo($filePath)
//            ->emailOutputTo('taylor@example.com');
//        // 失败时，发送到邮箱
//        $schedule->command('report:generate')
//            ->daily()
//            ->emailOutputOnFailure('taylor@example.com');
//        // 使用 before 和 after 方法，你可以决定在调度任务执行前或者执行后来运行代码：
//        $schedule->command('emails:send')
//            ->daily()
//            ->before(function () {
//                // 任务即将开始...
//            })
//            ->after(function () {
//                // 任务完成...
//            });
//        // 使用 onSuccess 和 onFailure 方法，你可以决定在调度任务成功或者失败运行代码。失败表示 Artisan 或系统命令以非零退出码终止：
//        $schedule->command('emails:send')
//            ->daily()
//            ->onSuccess(function () {
//                // 任务成功...
//            })
//            ->onFailure(function () {
//                // 任务失败...
//            });

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
