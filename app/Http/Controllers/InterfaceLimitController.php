<?php

namespace App\Http\Controllers;

use App\Services\InterfaceLimitService;
use App\Services\SecKillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

/**
 *  接口限流示例
 * Class SecKillController
 * @package App\Http\Controllers
 */
class InterfaceLimitController extends BasicController
{

    private $service;

    public function __construct(
        LoggerInterface $controllerLogger,
        InterfaceLimitService $interfaceLimitService
    )
    {
        parent::__construct($controllerLogger);
        $this->service = $interfaceLimitService;
    }

    /**
     * 接口限流示例
     * php redis 令牌桶算法 swoole毫秒定时器
     *
     */
    public function index() {
        return $this->service->exec();
    }

}
