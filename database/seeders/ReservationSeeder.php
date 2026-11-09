<?php

namespace Database\Seeders;

use App\Models\Mode;
use App\Models\Package;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ModeSeeder::class,
            PackageSeeder::class,
        ]);

        if (Reservation::query()->exists()) {
            return;
        }

        $modeIds = Mode::query()->pluck('id');
        $packageIds = Package::query()->pluck('id');

        Reservation::factory()
            ->count(130)
            ->create([
                'mode_id' => fn (): int => $modeIds->random(),
                'package_id' => fn (): int => $packageIds->random(),
            ]);
    }
}
