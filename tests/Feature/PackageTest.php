<?php

use App\Models\Package;
use App\Models\Reservation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('packages.index'))->assertRedirect(route('login'));
});

test('the packages index lists the packages with their reservation count', function () {
    $this->actingAs(User::factory()->create());

    $package = Package::factory()->create(['name' => 'Premium']);
    Reservation::factory()->count(2)->create(['package_id' => $package->id]);

    $this->get(route('packages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('packages/Index')
            ->has('packages', 1)
            ->where('packages.0.name', 'Premium')
            ->where('packages.0.reservations_count', 2)
        );
});

test('a package can be created', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('packages.store'), ['name' => 'Essentiel'])
        ->assertRedirect(route('packages.index'));

    expect(Package::query()->where('name', 'Essentiel')->exists())->toBeTrue();
});

test('a package name is required', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('packages.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    expect(Package::query()->count())->toBe(0);
});

test('a package name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Package::factory()->create(['name' => 'Premium']);

    $this->post(route('packages.store'), ['name' => 'Premium'])
        ->assertSessionHasErrors('name');
});

test('a package name cannot exceed 255 characters', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('packages.store'), ['name' => str_repeat('a', 256)])
        ->assertSessionHasErrors('name');
});

test('a package can be updated', function () {
    $this->actingAs(User::factory()->create());

    $package = Package::factory()->create(['name' => 'Ancien nom']);

    $this->put(route('packages.update', $package), ['name' => 'Nouveau nom'])
        ->assertRedirect(route('packages.index'));

    expect($package->refresh()->name)->toBe('Nouveau nom');
});

test('updating a package with its own name is allowed', function () {
    $this->actingAs(User::factory()->create());

    $package = Package::factory()->create(['name' => 'Premium']);

    $this->put(route('packages.update', $package), ['name' => 'Premium'])
        ->assertSessionHasNoErrors();
});

test('an unused package can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $package = Package::factory()->create();

    $this->delete(route('packages.destroy', $package))
        ->assertRedirect(route('packages.index'));

    expect(Package::query()->find($package->id))->toBeNull();
});

test('a package used by a reservation cannot be deleted', function () {
    $this->actingAs(User::factory()->create());

    $package = Package::factory()->create();
    Reservation::factory()->create(['package_id' => $package->id]);

    $this->from(route('packages.index'))
        ->delete(route('packages.destroy', $package))
        ->assertRedirect(route('packages.index'));

    expect(Package::query()->find($package->id))->not->toBeNull();
});
