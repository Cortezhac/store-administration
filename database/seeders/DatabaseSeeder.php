<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // El usuario de prueba es SOLO de desarrollo (y de testing).
        //
        // En cualquier otro entorno, `db:seed --force` —que es lo que corre el
        // deploy— crearía una cuenta con email y contraseña conocidos.
        //
        // Y no es solo un tema de seguridad: las factories dependen de
        // fakerphp/faker, que es require-dev. En producción (composer install
        // --no-dev) no está instalado, así que el helper global fake() ni
        // siquiera existe y el seed moría con
        // "Call to undefined function Database\Factories\fake()".
        if (! app()->environment('local', 'testing')) {
            return;
        }

        // firstOrCreate y no User::factory(): el seeder queda idempotente y
        // no depende de faker (que no está en producción).
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );
    }
}
