<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Docente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DocenteSeeder extends Seeder
{
    public function run()
    {
        // Crear 25 docentes con usuarios asociados
        foreach (range(1, 25) as $index) {
            // Crear docente
            $docente = Docente::create([
                'dni' => Str::random(10),
                'nombre1' => 'Nombre' . $index,
                'nombre2' => 'Nombre2' . $index,
                'apellidop' => 'ApellidoP' . $index,
                'apellidom' => 'ApellidoM' . $index,
                'contra' => bcrypt('password'), // Contraseña predeterminada
                'email' => 'email' . $index . '@institucional.com',
                'sexo' => $index % 2 == 0 ? 'M' : 'F',
                'tipo' => $index % 2 == 0 ? 'NOMBRADO' : 'CONTRATADO',
                'image' => 'https://ui-avatars.com/api/?name=' . urlencode('Nombre' . $index),
            ]);

            // Crear usuario asociado al docente
            $usuario = User::create([
                'name' => $docente->nombre1,
                'apellido' => $docente->apellidop,
                'sexo' => $docente->sexo,
                'password' => bcrypt('password'), // Contraseña predeterminada
                'status' => 'ACTIVO',
                'email' => $docente->email,
                'image' => $docente->image,
            ]);

            // Asignar rol de docente
            $docenteRole = Role::findById(2); // Asume que el ID del rol docente es 2
            $usuario->assignRole($docenteRole);
        }

        // Mensaje de éxito
        $this->command->info('Se crearon 25 docentes con sus usuarios asociados.');
    }
}
