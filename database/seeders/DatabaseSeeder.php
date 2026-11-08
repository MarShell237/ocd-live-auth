<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Bovann',
            'email' => 'bovann@example.com',
            'role' => "ADMIN",
            'password' => Hash::make('password')
        ]);

        $this->call(ModeSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(ReservationSeeder::class);
    }
}
