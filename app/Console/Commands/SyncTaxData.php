<?php

namespace App\Console\Commands;

use App\Services\TaxApiService;
use Illuminate\Console\Command;

class SyncTaxData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tax:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize tax data from external API/Gateway';

    /**
     * Execute the console command.
     */
    public function handle(TaxApiService $service)
    {
        $this->info('Starting Tax Data Synchronization...');
        
        $results = $service->syncFromExternalApi();
        
        $this->table(
            ['Created', 'Updated'],
            [[$results['created'], $results['updated']]]
        );

        $this->info('Tax data synced successfully!');
    }
}
