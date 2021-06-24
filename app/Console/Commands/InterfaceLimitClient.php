<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InterfaceLimitService;
use Psr\Log\LoggerInterface;

class InterfaceLimitClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:InterfaceLimitClient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command InterfaceLimitClient to consume token';

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
        $this->interfaceLimitService->consume();
        $this->logger->info($this->signature ." exec");
        return 0;
    }
}
