<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 1、 event目录 先定义事件类：ActionLog OR 命令执行： php artisan make:event ActionLog
 * 2、 在app/Providers下配置
 * protected $listen = [
        //...
        // 注册事件和监听器
        ActionLog::class => [
        TestEventActionLogListener::class,
        ],
        //...
    ];
 * 3、定义监听事件类：命令执行：php artisan make:listener TestEventActionLogListener
 * 4、触发事件： event(new ActionLog('用户1', '1', '测试监听事件开始'));
 * Class ActionLog
 * @package App\Events
 */
class ActionLog
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $type;
    public $msg;

    /**
     * Create a new event instance.
     * ActionLog constructor.
     * @param $user
     * @param $type
     * @param $msg
     */
    public function __construct($user, $type, $msg)
    {
        //
        $this->user = $user;
        $this->type = $type;
        $this->msg = $msg;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
