<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:create-admin-user')]
#[Description('Create an admin user for the application')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = text(
            label: 'Nombre del administrador',
            required: 'El nombre es obligatorio.',
            validate: fn (string $value) => strlen($value) < 2
                ? 'El nombre debe tener al menos 2 caracteres.'
                : null,
        );

        $email = text(
            label: 'Email del administrador',
            required: 'El email es obligatorio.',
            transform: fn (string $value) => strtolower(trim($value)),
            validate: function (string $value) {
                if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return 'El email no es válido.';
                }

                if (User::where('email', $value)->exists()) {
                    return 'Ya existe un usuario con ese email.';
                }

                return null;
            },
        );

        $password = password(
            label: 'Contraseña (mínimo 8 caracteres)',
            required: 'La contraseña es obligatoria.',
            validate: fn (string $value) => strlen($value) < 8
                ? 'La contraseña debe tener al menos 8 caracteres.'
                : null,
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        Role::findOrCreate(Roles::Admin->value);
        $user->assignRole(Roles::Admin->value);

        $this->info(sprintf('Usuario administrador %s creado correctamente.', $user->email));
        $this->table(
            ['Nombre', 'Email', 'Rol'],
            [[$user->name, $user->email, implode(', ', $user->getRoleNames()->all())]],
        );
    }
}
