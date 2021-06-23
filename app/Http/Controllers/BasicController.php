<?php

namespace App\Http\Controllers;

use Psr\Log\LoggerInterface;

class BasicController extends Controller
{
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }
}
