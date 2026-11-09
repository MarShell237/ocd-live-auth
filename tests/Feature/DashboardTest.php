<?php

use App\Models\Mode;
use App\Models\Package;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
});

test('the dashboard displays the reservation statistics and recent reservations', function () {
    $this->travelTo(now()->setTime(12, 0));

    $user = User::factory()->create();
    $mode = Mode::factory()->create();
    $package = Package::factory()->create(['name' => 'Premium']);

    $createReservation = fn (array $attributes) => Reservation::factory()->create([
        'mode_id' => $mode->id,
        'package_id' => $package->id,
        ...$attributes,
    ]);

    $createReservation([
        'creneau' => now()->addWeek(),
        'is_confirm' => false,
        'is_cancelled' => false,
    ]);
    $createReservation([
        'creneau' => now()->setTime(10, 0),
        'is_confirm' => false,
        'is_cancelled' => false,
    ]);
    $createReservation([
        'creneau' => now()->addWeek(),
        'is_confirm' => true,
        'is_cancelled' => false,
    ]);
    $createReservation([
        'creneau' => now()->addWeek(),
        'is_confirm' => false,
        'is_cancelled' => true,
    ]);
    $createReservation([
        'creneau' => now()->subWeek(),
        'is_confirm' => true,
        'is_cancelled' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.toValidate', 2)
            ->where('stats.confirmed', 1)
            ->where('stats.today', 1)
            ->where('stats.freeSlots', 1)
            ->has('reservations', 5)
            ->where('reservations', function (Collection $reservations): bool {
                $statuses = $reservations->pluck('status');

                return $statuses->filter(fn (string $status) => $status === 'confirmed')->count() === 2
                    && $statuses->filter(fn (string $status) => $status === 'pending')->count() === 2
                    && $statuses->filter(fn (string $status) => $status === 'cancelled')->count() === 1
                    && $reservations->every(
                        fn (array $reservation): bool => $reservation['package'] === 'Premium'
                            && str_starts_with($reservation['reference'], 'OCD-'),
                    );
            })
        );
});

test('the dashboard displays up to thirty reservations', function () {
    $user = User::factory()->create();

    Reservation::factory()->count(31)->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('reservations', 30)
        );
});
