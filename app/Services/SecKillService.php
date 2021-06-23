<?php
namespace App\Services;
use App\Utils\ResultMsgJson;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class SecKillService extends BaseService {

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
     * php 乐观锁（客户端全部可读，当写的时候通过版本标识判断数据是否变更，已变更则不执行）
     * redis watch监听key 事务实现 如果key变化，则exec执行失败
     */
    public function secKill() {
        $watchKey = 'count';
        $total = 10;

        // watch 监听key
        $this->redis->watch($watchKey);
        $count = $this->redis->get($watchKey);
        if ($count < $total) {
            // redis 事务 watch监听key的变化，当key被修改
            $this->redis->multi();

            //设置延迟，方便测试效果。
            sleep(3);

            // 自增1
            $this->redis->incr($watchKey);
            $res = $this->redis->exec();

            if ($res) {
                // 将用户先写入队列，后异步执行
                $this->redis->hset('secKillUserHash', 'userId_'.rand(1,9999), time());
                //echo '恭喜你，抢购成功! 第 '.($count+1).' 件商品';
                return ResultMsgJson::successReturn([$count+1]);
            } else {
                return ResultMsgJson::errorReturn("手气不好，再抢购!");
            }

        } else {
            return ResultMsgJson::errorReturn('很遗憾，已售罄!');
        }
    }

    public function getLists() {
        $this->logger->info("getLists ");
        $res = $this->redis->hgetall('secKillUserHash');

        //dd($res);

        // TODO: 执行下单购买操作
        $return = [];
        foreach ($res as $key=> $value) {
            $return[] = [$key=>$value];
        }

        return ResultMsgJson::successReturn($return);
    }

    /**
     * 删除key
     */
    public function del() {
        $this->redis->del('count');
        $this->redis->del('secKillUserList');
        $this->redis->del('secKillUserHash');

        return ResultMsgJson::successReturn();
    }

}