<?php

use App\Models\Mode;
use App\Models\Package;
use App\Models\Reservation;

test('a reservation reference is built from the current year and its id', function () {
    $reservation = Reservation::factory()->create();

    expect($reservation->reference)
        ->toBe(sprintf('OCD-%d-%06d', now()->year, $reservation->id));
});

test('a reservation belongs to a mode and a package', function () {
    $reservation = Reservation::factory()->create();

    expect($reservation->mode)->toBeInstanceOf(Mode::class)
        ->and($reservation->package)->toBeInstanceOf(Package::class);
});

test('a reservation is not confirmed nor cancelled by default', function () {
    $reservation = Reservation::factory()->create();

    expect($reservation->is_confirm)->toBeFalse()
        ->and($reservation->is_cancelled)->toBeFalse();
});
