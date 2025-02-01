<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'podonga69@gmail.com'],
            [
                'name' => 'admin',
                'password' => '$2y$10$pEEmboyCEH51h7w79RbH1eG5NhPF4fwPyTjExJ3wEkgosr0o6NIgC',
                'sexo' => 'M',
                'apellido' => 'Apellido',
                'status' => 'ACTIVO',
                'image' => 'ruta/foto.jpg'
            ]
        );
        
        $user->assignRole('Administrador');
    }
}
