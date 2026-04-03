<?php

namespace App\Console\Commands;

use App\Models\Pajak;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverduePajak extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tax:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pindahkan status pajak yang melewati jatuh tempo secara background agar UI ringan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan jatuh tempo...');
        Log::info('Background Sync [Jatuh Tempo] Started...');

        // Ambil data yang masih 'diproses' tapi sudah lewat deadline
        $overdue = Pajak::where('status', 'diproses')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('Semua data pajak sudah sesuai jadwal.');
            return;
        }

        $count = 0;
        foreach ($overdue as $pajak) {
            /** @var \App\Models\Pajak $pajak */
            $pajak->update(['status' => 'terlambat']);
            $pajak->monitorings()->create([
                'user_id' => null, // Otonom/Sistem
                'status_proses' => 'terlambat',
                'catatan' => 'Status otomatis diubah oleh sistem karena melewati jatuh tempo.',
            ]);
            $count++;
        }

        $this->info("Berhasil memperbarui $count data menjadi Terlambat.");
        Log::info("Background Sync Completed. Updated $count records to overdue.");
    }
}
