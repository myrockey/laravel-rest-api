<?php
namespace App\Services;
use App\Utils\ResultMsgJson;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

/**
 *
 * Class TokenBucketService
 * @package App\Services
 */
class InterfaceLimitService extends BaseService{

    protected $redis;

    public function __construct(
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        /** @var  $redis \Predis\Client */
        $redis = Redis::connection();
        $this->redis = $redis;
    }

    /**
     * 模拟执行
     */
    public function exec() {
        $queue = 'myContainer';
        $max = 5;
        $tokenBucket = new TokenBucketService($queue, $max);

        $tokenBucket->reset();

        // 循环获取令牌，令牌桶内只有5个令牌，因此最后3次获取失败
        for($i=0; $i<8; $i++){
            var_dump($tokenBucket->get());
        }

        // 加入10个令牌，最大令牌为5，因此只能加入5个
        $add_num = $tokenBucket->add(10);

        var_dump($add_num);

        // 循环获取令牌，令牌桶内只有5个令牌，因此最后1次获取失败
        for($i=0; $i<7; $i++){
            var_dump($tokenBucket->get());
        }

    }

    /**
     * 投递令牌
     */
    public function add () {
        //投递令牌
        $queue = 'myContainer';
        $max = 5;
        $tokenBucket = new TokenBucketService($queue, $max);

        /*swoole_timer_tick(800, function () use ($token) {
            $tokenBucket->add(1);
        });*/
        \Swoole\Timer::tick(800, function (int $timer_id) use ($tokenBucket) {
            echo "after 800ms.\n";
            $res = $tokenBucket->add(1);
            var_dump($res);
            if ($res) {
                echo 'token is to all';
            } else {
                echo 'token is add ...';
            }
        }, $tokenBucket);

//        // 加入10个令牌，最大令牌为5，因此只能加入5个
//        $add_num = $tokenBucket->add(10);
//
//        var_dump($add_num);
    }

    /**
     * 消费令牌
     */
    public function consume() {
        $queue = 'myContainer';
        $max = 5;
        $tokenBucket = new TokenBucketService($queue, $max);

        /*swoole_timer_tick(500, function () use ($token) {
            $tokenBucket->get();
        });*/
        \Swoole\Timer::tick(500, function (int $timer_id) use($tokenBucket) {
            echo "after 500ms.\n";
            $res = $tokenBucket->get();
            var_dump($res);
        }, $tokenBucket);


        // 循环获取令牌，令牌桶内只有5个令牌，因此最后1次获取失败
//        for($i=0; $i<7; $i++){
//            var_dump($tokenBucket->get());
//        }
    }
}
