<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumno;
use App\Models\User;
use App\Models\Maestria;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class AlumnoSeeder extends Seeder
{
    public function run()
    {
        // Obtener la maestría con id 1
        $maestria = Maestria::find(1);

        if (!$maestria) {
            $this->command->error('No se encontró la maestría con id 1. Asegúrate de tenerla en la base de datos.');
            return;
        }

        // Arancel de la maestría
        $arancel = $maestria->arancel;

        // Crear 5 alumnos
        foreach (range(1, 5) as $index) {
            // Generar un número de registro secuencial
            $nuevoRegistro = Alumno::where('maestria_id', $maestria->id)->count() + 1;

            // Generar un DNI único como string
            do {
                $dni = 'DNI' . rand(100000000, 999999999);
            } while (Alumno::where('dni', $dni)->exists());

            // Generar el email institucional basado en el formato especificado
            $nombre1 = 'Nombre' . $index;
            $nombre2 = 'Nombre2' . $index;
            $ultimosDigitosDNI = substr($dni, -2); // Extraer los últimos dos dígitos del DNI
            $emailInstitucional = strtolower($nombre1 . '-' . $nombre2 . $ultimosDigitosDNI . '@unesum.com');

            // Crear un nuevo objeto Alumno
            $alumno = Alumno::create([
                'nombre1' => $nombre1,
                'nombre2' => $nombre2,
                'apellidop' => 'ApellidoP' . $index,
                'apellidom' => 'ApellidoM' . $index,
                'contra' => bcrypt('123456'),
                'sexo' => $index % 2 == 0 ? 'M' : 'F',
                'dni' => $dni,
                'email_institucional' => $emailInstitucional,
                'email_personal' => 'email' . $index . '@personal.com',
                'estado_civil' => 'Soltero',
                'fecha_nacimiento' => Carbon::now()->subYears(rand(18, 35)),
                'provincia' => 'Provincia' . $index,
                'canton' => 'Canton' . $index,
                'barrio' => 'Barrio' . $index,
                'direccion' => 'Direccion' . $index,
                'nacionalidad' => 'Nacionalidad' . $index,
                'etnia' => 'Etnia' . $index,
                'carnet_discapacidad' => 'Carnet' . rand(1000, 9999),
                'tipo_discapacidad' => 'Discapacidad' . rand(1, 5),
                'maestria_id' => $maestria->id,
                'porcentaje_discapacidad' => rand(0, 100),
                'registro' => $nuevoRegistro,
                'monto_total' => $arancel,
                'image' => 'https://ui-avatars.com/api/?name=' . urlencode(substr($nombre1, 0, 1)),
            ]);

            // Crear el usuario asociado
            $usuario = User::create([
                'name' => $alumno->nombre1,
                'apellido' => $alumno->apellidop,
                'sexo' => $alumno->sexo,
                'password' => bcrypt('123456'), // Contraseña por defecto
                'status' => 'ACTIVO',
                'email' => $alumno->email_institucional,
                'image' => $alumno->image,
            ]);

            // Asignar el rol al usuario
            $alumnoRole = Role::findById(4); // Rol de alumno
            $usuario->assignRole($alumnoRole);
        }

        $this->command->info('Se crearon 5 alumnos con sus usuarios asociados en la maestría con id 1.');
    }
}
