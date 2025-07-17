<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Postulante;
use App\Models\User;
use App\Models\Maestria;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission;

class PostulantesTableSeeder extends Seeder
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
        $incripcion = $maestria->inscripcion;
        $maticula = $maestria->matricula;

        // Crear 25 postulantes
        foreach (range(400, 425) as $index) {
            // Generar un número de registro secuencial
            $nuevoRegistro = Postulante::where('maestria_id', $maestria->id)->count() + 1;

            // Generar un DNI único como string
            do {
                $dni = 'DNI' . rand(100000000, 999999999);
            } while (Postulante::where('dni', $dni)->exists());

            // Generar el email institucional basado en el formato especificado
            $nombre1 = 'Nombre' . $index;
            $nombre2 = 'Nombre2' . $index;
            $ultimosDigitosDNI = substr($dni, -2); // Extraer los últimos dos dígitos del DNI
            $emailInstitucional = strtolower($nombre1 . '-' . $nombre2 . $ultimosDigitosDNI . '@unesum.com');

            // Crear un nuevo objeto Postulante
            $postulante = Postulante::create([
                'dni' => $dni,
                'apellidop' => 'ApellidoP' . $index,
                'apellidom' => 'ApellidoM' . $index,
                'nombre1' => $nombre1,
                'nombre2' => $nombre2,
                'correo_electronico' => $emailInstitucional,
                'celular' => '099' . rand(1000000, 9999999),
                'titulo_profesional' => 'Titulo' . $index,
                'universidad_titulo' => 'Universidad' . $index,
                'sexo' => $index % 2 == 0 ? 'M' : 'F',
                'fecha_nacimiento' => Carbon::now()->subYears(rand(18, 35)),
                'nacionalidad' => 'Nacionalidad' . $index,
                'discapacidad' => $index % 2 == 0 ? 'Si' : 'No',
                'porcentaje_discapacidad' => rand(0, 100),
                'codigo_conadis' => 'CONADIS' . rand(1000, 9999),
                'provincia' => 'Provincia' . $index,
                'etnia' => 'Etnia' . $index,
                'nacionalidad_indigena' => 'NacionalidadIndigena' . $index,
                'canton' => 'Canton' . $index,
                'direccion' => 'Direccion' . $index,
                'tipo_colegio' => 'TipoColegio' . $index,
                'cantidad_miembros_hogar' => rand(1, 6),
                'ingreso_total_hogar' => '$' . rand(1000, 10000),
                'nivel_formacion_padre' => 'NivelPadre' . $index,
                'nivel_formacion_madre' => 'NivelMadre' . $index,
                'origen_recursos_estudios' => 'OrigenEstudios' . $index,
                'imagen' => 'https://ui-avatars.com/api/?name=' . urlencode(substr($nombre1, 0, 1)),
                'pdf_cedula' => 'path/to/pdf_cedula' . $index . '.pdf',
                'pdf_papelvotacion' => 'path/to/pdf_papelvotacion' . $index . '.pdf',
                'pdf_titulouniversidad' => 'path/to/pdf_titulouniversidad' . $index . '.pdf',
                'pdf_conadis' => 'path/to/pdf_conadis' . $index . '.pdf',
                'pdf_hojavida' => 'path/to/pdf_hojavida' . $index . '.pdf',
                'status' => 0,  // Estado por defecto (no aceptado)
                'monto_matricula' => $maticula,
                'monto_inscripcion' => $incripcion,  // Inscripción es 10% del arancel
                'pago_matricula' => 'path/to/pago_matricula' . $index . '.pdf',
                'tipo_discapacidad' => 'TipoDiscapacidad' . rand(1, 5),
                'carta_aceptacion' => 'path/to/carta_aceptacion' . $index . '.pdf',
                'maestria_id' => $maestria->id,
            ]);

            // Crear el usuario asociado
            $usuario = User::create([
                'name' => $postulante->nombre1,
                'apellido' => $postulante->apellidop,
                'sexo' => $postulante->sexo,
                'password' => bcrypt('123456'), // Contraseña por defecto
                'status' => 'ACTIVO',
                'email' => $postulante->correo_electronico,
                'image' => $postulante->imagen,
            ]);

            // Asignar el rol al usuario
            $postulanteRole = Role::findById(5); // Rol de postulante
            $usuario->assignRole($postulanteRole);
        }

        $this->command->info('Se crearon 25 postulantes con sus usuarios asociados en la maestría con id 1.');
    }
    
}

