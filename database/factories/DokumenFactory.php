<?php

namespace Database\Factories;

use App\Models\Dokumen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dokumen>
 */
class DokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_dokumen' => $this->faker->randomElement([
                'Bukti Potong PPh 21',
                'Faktur Pajak',
                'Laporan Keuangan',
                'NPWP Perusahaan',
                'Surat Keterangan Fiskal'
            ]),
            'file_dokumen' => $this->faker->filePath() . '.pdf',
            'ukuran_file' => $this->faker->numberBetween(100000, 5000000), // 100KB - 5MB
            'tanggal_upload' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'pajak_id' => \App\Models\Pajak::factory(),

            // Validation fields
            'status_validasi' => 'pending',
            'tipe_file' => 'pdf',
        ];
    }

    /**
     * State untuk dokumen yang sudah divalidasi valid
     */
    public function valid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_validasi' => 'valid',
                'validated_by' => \App\Models\User::factory(),
                'tanggal_validasi' => now(),
            ];
        });
    }

    /**
     * State untuk dokumen yang sudah divalidasi invalid
     */
    public function invalid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_validasi' => 'invalid',
                'validated_by' => \App\Models\User::factory(),
                'tanggal_validasi' => now(),
                'keterangan_validasi' => $this->faker->sentence(),
            ];
        });
    }
}
