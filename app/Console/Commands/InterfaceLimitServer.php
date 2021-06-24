<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InterfaceLimitService;
use Psr\Log\LoggerInterface;

class InterfaceLimitServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:InterfaceLimitServer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is to  create add token to tokenBucket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        LoggerInterface $loggerInterface,
        InterfaceLimitService $interfaceLimitService)
    {
        parent::__construct();
        $this->logger = $loggerInterface;
        $this->interfaceLimitService = $interfaceLimitService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->interfaceLimitService->add();
        $this->logger->info($this->signature ." exec");
        return 0;
    }
}
