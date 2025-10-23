<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     * Only creates essential data: permissions and admin user.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeding para producción...');

        // 1. Crear permisos y roles
        $this->call([
            ShieldSeeder::class,
        ]);

        // 2. Crear solo el usuario administrador
        $this->createAdminUser();

        $this->command->info('✅ Seeding de producción completado.');
        $this->command->info('👤 Usuario administrador: admin@restaurante.com');
        $this->command->info('🔑 Contraseña temporal: password');
        $this->command->warn('⚠️  IMPORTANTE: Cambia la contraseña después del primer login.');
    }

    private function createAdminUser(): void
    {
        $this->command->info('👤 Creando usuario administrador...');

        $admin = User::firstOrCreate([
            'email' => 'admin@restaurante.com',
        ], [
            'name' => 'Administrador',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de administrador
        setPermissionsTeamId(1);
        $admin->syncRoles('admin');

        $this->command->info('✅ Usuario administrador creado exitosamente.');
    }
}
