<!-- resources/views/filament/pages/manage-roles.blade.php -->
<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Selector de Rol -->
        <div class="fi-section-content-ctn overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-500/10">
                        <x-heroicon-o-shield-check class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Seleccionar Rol</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Elija el rol para gestionar sus permisos</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-950 dark:text-white">Rol</label>
                        <select 
                            wire:model.live="selectedRole"
                            class="block w-full rounded-lg border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 bg-white dark:bg-gray-900 dark:text-white dark:ring-gray-600 dark:placeholder:text-gray-500 dark:focus:ring-primary-500"
                        >
                            <option value="">Seleccione un rol...</option>
                            @foreach($this->getRoles() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedRole)
                        @php
                            $role = \App\Models\Role::find($selectedRole);
                        @endphp
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                            <h4 class="font-medium text-gray-950 dark:text-white mb-2">{{ $role->display_name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $role->description }}</p>
                            <div class="mt-3 flex items-center gap-4">
                                <span class="fi-badge inline-flex items-center justify-center gap-x-1 rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-600 ring-1 ring-inset ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                    {{ $role->users()->count() }} usuarios
                                </span>
                                <span class="fi-badge inline-flex items-center justify-center gap-x-1 rounded-md bg-success-50 px-2 py-1 text-xs font-medium text-success-600 ring-1 ring-inset ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                    {{ $role->permissions()->count() }} permisos
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permisos por Grupo -->
        @if($selectedRole)
            <form wire:submit="savePermissions">
                <div class="space-y-6">
                    @foreach($this->getPermissionsByGroup() as $group => $permissions)
                        <div class="fi-section-content-ctn overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                            <div class="fi-section-content p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-custom-400 to-custom-600">
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($group, 0, 2)) }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-950 dark:text-white capitalize">
                                        {{ ucfirst($group) }}
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($permissions as $permissionId => $permissionName)
                                        @php
                                            $description = $this->getPermissionDescriptions()[$permissionId] ?? null;
                                        @endphp
                                        <label class="fi-checkbox-option group relative flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 transition-colors hover:border-primary-300 hover:bg-primary-50 dark:border-white/10 dark:hover:border-primary-600 dark:hover:bg-primary-500/10">
                                            <input 
                                                type="checkbox" 
                                                wire:model="rolePermissions" 
                                                value="{{ $permissionId }}"
                                                class="fi-checkbox-input mt-1 rounded border-none bg-white shadow-sm ring-1 ring-inset ring-gray-950/10 checked:ring-0 focus:ring-2 focus:ring-primary-600 focus:checked:ring-primary-600 disabled:pointer-events-none disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-current disabled:checked:text-gray-400 dark:bg-white/5 dark:ring-white/20 dark:checked:bg-primary-500 dark:focus:ring-primary-500 dark:disabled:bg-transparent dark:disabled:checked:bg-gray-600 text-primary-600"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-medium text-gray-950 dark:text-white block">
                                                    {{ $permissionName }}
                                                </span>
                                                @if($description)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">
                                                        {{ $description }}
                                                    </span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Botones de Acción -->
                    <div class="fi-section-content-ctn overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <div class="fi-section-content p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Guardar Cambios</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Los cambios se aplicarán inmediatamente a todos los usuarios con este rol</p>
                                </div>
                                <div class="flex gap-3">
                                    <button 
                                        type="button"
                                        wire:click="loadRolePermissions"
                                        class="fi-btn fi-btn-outlined fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-btn-color-gray gap-1.5 px-3 py-2 text-sm ring-1 bg-white text-gray-950 hover:bg-gray-50 focus-visible:ring-primary-600 ring-gray-950/10 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:ring-white/20"
                                    >
                                        Restablecer
                                    </button>
                                    <button 
                                        type="submit"
                                        class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-btn-color-primary gap-1.5 px-3 py-2 text-sm bg-primary-600 text-white hover:bg-primary-500 focus-visible:ring-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400"
                                    >
                                        <x-heroicon-m-check class="-ml-0.5 h-4 w-4" />
                                        Guardar Permisos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        <!-- Información Adicional -->
        <div class="fi-section-content-ctn overflow-hidden rounded-xl bg-gradient-to-r from-primary-50 to-custom-50 shadow-sm ring-1 ring-primary-600/10 dark:from-primary-500/10 dark:to-custom-500/10 dark:ring-primary-400/20">
            <div class="fi-section-content p-6">
                <div class="flex items-start gap-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-500/20 flex-shrink-0">
                        <x-heroicon-o-information-circle class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-primary-900 dark:text-primary-100 mb-2">Información Importante</h4>
                        <ul class="text-sm text-primary-800 dark:text-primary-200 space-y-1 list-disc list-inside">
                            <li>Los cambios en permisos se aplican inmediatamente a todos los usuarios con el rol seleccionado</li>
                            <li>El rol "Super Administrador" tiene acceso completo automáticamente</li>
                            <li>Los usuarios sin área asignada no podrán ver citas específicas de área</li>
                            <li>Revise cuidadosamente los permisos antes de guardar los cambios</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-to-br.from-custom-400.to-custom-600 {
            background: linear-gradient(135deg, rgb(168 85 247) 0%, rgb(124 58 237) 100%);
        }
        
        .dark .bg-gradient-to-r.from-primary-50.to-custom-50,
        .dark .from-primary-500\/10.to-custom-500\/10 {
            background: linear-gradient(90deg, rgb(59 130 246 / 0.1) 0%, rgb(168 85 247 / 0.1) 100%);
        }
    </style>
</x-filament-panels::page>