<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $packages = [
            'Visibilité',
            'Premium',
            'Essentiel',
        ];

        foreach ($packages as $name) {
            Package::firstOrCreate(['name' => $name]);
        }
    }
}
