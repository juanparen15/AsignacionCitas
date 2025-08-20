<?php
// Crear comando de artisan para limpiar datos
// php artisan make:command CleanRolePermissions

// app/Console/Commands/CleanRolePermissions.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class CleanRolePermissions extends Command
{
    protected $signature = 'roles:clean';
    protected $description = 'Limpiar datos corruptos en las tablas de roles y permisos';

    public function handle()
    {
        $this->info('Iniciando limpieza de datos de roles y permisos...');

        // 1. Limpiar registros con permission_id = 0 o que no existen
        $deleted = DB::table('permission_role')
            ->where('permission_id', '=', 0)
            ->orWhereNotIn('permission_id', function($query) {
                $query->select('id')->from('permissions');
            })
            ->delete();

        if ($deleted > 0) {
            $this->warn("Eliminados {$deleted} registros corruptos de permission_role");
        }

        // 2. Limpiar registros con role_id que no existe
        $deleted = DB::table('permission_role')
            ->whereNotIn('role_id', function($query) {
                $query->select('id')->from('roles');
            })
            ->delete();

        if ($deleted > 0) {
            $this->warn("Eliminados {$deleted} registros con role_id inválido");
        }

        // 3. Limpiar registros duplicados
        $duplicates = DB::select("
            SELECT role_id, permission_id, COUNT(*) as count 
            FROM permission_role 
            GROUP BY role_id, permission_id 
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicates as $duplicate) {
            // Mantener solo uno y eliminar los duplicados
            $records = DB::table('permission_role')
                ->where('role_id', $duplicate->role_id)
                ->where('permission_id', $duplicate->permission_id)
                ->orderBy('id')
                ->get();

            // Eliminar todos excepto el primero
            for ($i = 1; $i < count($records); $i++) {
                DB::table('permission_role')->where('id', $records[$i]->id)->delete();
            }

            $this->warn("Eliminados " . ($duplicate->count - 1) . " duplicados para role_id={$duplicate->role_id}, permission_id={$duplicate->permission_id}");
        }

        // 4. Verificar integridad
        $this->info('Verificando integridad de datos...');
        
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalRelations = DB::table('permission_role')->count();

        $this->info("Roles: {$totalRoles}");
        $this->info("Permisos: {$totalPermissions}");
        $this->info("Relaciones: {$totalRelations}");

        // 5. Mostrar roles sin permisos
        $rolesWithoutPermissions = Role::doesntHave('permissions')->get();
        if ($rolesWithoutPermissions->count() > 0) {
            $this->warn('Roles sin permisos:');
            foreach ($rolesWithoutPermissions as $role) {
                $this->line("  - {$role->display_name} ({$role->name})");
            }
        }

        $this->info('Limpieza completada exitosamente.');
        return 0;
    }
}