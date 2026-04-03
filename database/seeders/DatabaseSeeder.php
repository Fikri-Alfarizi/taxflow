<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@taxflow.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@taxflow.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]
        );

        // Generate 98 more Staff Users for scaling (Total 100)
        echo "Generating 98 additional staff users...\n";
        User::factory()->count(98)->create(['role' => 'staff']);
        $this->call([
            PajakSeeder::class,
        ]);
    }
}
