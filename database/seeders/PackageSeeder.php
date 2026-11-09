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
            [
                'name' => 'Visibilité',
                'description' => 'Votre premiere presentation en direct',
                'price' => 15000,
                'duration' => 15,
                'offers' => ['presentation pendant un direct tiktok', 'confirmation par email']
            ],
            [
                'name' => 'Premium',
                'description' => 'Plus de temps pour convaincre votre audience',
                'price' => 25000,
                'duration' => 30,
                'offers' => ['presentation pendant un direct tiktok', 'Temps de presentation prolonger', 'confirmation par email']            
            ],
            [
                'name' => 'Essentiel',
                'description' => 'Visibilite maximale, pendant et apres le direct',
                'price' => 30000,
                'duration' => 30,
                'offers' => ['presentation pendant un direct tiktok', 'Temps de presentation prolonger', 'Rediffusion sur le compte TikTok de Bouvan', 'confirmation par email']            
            ]
        ];

        foreach ($packages as $p) {
            Package::firstOrCreate([
                'mode_id' => 1,
                'name' => $p['name'],
                'description' => $p['description'],
                'price' => $p['price'],
                'duration' => $p['duration'],
                'offers' => $p['offers']
            ]);
        }
        foreach ($packages as $p) {
            Package::firstOrCreate([
                'mode_id' => 2,
                'name' => $p['name'],
                'description' => $p['description'],
                'price' => $p['price'],
                'duration' => $p['duration'],
                'offers' => $p['offers']
            ]);
        }
    }
}
