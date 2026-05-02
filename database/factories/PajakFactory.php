<?php

namespace Database\Factories;

use App\Models\Pajak;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pajak>
 */
class PajakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_perusahaan' => $this->faker->company(),
            'npwp' => $this->faker->numerify('##.###.###.#-###.###'),
            'alamat_lengkap' => $this->faker->address(),
            'nama_pic' => $this->faker->name(),
            'kontak_pic' => $this->faker->phoneNumber(),
            'email_pic' => $this->faker->email(),
            'kpp_pratama' => 'KPP ' . $this->faker->city(),
            'kategori_usaha' => $this->faker->randomElement(['Perbankan', 'Telekomunikasi', 'Manufaktur', 'Teknologi']),
            'id_transaksi_source' => 'TX-' . strtoupper($this->faker->bothify('????????')),
            'logo_url' => $this->faker->imageUrl(100, 100, 'business'),
            'jenis_pajak' => $this->faker->randomElement(['PPh Pasal 21', 'PPh Pasal 23', 'PPN', 'PBB']),
            'periode' => $this->faker->date('M Y'),
            'tanggal_input' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'tanggal_jatuh_tempo' => $this->faker->dateTimeBetween('now', '+3 months'),
            'status' => $this->faker->randomElement(['diproses', 'selesai', 'terlambat']),
            'keterangan' => $this->faker->sentence(),
            'user_id' => \App\Models\User::factory(),

            // Approval workflow fields
            'status_verifikasi' => 'pending',
            'status_validasi' => 'pending',
            'status_approval' => 'pending',
        ];
    }

    /**
     * State untuk pajak yang sudah diverifikasi
     */
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_verifikasi' => 'verified',
                'verified_by' => \App\Models\User::factory(),
                'tanggal_verifikasi' => now(),
            ];
        });
    }

    /**
     * State untuk pajak yang sudah divalidasi
     */
    public function validated()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_verifikasi' => 'verified',
                'status_validasi' => 'valid',
                'verified_by' => \App\Models\User::factory(),
                'tanggal_verifikasi' => now(),
            ];
        });
    }

    /**
     * State untuk pajak yang sudah diapprove
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_verifikasi' => 'verified',
                'status_validasi' => 'valid',
                'status_approval' => 'approved',
                'status' => 'selesai',
                'verified_by' => \App\Models\User::factory(),
                'approved_by' => \App\Models\User::factory(),
                'tanggal_verifikasi' => now(),
                'tanggal_approval' => now(),
            ];
        });
    }
}
