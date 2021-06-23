<?php
namespace App\Services;
use App\Utils\ResultMsgJson;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

/**
 * 令牌桶算法
 * Class TokenBucketService
 * @package App\Services
 */
class TokenBucketService {

    protected $redis; // redis实例
    protected $max; // 最大令牌数 服务器最大承受请求数，需要知道服务器性能
    protected $queue; // 令牌桶队列

    public function __construct(
        $queue, $max
    ) {
        /** @var  $redis \Predis\Client */
        $redis = Redis::connection();
        $this->redis = $redis;
        $this->queue = $queue;
        $this->max = $max;
    }


    /**
     * 加入令牌
     * @param $num
     * @return array
     */
    public function add($num) {
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

    /**
     * 获取令牌
     * @return bool
     */
    public function get() {
        return $this->redis->rpop($this->queue) ? true : false;
    }

    /**
     * 重设令牌桶，填满令牌
     */
    public function reset() {
        $this->redis->del($this->queue);
        $this->add($this->max);
    }
}