<?php

namespace Database\Seeders;

use App\Models\Pajak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PajakSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users for distribution
        $userIds = User::pluck('id')->toArray();
        $apiService = new \App\Services\TaxApiService();

        $companies = [
            ['name' => 'PT Bank Mandiri', 'logo' => '/logos/mandiri.png', 'kpp' => 'KPP Wajib Pajak Besar Satu'],
            ['name' => 'PT Telkom Indonesia', 'logo' => '/logos/telkom.png', 'kpp' => 'KPP Wajib Pajak Besar Empat'],
            ['name' => 'PT Astra International', 'logo' => '/logos/astra.png', 'kpp' => 'KPP Perusahaan Masuk Bursa'],
            ['name' => 'PT Indofood', 'logo' => '/logos/Indofood.png', 'kpp' => 'KPP Pratama Gambir'],
            ['name' => 'PT GoTo Gojek Tokopedia', 'logo' => '/logos/goto.png', 'kpp' => 'KPP Madya Jakarta Selatan'],
            ['name' => 'PT Pertamina', 'logo' => '/logos/pertamina.jpg', 'kpp' => 'KPP Wajib Pajak Besar Dua'],
            ['name' => 'PT Unilever Indonesia', 'logo' => '/logos/unilever.jpg', 'kpp' => 'KPP Madya Tangerang'],
            ['name' => 'PT Bank Central Asia', 'logo' => '/logos/bca.png', 'kpp' => 'KPP Wajib Pajak Besar Tiga'],
        ];

        $taxTypes = ['PPh Pasal 21', 'PPh Pasal 23', 'PPN', 'PBB', 'PPh Final'];
        $statuses = ['diproses', 'selesai', 'terlambat'];
        $categories = ['Perbankan', 'Telekomunikasi', 'Manufaktur', 'Teknologi', 'Energi', 'FMCG'];

        echo "Seeding 2500+ records distributed among 100 users with Digital Assets...\n";

        // Performance: Chunk the inserts for speed
        $data = [];
        for ($i = 1; $i <= 2500; $i++) {
            $company = $companies[array_rand($companies)];
            $status = $statuses[array_rand($statuses)];
            $assignedUserId = $userIds[array_rand($userIds)];
            
            // Random deadline: some past, some today, some future
            $deadline = now()->addDays(rand(-30, 60));
            
            $pajakId = DB::table('pajaks')->insertGetId([
                'nama_perusahaan' => $company['name'],
                'npwp' => rand(10, 99) . '.' . rand(100, 999) . '.' . rand(100, 999) . '.' . rand(0, 9) . '-' . rand(100, 999) . '.000',
                'alamat_lengkap' => 'Jl. Jenderal Sudirman No. ' . rand(1, 100) . ', Jakarta Pusat, DKI Jakarta',
                'nama_pic' => 'Petugas ' . Str::random(5),
                'kontak_pic' => '08' . rand(111111111, 999999999),
                'email_pic' => 'pic.' . strtolower(Str::random(5)) . '@' . strtolower(str_replace(' ', '', $company['name'])) . '.com',
                'kpp_pratama' => $company['kpp'],
                'kategori_usaha' => $categories[array_rand($categories)],
                'id_transaksi_source' => 'TX-' . strtoupper(Str::random(8)),
                'logo_url' => $company['logo'],
                'jenis_pajak' => $taxTypes[array_rand($taxTypes)],
                'periode' => date('F Y', strtotime("-" . rand(0, 5) . " month")),
                'tanggal_input' => now(),
                'tanggal_jatuh_tempo' => $deadline,
                'status' => $status,
                'user_id' => $assignedUserId,
                // NEW: Approval workflow fields (sesuai dokumentasi)
                'status_verifikasi' => 'pending',
                'status_validasi' => 'pending',
                'status_approval' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed 1-3 documents for every 2nd tax record
            if ($i % 2 == 0) {
                $docs = $apiService->generateMockDocuments($pajakId, rand(1, 3));
                // Update documents with new validation fields
                foreach ($docs as &$doc) {
                    $doc['status_validasi'] = 'pending';
                    $doc['tipe_file'] = 'pdf'; // Default to PDF
                }
                DB::table('dokumens')->insert($docs);
            }

            // [NEW] Seed 1-2 initial monitoring logs per record
            DB::table('monitorings')->insert([
                'pajak_id' => $pajakId,
                'user_id' => $assignedUserId,
                'status_proses' => $status,
                'catatan' => 'Sistem automasi: Data pertama (' . $status . ') untuk entitas ' . $company['name'] . '.',
                'tanggal_update' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($i % 500 == 0) {
                echo "Processed " . ($i) . " entities with their digital assets...\n";
            }
        }

        echo "Seeding completed successfully.\n";
    }
}
