<?php

namespace App\Services;

use App\Models\Pajak;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaxApiService
{
    /**
     * Mock sync from external API.
     * In a real scenario, this would use Http::get('https://api.taxgateway.com/payments')
     */
    public function syncFromExternalApi()
    {
        Log::info("Starting Tax API Synchronization...");

        // Mocking API Response (e.g., 20 new records)
        $mockRecords = $this->generateMockApiData(20);

        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        $results = [
            'created' => 0,
            'updated' => 0,
        ];

        foreach ($mockRecords as $record) {
            // Using updateOrCreate for "Upsert" logic
            $pajak = Pajak::updateOrCreate(
                [
                    'nama_perusahaan' => $record['nama_perusahaan'],
                    'jenis_pajak' => $record['jenis_pajak'],
                    'periode' => $record['periode'],
                ],
                [
                    'tanggal_input' => $record['tanggal_input'],
                    'tanggal_jatuh_tempo' => $record['tanggal_jatuh_tempo'],
                    'status' => $record['status'],
                    'keterangan' => $record['keterangan'] . ' (Synced from API)',
                    'user_id' => $admin->id,
                ]
            );

            if ($pajak->wasRecentlyCreated) {
                $results['created']++;
            } else {
                $results['updated']++;
            }
        }

        // Save last sync time to cache/settings if needed
        cache(['last_tax_sync' => now()->toDateTimeString()], now()->addDays(7));

        Log::info("Tax Sync Completed. Created: {$results['created']}, Updated: {$results['updated']}");

        return $results;
    }

    /**
     * Helper to generate fake API data for demonstration.
     */
    private function generateMockApiData($count)
    {
        $data = [];
        $companies = ['PT Telkom Indonesia', 'PT Bank Mandiri', 'PT Indofood', 'PT Astra International', 'PT GoTo Gojek Tokopedia'];
        $types = ['PPh Pasal 21', 'PPh Pasal 23', 'PPN Masa', 'PBB'];

        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'nama_perusahaan' => $companies[array_rand($companies)],
                'jenis_pajak' => $types[array_rand($types)],
                'periode' => date('F Y', strtotime("-1 month")),
                'tanggal_input' => date('Y-m-d'),
                'tanggal_jatuh_tempo' => date('Y-m-d', strtotime("+15 days")),
                'status' => 'diproses',
                'keterangan' => 'Data otomatis dari Tax Gateway.',
            ];
        }

        return $data;
    }
}
