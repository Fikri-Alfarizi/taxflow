<?php

namespace Database\Factories;

use App\Models\CatatanPerbaikan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatatanPerbaikan>
 */
class CatatanPerbaikanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'catatan_perbaikan' => $this->faker->sentence(),
            'status' => $this->faker->randomElement([
                'belum_diperbaiki',
                'sedang_diperbaiki',
                'selesai_diperbaiki'
            ]),
            'tanggal_catatan' => now(),
            'pajak_id' => \App\Models\Pajak::factory(),
            'created_by' => \App\Models\User::factory(),
        ];
    }

    /**
     * State untuk catatan yang belum diperbaiki
     */
    public function belumDiperbaiki()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'belum_diperbaiki',
                'tanggal_perbaikan' => null,
            ];
        });
    }

    /**
     * State untuk catatan yang sudah diperbaiki
     */
    public function selesaiDiperbaiki()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'selesai_diperbaiki',
                'tanggal_perbaikan' => now(),
            ];
        });
    }
}
