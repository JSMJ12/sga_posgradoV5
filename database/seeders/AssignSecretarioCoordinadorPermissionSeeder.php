<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignSecretarioCoordinadorPermissionSeeder extends Seeder
{
    public function run()
    {
        // Verifica si el permiso ya existe, si no, lo crea
        $permission = Permission::firstOrCreate(['name' => 'secretario_coordinador']);

        // Obtén los roles
        $secretarioRole = Role::findByName('Secretario/a EPSU');
        $coordinadorRole = Role::findByName('Coordinador');
        $administradorRole = Role::findByName('Administrador');

        // Asigna el permiso a ambos roles
        $secretarioRole->givePermissionTo($permission);
        $coordinadorRole->givePermissionTo($permission);
        $administradorRole->givePermissionTo($permission);
        $this->command->info('El permiso "secretario_coordinador" ha sido asignado a los roles Secretario, Coordinador y administrador.');
    }
}
