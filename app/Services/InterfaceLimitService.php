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
}