<?php
namespace App\Console;

use Illuminate\Support\Facades\Log;

class RecordLogs {

    public function __invoke() {
        Log::error('test schedule record logs');
    }
}