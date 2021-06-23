<?php

namespace App\Http\Controllers;

use App\Services\SecKillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

/**
 *  秒杀示例
 * Class SecKillController
 * @package App\Http\Controllers
 */
class SecKillController extends BasicController
{

    private $secKillService;

    public function __construct(
        LoggerInterface $controllerLogger,
        SecKillService $secKillService
    )
    {
        parent::__construct($controllerLogger);
        $this->secKillService = $secKillService;
    }

    /**
     * 秒杀示例
     * php 乐观锁（客户端全部可读，当写的时候通过版本标识判断数据是否变更，已变更则不执行）redis watch监听key 事务实现
     *
     */
    public function index() {
        return $this->secKillService->secKill();
    }

    /**
     *  获取列表
     * @return array
     */
    public function getLists() {
        return $this->secKillService->getLists();
    }

    /**
     *  清空数据
     * @return array
     */
    public function del() {
        return $this->secKillService->del();
    }

}
