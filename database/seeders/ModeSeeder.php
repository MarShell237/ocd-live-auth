<?php

namespace Database\Seeders;

use App\Models\Mode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $modes = [
            'En présentiel',
            'En ligne',
        ];

        foreach ($modes as $name) {
            Mode::firstOrCreate(['name' => $name]);
        }
    }
}
