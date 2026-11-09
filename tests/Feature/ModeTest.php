<?php

use App\Models\Mode;
use App\Models\Reservation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('modes.index'))->assertRedirect(route('login'));
});

test('the modes index lists the modes with their reservation count', function () {
    $this->actingAs(User::factory()->create());

    $mode = Mode::factory()->create(['name' => 'En ligne']);
    Reservation::factory()->count(2)->create(['mode_id' => $mode->id]);

    $this->get(route('modes.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('modes/Index')
            ->has('modes', 1)
            ->where('modes.0.name', 'En ligne')
            ->where('modes.0.reservations_count', 2)
        );
});

test('a mode can be created', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('modes.store'), ['name' => 'En présentiel'])
        ->assertRedirect(route('modes.index'));

    expect(Mode::query()->where('name', 'En présentiel')->exists())->toBeTrue();
});

test('a mode name is required', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('modes.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    expect(Mode::query()->count())->toBe(0);
});

test('a mode name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Mode::factory()->create(['name' => 'En ligne']);

    $this->post(route('modes.store'), ['name' => 'En ligne'])
        ->assertSessionHasErrors('name');
});

test('a mode name cannot exceed 255 characters', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('modes.store'), ['name' => str_repeat('a', 256)])
        ->assertSessionHasErrors('name');
});

test('a mode can be updated', function () {
    $this->actingAs(User::factory()->create());

    $mode = Mode::factory()->create(['name' => 'Ancien nom']);

    $this->put(route('modes.update', $mode), ['name' => 'Nouveau nom'])
        ->assertRedirect(route('modes.index'));

    expect($mode->refresh()->name)->toBe('Nouveau nom');
});

test('updating a mode with its own name is allowed', function () {
    $this->actingAs(User::factory()->create());

    $mode = Mode::factory()->create(['name' => 'En ligne']);

    $this->put(route('modes.update', $mode), ['name' => 'En ligne'])
        ->assertSessionHasNoErrors();
});

test('an unused mode can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $mode = Mode::factory()->create();

    $this->delete(route('modes.destroy', $mode))
        ->assertRedirect(route('modes.index'));

    expect(Mode::query()->find($mode->id))->toBeNull();
});

test('a mode used by a reservation cannot be deleted', function () {
    $this->actingAs(User::factory()->create());

    $mode = Mode::factory()->create();
    Reservation::factory()->create(['mode_id' => $mode->id]);

    $this->from(route('modes.index'))
        ->delete(route('modes.destroy', $mode))
        ->assertRedirect(route('modes.index'));

    expect(Mode::query()->find($mode->id))->not->toBeNull();
});
