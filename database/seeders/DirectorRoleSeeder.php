<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DirectorRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el permiso si no existe
        $permission = Permission::firstOrCreate(['name' => 'dashboard_director']);

        // Crear el rol de director si no existe
        $role = Role::firstOrCreate(['name' => 'Director']);

        // Asignar el permiso al rol
        $role->givePermissionTo($permission);
    }
}
