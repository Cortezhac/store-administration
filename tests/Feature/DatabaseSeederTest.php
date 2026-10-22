<?php

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;

/**
 * `db:seed` en un entorno 'production' pide confirmación (ConfirmableTrait),
 * así que hay que pasar --force explícitamente. Es exactamente lo que corre
 * el deploy en producción.
 */
function seedDatabaseWithForce(): void
{
    test()->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
        ->assertSuccessful();
}

test('crea los roles del enum sin duplicar', function () {
    seedDatabaseWithForce();

    expect(Role::pluck('name')->all())
        ->toContain(Roles::Admin->value)
        ->toContain(Roles::Employee->value);

    // Idempotente: correrlo dos veces no duplica roles ni usuarios.
    seedDatabaseWithForce();

    expect(Role::count())->toBe(count(Roles::cases()))
        ->and(User::where('email', 'test@example.com')->count())->toBe(1);
});

test('no crea el usuario de test fuera de local y testing', function () {
    // Esto es lo que corre el deploy en producción: `db:seed --force`.
    $this->app['env'] = 'production';

    seedDatabaseWithForce();

    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

test('crea el usuario de test en testing', function () {
    seedDatabaseWithForce();

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
