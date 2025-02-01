<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir roles con IDs específicos
        $roles = [
            ['id' => 1, 'name' => 'Administrador'],
            ['id' => 2, 'name' => 'Docente'],
            ['id' => 3, 'name' => 'Secretario'],
            ['id' => 4, 'name' => 'Alumno'],
            ['id' => 5, 'name' => 'Postulante'],
            ['id' => 6, 'name' => 'Coordinador'],
            ['id' => 7, 'name' => 'Secretario/a EPSU'],
            ['id' => 8, 'name' => 'Titulado_proceso'],
            ['id' => 9, 'name' => 'Titulado'],
            ['id' => 10, 'name' => 'Tutor'],
        ];
        
        // Crear o actualizar roles con IDs fijos
        foreach ($roles as $roleData) {
            Role::updateOrCreate(['id' => $roleData['id']], $roleData);
        }

        // Obtener los roles recién creados o actualizados
        $role1 = Role::find(1);
        $role2 = Role::find(2);
        $role3 = Role::find(3);
        $role4 = Role::find(4);
        $role5 = Role::find(5);
        $role6 = Role::find(6);
        $role7 = Role::find(7);
        $role8 = Role::find(8);
        $role9 = Role::find(9);
        $role10 = Role::find(10);

        // Crear permisos y sincronizar con roles
        Permission::create(['name' => 'dashboard_admin'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.usuarios.disable'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.usuarios.enable'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.usuarios.crear'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.usuarios.editar'])->syncRoles([$role1]);
        Permission::create(['name' => 'admin.usuarios.listar'])->syncRoles([$role1]);

        Permission::create(['name' => 'dashboard_secretario'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'secretarios.crear'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'secretarios.editar'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'secretarios.listar'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'docentes.crear'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'docentes.editar'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'docentes.listar'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'paralelo.crear'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'paralelo.eliminar'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'paralelo.editar'])->syncRoles([$role1, $role3]);
        Permission::create(['name' => 'paralelo.listar'])->syncRoles([$role1, $role3]);

        Permission::create(['name' => 'dashboard_docente'])->syncRoles([$role2]);
        Permission::create(['name' => 'calificar'])->syncRoles([$role2, $role1]);

        Permission::create(['name' => 'dashboard_alumno'])->syncRoles([$role4]);
        Permission::create(['name' => 'alumno_descuento'])->syncRoles([$role4]);
        Permission::create(['name' => 'dashboard_coordinador'])->syncRoles([$role6]);
        Permission::create(['name' => 'dashboard_secretario_epsu'])->syncRoles([$role7]);
        Permission::create(['name' => 'alumno_pago'])->syncRoles([$role4]);

        Permission::create(['name' => 'dashboard_postulante'])->syncRoles([$role5]);

        Permission::create(['name' => 'titulado_proceso'])->syncRoles([$role8]);
        Permission::create(['name' => 'titulado'])->syncRoles([$role9]);
        Permission::create(['name' => 'revisar_tesis'])->syncRoles([$role10]);
    }
}
