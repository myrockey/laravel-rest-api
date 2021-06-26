<?php

/**
 * Class SwooleTimerTickServer
 */
class SwooleTimerTickServer {

    protected $redis;
    protected $config;
    protected $queue = 'myContainer';
    protected $max = 5;

    public function __construct($config = [
        'host'=>'127.0.0.1',
        'port'=>'6379',
        'timeout'=>'30',
    ])
    {
        $this->redis = new \Redis();
        $res = $this->redis->connect($config['host'],$config['port'],$config['timeout']);
        if (!$res) {
            echo 'redis connect failed';
        } else {
            echo 'redis connect success';
        }

    }


    /**
     * 加入令牌
     * @param $num
     * @return array
     */
    protected function add($num) {
        // 当前令牌数
        $curNum = $this->redis->llen($this->queue);

        // 最大令牌数
        $maxNum = $this->max;

        // 计算最大可加入令牌数，不能超过最大令牌数
        $addNum = $maxNum >= $curNum+$num ? $num : $maxNum-$curNum;

        // 加入令牌
        if ($addNum > 0) {
            $token = array_fill(0, $addNum, 1);
            $this->redis->lpush($this->queue, ...$token);
            return $addNum;
        } else {
            return 0;
        }
    }

    public function exec() {
        echo 'server is running'.PHP_EOL;

        Swoole\Timer::tick(800, function (int $timer_id) {
            echo "after 800ms.\n";
            $res = $this->add(1);
            if ($res) {
                echo 'token is to all';
            }
        });

       /* swoole_timer_tick(800, function (){
           $this->add(1);
        });*/
    }
}


(new SwooleTimerTickServer())->exec();