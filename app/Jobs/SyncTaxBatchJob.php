<?php

namespace App\Jobs;

use App\Services\TaxApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncTaxBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TaxApiService $service): void
    {
        Log::info("Background Sync Started...");
        
        $results = $service->syncFromExternalApi();

        Log::info("Background Sync Completed: " . json_encode($results));
    }
}
