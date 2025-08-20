<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            // Gestión de usuarios
            [
                'name' => 'manage_users',
                'display_name' => 'Gestionar Usuarios',
                'description' => 'Crear, editar y eliminar usuarios del sistema',
                'group' => 'usuarios'
            ],
            [
                'name' => 'view_users',
                'display_name' => 'Ver Usuarios',
                'description' => 'Ver la lista de usuarios del sistema',
                'group' => 'usuarios'
            ],

            // Gestión de roles
            [
                'name' => 'manage_roles',
                'display_name' => 'Gestionar Roles',
                'description' => 'Crear, editar y eliminar roles y permisos',
                'group' => 'roles'
            ],
            [
                'name' => 'assign_roles',
                'display_name' => 'Asignar Roles',
                'description' => 'Asignar y remover roles a usuarios',
                'group' => 'roles'
            ],

            // Gestión de citas
            [
                'name' => 'view_all_citas',
                'display_name' => 'Ver Todas las Citas',
                'description' => 'Ver citas de todas las áreas y secretarías',
                'group' => 'citas'
            ],
            [
                'name' => 'view_area_citas',
                'display_name' => 'Ver Citas del Área',
                'description' => 'Ver solo las citas del área asignada',
                'group' => 'citas'
            ],
            [
                'name' => 'manage_citas',
                'display_name' => 'Gestionar Citas',
                'description' => 'Crear, editar, confirmar y cancelar citas',
                'group' => 'citas'
            ],
            [
                'name' => 'export_citas',
                'display_name' => 'Exportar Citas',
                'description' => 'Exportar reportes y listados de citas',
                'group' => 'citas'
            ],

            // Configuración del sistema
            [
                'name' => 'manage_secretarias',
                'display_name' => 'Gestionar Secretarías',
                'description' => 'Crear, editar y eliminar secretarías',
                'group' => 'configuracion'
            ],
            [
                'name' => 'manage_areas',
                'display_name' => 'Gestionar Áreas',
                'description' => 'Crear, editar y eliminar áreas',
                'group' => 'configuracion'
            ],
            [
                'name' => 'manage_tramites',
                'display_name' => 'Gestionar Trámites',
                'description' => 'Crear, editar y eliminar trámites',
                'group' => 'configuracion'
            ],
            [
                'name' => 'manage_configuracion',
                'display_name' => 'Gestionar Configuración',
                'description' => 'Configurar horarios, días y restricciones de trámites',
                'group' => 'configuracion'
            ],

            // Reportes y estadísticas
            [
                'name' => 'view_reports',
                'display_name' => 'Ver Reportes',
                'description' => 'Acceder a reportes y estadísticas del sistema',
                'group' => 'reportes'
            ],
            [
                'name' => 'view_analytics',
                'display_name' => 'Ver Analíticas',
                'description' => 'Ver dashboard con métricas y análisis',
                'group' => 'reportes'
            ],

            // Administración del sistema
            [
                'name' => 'system_settings',
                'display_name' => 'Configuración del Sistema',
                'description' => 'Acceder a configuraciones avanzadas del sistema',
                'group' => 'sistema'
            ],
            [
                'name' => 'view_logs',
                'display_name' => 'Ver Logs',
                'description' => 'Acceder a logs y auditoría del sistema',
                'group' => 'sistema'
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // Crear roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrador',
                'description' => 'Acceso completo a todas las funcionalidades del sistema',
                'permissions' => Permission::all()->pluck('name')->toArray()
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Administrador general con acceso a la mayoría de funciones',
                'permissions' => [
                    'view_users',
                    'manage_users',
                    'assign_roles',
                    'view_all_citas',
                    'manage_citas',
                    'export_citas',
                    'manage_secretarias',
                    'manage_areas',
                    'manage_tramites',
                    'manage_configuracion',
                    'view_reports',
                    'view_analytics'
                ]
            ],
            [
                'name' => 'coordinador',
                'display_name' => 'Coordinador de Área',
                'description' => 'Responsable de un área específica y sus citas',
                'permissions' => [
                    'view_users',
                    'view_area_citas',
                    'manage_citas',
                    'export_citas',
                    'manage_tramites',
                    'manage_configuracion',
                    'view_reports'
                ]
            ],
            [
                'name' => 'operador',
                'display_name' => 'Operador',
                'description' => 'Usuario operativo que gestiona citas de su área',
                'permissions' => [
                    'view_area_citas',
                    'manage_citas',
                    'view_reports'
                ]
            ],
            [
                'name' => 'consulta',
                'display_name' => 'Solo Consulta',
                'description' => 'Usuario con acceso de solo lectura a las citas de su área',
                'permissions' => [
                    'view_area_citas'
                ]
            ],
            [
                'name' => 'recepcionista',
                'display_name' => 'Recepcionista',
                'description' => 'Encargado de la atención y confirmación de citas',
                'permissions' => [
                    'view_area_citas',
                    'manage_citas'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            // Verificar que los permisos existen antes de asignar
            $validPermissions = Permission::whereIn('name', $permissions)->pluck('id')->toArray();

            if (count($validPermissions) !== count($permissions)) {
                $missingPermissions = array_diff($permissions, Permission::whereIn('name', $permissions)->pluck('name')->toArray());
                $this->command->warn("Advertencia: Los siguientes permisos no existen para el rol {$role->name}: " . implode(', ', $missingPermissions));
            }

            // Sincronizar solo permisos válidos
            $role->permissions()->sync($validPermissions);

            $this->command->info("Rol '{$role->display_name}' creado/actualizado con " . count($validPermissions) . " permisos.");
        }

        $this->command->info('Roles y permisos creados exitosamente.');
    }
}
