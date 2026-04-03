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
        // Get an admin user for attribution
        $user = User::first() ?? User::factory()->create();

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

        echo "Seeding 2500+ records with REAL corporate logos and DETAILED entity DNA...\n";

        // Performance: Chunk the inserts for speed
        $data = [];
        for ($i = 1; $i <= 2500; $i++) {
            $company = $companies[array_rand($companies)];
            $status = $statuses[array_rand($statuses)];
            
            // Random deadline: some past, some today, some future
            $deadline = now()->addDays(rand(-30, 60));
            
            $data[] = [
                'nama_perusahaan' => $company['name'],
                'npwp' => rand(10, 99) . '.' . rand(100, 999) . '.' . rand(100, 999) . '.' . rand(0, 9) . '-' . rand(100, 999) . '.000',
                'alamat_lengkap' => 'Jl. Jenderal Sudirman No. ' . rand(1, 100) . ', Jakarta Pusat, DKI Jakarta',
                'nama_pic' => 'Budi ' . Str::random(5),
                'kontak_pic' => '08' . rand(111111111, 999999999),
                'email_pic' => 'pic.' . strtolower(Str::random(5)) . '@' . strtolower(str_replace(' ', '', $company['name'])) . '.com',
                'kpp_pratama' => $company['kpp'],
                'kategori_usaha' => $categories[array_rand($categories)],
                'id_transaksi_source' => 'TX-' . strtoupper(Str::random(8)),
                'logo_url' => $company['logo'],
                'jenis_pajak' => $taxTypes[array_rand($taxTypes)],
                'periode' => 'March 2026',
                'tanggal_input' => now(),
                'tanggal_jatuh_tempo' => $deadline,
                'status' => $status,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 500) {
                DB::table('pajaks')->insert($data);
                $data = [];
                echo "Inserted " . ($i) . " records...\n";
            }
        }

        if (!empty($data)) {
            DB::table('pajaks')->insert($data);
        }

        echo "Seeding completed successfully.\n";
    }
}
