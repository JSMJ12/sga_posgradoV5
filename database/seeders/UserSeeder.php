<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin1234'),
                'sexo' => 'M',
                'apellido' => 'Apellido',
                'status' => 'ACTIVO',
                'image' => 'ruta/foto.jpg'
            ]
        );

        $user->assignRole('Administrador');
    }
}
