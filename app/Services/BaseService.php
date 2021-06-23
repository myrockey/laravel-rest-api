<?php

namespace App\Services;

use Psr\Log\LoggerInterface;


abstract class BaseService
{
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
}