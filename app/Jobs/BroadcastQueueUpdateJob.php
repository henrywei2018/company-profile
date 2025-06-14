<?php

// File: app/Jobs/BroadcastQueueUpdateJob.php

namespace App\Jobs;

use App\Events\ChatQueueUpdated;
use App\Services\ChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastQueueUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    public function __construct()
    {
        $this->onQueue('broadcast');
    }

    public function handle(ChatService $chatService): void
    {
        $metrics = $chatService->getRealTimeMetrics();
        
        broadcast(new ChatQueueUpdated($metrics))->toOthers();
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Queue update broadcast failed: ' . $exception->getMessage());
    }
}