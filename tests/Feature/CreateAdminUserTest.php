<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('creates an admin user with the admin role', function () {
    $this->artisan('app:create-admin-user')
        ->expectsQuestion('Nombre del administrador', 'Capi Admin')
        ->expectsQuestion('Email del administrador', 'admin@example.com')
        ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'secret123')
        ->expectsOutputToContain('admin@example.com')
        ->assertSuccessful();

    $user = User::where('email', 'admin@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Capi Admin')
        ->and(Hash::check('secret123', $user->password))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();
});

test('fails when the email is already taken', function () {
    User::factory()->create(['email' => 'admin@example.com']);

    $this->artisan('app:create-admin-user')
        ->expectsQuestion('Nombre del administrador', 'Capi Admin')
        ->expectsQuestion('Email del administrador', 'admin@example.com')
        ->expectsOutputToContain('Ya existe un usuario con ese email.')
        ->assertFailed();

    expect(User::where('email', 'admin@example.com')->count())->toBe(1);
});
