<?php
// app/Filament/Pages/ManageRoles.php

namespace App\Filament\Pages;

use App\Models\Role;
use App\Models\Permission;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ManageRoles extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static string $view = 'filament.pages.manage-roles';
    protected static ?string $navigationLabel = 'Gestión de Roles';
    protected static ?string $title = 'Gestión de Roles y Permisos';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 3;

    public $selectedRole = null;
    public $rolePermissions = [];

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedRole')
                ->label('Rol')
                ->options($this->getRoles())
                ->searchable()
                ->placeholder('Seleccione un rol...')
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->selectedRole = $state;
                    $this->loadRolePermissions();
                }),
        ];
    }

    public function mount(): void
    {
        // Verificar permisos
        if (!Auth::user()->hasPermission('manage_roles')) {
            abort(403, 'No tienes permisos para gestionar roles.');
        }

        // Cargar el primer rol por defecto
        $firstRole = Role::first();
        if ($firstRole) {
            $this->selectedRole = $firstRole->id;
            $this->form->fill(['selectedRole' => $firstRole->id]);
            $this->loadRolePermissions();
        }
    }

    public function loadRolePermissions(): void
    {
        if ($this->selectedRole) {
            $role = Role::find($this->selectedRole);
            if ($role) {
                $this->rolePermissions = $role->permissions->pluck('id')->toArray();
            }
        }
    }

    public function savePermissions(): void
    {
        if (!$this->selectedRole) {
            Notification::make()
                ->title('Error')
                ->body('Seleccione un rol primero.')
                ->danger()
                ->send();
            return;
        }

        $role = Role::find($this->selectedRole);
        
        if (!$role) {
            Notification::make()
                ->title('Error')
                ->body('Rol no encontrado.')
                ->danger()
                ->send();
            return;
        }

        $role->permissions()->sync($this->rolePermissions);

        Notification::make()
            ->title('Éxito')
            ->body("Permisos actualizados para el rol {$role->display_name}.")
            ->success()
            ->send();
    }

    public function getRoles(): array
    {
        return Role::orderBy('display_name')->pluck('display_name', 'id')->toArray();
    }

    public function getPermissionsByGroup(): array
    {
        return Permission::active()
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group')
            ->map(function ($permissions) {
                return $permissions->mapWithKeys(function ($permission) {
                    return [$permission->id => $permission->display_name];
                })->toArray();
            })
            ->toArray();
    }

    public function getPermissionDescriptions(): array
    {
        return Permission::active()
            ->whereNotNull('description')
            ->pluck('description', 'id')
            ->toArray();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermission('manage_roles');
    }
}