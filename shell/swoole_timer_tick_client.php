<?php

/**
 * Class SwooleTimerTickClient
 */
class SwooleTimerTickClient {

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
     * 获取令牌
     * @return bool
     */
    public function get() {
        return $this->redis->rpop($this->queue) ? true : false;
    }

    public function exec() {
        echo 'client is running'.PHP_EOL;

        Swoole\Timer::tick(500, function (int $timer_id) {
            echo "after 500ms.\n";
            $res = $this->get();
            var_dump($res);
        });
    }
}


(new SwooleTimerTickClient())->exec();