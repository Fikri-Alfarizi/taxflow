<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pajak;
use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RandomizeApprovalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:randomize-approval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Randomize approval workflow statuses for development and testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting randomization of approval statuses...');
        
        $pajaks = Pajak::all();
        $admin = User::where('role', 'admin')->first() ?? User::first();
        
        $bar = $this->output->createProgressBar(count($pajaks));
        $bar->start();

        foreach ($pajaks as $pajak) {
            $rand = rand(1, 100);
            
            // Default: All Pending
            $status_v = 'pending';
            $status_va = 'pending';
            $status_a = 'pending';
            $tanggal_v = null;
            $tanggal_va = null;
            $tanggal_a = null;
            $status_utama = $pajak->status;

            if ($rand > 30) { // 70% chance to be at least verified
                $status_v = 'verified';
                $tanggal_v = now()->subDays(rand(1, 10));
                
                if ($rand > 60) { // 40% chance to be validated (if verified)
                    $status_va = 'valid';
                    $tanggal_va = $tanggal_v->copy()->addHours(rand(1, 24));
                    
                    if ($rand > 85) { // 15% chance to be approved (if valid)
                        $status_a = 'approved';
                        $tanggal_a = $tanggal_va->copy()->addHours(rand(1, 24));
                        $status_utama = 'selesai';
                    } elseif ($rand > 80) { // 5% chance to be rejected
                        $status_a = 'rejected';
                        $tanggal_a = $tanggal_va->copy()->addHours(rand(1, 24));
                        $status_utama = 'ditolak';
                    }
                } elseif ($rand > 55) { // 5% chance to be invalid
                    $status_va = 'invalid';
                    $tanggal_va = $tanggal_v->copy()->addHours(rand(1, 24));
                }
            } elseif ($rand > 25) { // 5% chance to be revision
                $status_v = 'needs_revision';
                $tanggal_v = now()->subDays(rand(1, 5));
            }

            // Sync Dokumens status
            if ($pajak->dokumens()->count() > 0) {
                foreach ($pajak->dokumens as $dokumen) {
                    $dok_status = 'pending';
                    $dok_date = null;
                    
                    if ($status_va == 'valid') {
                        $dok_status = 'valid';
                        $dok_date = $tanggal_va;
                    } elseif ($status_va == 'invalid') {
                        $dok_status = rand(0, 1) ? 'valid' : 'invalid';
                        $dok_date = $tanggal_va;
                    }
                    
                    $dokumen->update([
                        'status_validasi' => $dok_status,
                        'validated_by' => ($dok_status != 'pending') ? $admin->id : null,
                        'tanggal_validasi' => $dok_date
                    ]);
                }
            }

            $pajak->update([
                'status_verifikasi' => $status_v,
                'status_validasi' => $status_va,
                'status_approval' => $status_a,
                'verified_by' => ($status_v != 'pending') ? $admin->id : null,
                'approved_by' => ($status_a != 'pending') ? $admin->id : null,
                'tanggal_verifikasi' => $tanggal_v,
                'tanggal_validasi' => $tanggal_va,
                'tanggal_approval' => $tanggal_a,
                'status' => $status_utama
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Randomization complete! Database is now populated with various approval states.');
    }
}
