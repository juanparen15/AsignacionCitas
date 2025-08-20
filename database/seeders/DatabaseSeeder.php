<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SecretariaSeeder::class,
            RolePermissionSeeder::class,
        ]);

        // Crear usuario super admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sistemas.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'active' => true,
            ]
        );

        $superAdmin->assignRole('super_admin');

        // Crear usuario coordinador de ejemplo
        $coordinador = User::firstOrCreate(
            ['email' => 'coordinador@sistema.com'],
            [
                'name' => 'Coordinador de Área',
                'password' => Hash::make('coordinador123'),
                'email_verified_at' => now(),
                'active' => true,
                'area_id' => 1, // Asignar al primer área creada
            ]
        );

        $coordinador->assignRole('coordinador');

        // Crear usuario operador de ejemplo
        $operador = User::firstOrCreate(
            ['email' => 'operador@sistema.com'],
            [
                'name' => 'Operador de Citas',
                'password' => Hash::make('operador123'),
                'email_verified_at' => now(),
                'active' => true,
                'area_id' => 1, // Asignar al primer área creada
            ]
        );

        $operador->assignRole('operador');

        $this->command->info('Usuarios de ejemplo creados:');
        $this->command->info('- admin@sistema.com / admin123 (Super Admin)');
        $this->command->info('- coordinador@sistema.com / coordinador123 (Coordinador)');
        $this->command->info('- operador@sistema.com / operador123 (Operador)');
    }
}