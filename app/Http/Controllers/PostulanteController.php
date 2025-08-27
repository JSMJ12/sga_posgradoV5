<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Postulante;
use App\Models\Maestria;
use App\Models\Secretario;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Notifications\MatriculaExito;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NuevoUsuarioNotification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PostulanteAceptadoNotification;
use App\Models\Docente;
use Illuminate\Validation\Rule;



class PostulanteController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();

        if ($user->hasRole('Administrador')) {
            $postulantes = Postulante::with('documentos_verificados')->get();
        } elseif ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();

            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $postulantes = Postulante::whereIn('maestria_id', $maestriasIds)
                ->with('documentos_verificados')
                ->get();
        } elseif ($user->hasRole('Coordinador')) {
            $docente = Docente::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();

            $maestria = $docente->maestria->first();

            if (!$maestria) {
                abort(403, 'El coordinador no tiene ninguna maestría asignada.');
            }

            $postulantes = Postulante::where('maestria_id', $maestria->id)
                ->with('documentos_verificados')
                ->get();
        } else {
            abort(403, 'No autorizado');
        }


        return view('postulantes.index', compact('postulantes', 'perPage'));
    }

    public function create()
    {
        $maestrias = Maestria::where('status', 'ACTIVO')->get();
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        $tipo_colegio = [
            'FISCAL',
            'FISCOMISIONAL',
            'PARTICULAR',
            'MUNICIPAL',
            'EXTRANJERO',
            'NO REGISTRA'
        ];
        $ingreso_hogar = [
            'RANGO 1 - HASTA 1 SBU',
            'RANGO 2 - MÁS DE 1 A MENOS DE 2 SBU',
            'RANGO 3 - MÁS DE 2 A MENOS DE 3 SBU',
            'RANGO 4 - MÁS DE 3 A MENOS DE 4 SBU',
            'RANGO 5 - MÁS DE 4 A MENOS DE 5 SBU',
            'RANGO 6 - MÁS DE 5 A MENOS DE 6 SBU',
            'RANGO 7 - MÁS DE 6 A MENOS DE 7 SBU',
            'RANGO 8 - MÁS DE 7 A MENOS DE 8 SBU',
            'RANGO 9 - MÁS DE 8 A MENOS DE 9 SBU',
            'RANGO 10-DE 9 EN ADELANTE',
            'NO REGISTRA'
        ];
        $formacion_padre = [
            'NINGUNO',
            'CENTRO DE ALFABETIZACIÓN',
            'JARDIN INFANTES',
            'EDUCACIÓN BÁSICA',
            'EDUCACIÓN MEDIA',
            'SUPERIOR NO UNIVERSITARIA COMPLETA',
            'SUPERIOR NO UNIVERSITARIA INCOMPLETA',
            'SUPERIOR UNIVERSITARIA COMPLETA',
            'SUPERIOR UNIVERSITARIA INCOMPLETA',
            'DIPLOMADO',
            'ESPECIALIDAD',
            'POSGRADO MAESTRÍA',
            'POSGRADO ESPECIALIDAD ÁREA SALUD',
            'POSGRADO PHD',
            'NO SABE',
            'NO REGISTRA'
        ];
        $origen_recursos = [
            'RECURSOS PROPIOS',
            'PADRES TUTORES',
            'PAREJA SENTIMENTAL',
            'HERMANOS',
            'OTROS MIEMBROS DEL HOGAR',
            'OTROS FAMILIARES',
            'BECA ESTUDIO',
            'CRÉDITO EDUCATIVO',
            'NO REGISTRA'
        ];

        return view('postulantes.create', compact('maestrias', 'provincias', 'tipo_colegio', 'ingreso_hogar', 'formacion_padre', 'origen_recursos'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'dni' => [
                'required',
                Rule::unique('postulantes', 'dni'),
                function ($attribute, $value, $fail) {
                    if (DB::table('alumnos')->where('dni', $value)->exists()) {
                        $fail('El DNI ya está registrado en alumnos.');
                    }
                },
            ],
            'correo_electronico' => [
                'required',
                'email',
                Rule::unique('postulantes', 'correo_electronico'),
                function ($attribute, $value, $fail) {
                    if (DB::table('alumnos')->where('email_institucional', $value)->exists()) {
                        $fail('El correo ya está registrado en alumnos.');
                    }
                },
            ],
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $maestria = Maestria::where('id', $request->input('maestria_id'))->first();

        // Valores por defecto si no hay maestría
        $montoMatricula = $maestria ? $maestria->matricula : 0;
        $montoInscripcion = $maestria ? $maestria->inscripcion : 0;

        $imagenPath = null;

        // Inicializar el objeto Postulante
        $postulante = new Postulante();

        // Generar imagen de avatar o cargar la proporcionada
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes_usuarios', 'public');
        } else {
            $primeraLetra = substr($request->input('nombre1'), 0, 1);
            $imagenPath = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }
        // Guardar el postulante
        $postulante->fill([
            // Datos personales
            'dni' => $request->input('dni'),
            'apellidop' => $request->input('apellidop'),
            'apellidom' => $request->input('apellidom'),
            'nombre1' => $request->input('nombre1'),
            'nombre2' => $request->input('nombre2'),
            'correo_electronico' => $request->input('correo_electronico'),
            'celular' => $request->input('celular'),
            'telefono_convencional' => $request->input('telefono_convencional'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'edad' => $request->input('edad'),
            'sexo' => $request->input('sexo'),
            'tipo_sangre' => $request->input('tipo_sangre'),
            'nacionalidad' => $request->input('nacionalidad'),
            'anios_residencia' => $request->input('anios_residencia'),
            'libreta_militar' => $request->input('libreta_militar'),
            'discapacidad' => $request->input('discapacidad'),
            'tipo_discapacidad' => $request->input('tipo_discapacidad'),
            'porcentaje_discapacidad' => $request->input('porcentaje_discapacidad'),
            'codigo_conadis' => $request->input('codigo_conadis'),
            'imagen' => $imagenPath,

            // Lugar de residencia
            'pais_residencia' => $request->input('pais_residencia'),
            'provincia' => $request->input('provincia'),
            'canton' => $request->input('canton'),
            'parroquia' => $request->input('parroquia'),
            'calle_principal' => $request->input('calle_principal'),
            'numero_direccion' => $request->input('numero_direccion'),
            'calle_secundaria' => $request->input('calle_secundaria'),
            'referencia_direccion' => $request->input('referencia_direccion'),
            'telefono_domicilio' => $request->input('telefono_domicilio'),
            'celular_residencia' => $request->input('celular_residencia'),

            // Contacto de emergencia
            'contacto_apellidos' => $request->input('contacto_apellidos'),
            'contacto_nombres' => $request->input('contacto_nombres'),
            'contacto_parentesco' => $request->input('contacto_parentesco'),
            'contacto_telefono' => $request->input('contacto_telefono'),
            'contacto_celular' => $request->input('contacto_celular'),

            // Académica
            'especialidad_bachillerato' => $request->input('especialidad_bachillerato'),
            'colegio_bachillerato' => $request->input('colegio_bachillerato'),
            'ciudad_bachillerato' => $request->input('ciudad_bachillerato'),
            'titulo_profesional' => $request->input('titulo_profesional'),
            'especialidad_mencion' => $request->input('especialidad_mencion'),
            'universidad_titulo' => $request->input('universidad_titulo'),
            'ciudad_universidad' => $request->input('ciudad_universidad'),
            'pais_universidad' => $request->input('pais_universidad'),
            'registro_senescyt' => $request->input('registro_senescyt'),
            'titulo_posgrado' => $request->input('titulo_posgrado'),
            'denominacion_posgrado' => $request->input('denominacion_posgrado'),
            'universidad_posgrado' => $request->input('universidad_posgrado'),
            'ciudad_posgrado' => $request->input('ciudad_posgrado'),
            'pais_posgrado' => $request->input('pais_posgrado'),

            // Laboral
            'lugar_trabajo' => $request->input('lugar_trabajo'),
            'funcion_laboral' => $request->input('funcion_laboral'),
            'ciudad_trabajo' => $request->input('ciudad_trabajo'),
            'direccion_trabajo' => $request->input('direccion_trabajo'),
            'telefono_trabajo' => $request->input('telefono_trabajo'),

            // Datos socioeconómicos
            'etnia' => $request->input('etnia'),
            'nacionalidad_indigena' => $request->input('nacionalidad_indigena'),
            'direccion' => $request->input('direccion'),
            'tipo_colegio' => $request->input('tipo_colegio'),
            'cantidad_miembros_hogar' => $request->input('cantidad_miembros_hogar'),
            'ingreso_total_hogar' => $request->input('ingreso_total_hogar'),
            'nivel_formacion_padre' => $request->input('nivel_formacion_padre'),
            'nivel_formacion_madre' => $request->input('nivel_formacion_madre'),
            'origen_recursos_estudios' => $request->input('origen_recursos_estudios'),

            // Documentos
            'pdf_cedula' => $request->input('pdf_cedula'),
            'pdf_papelvotacion' => $request->input('pdf_papelvotacion'),
            'pdf_titulouniversidad' => $request->input('pdf_titulouniversidad'),
            'pdf_conadis' => $request->input('pdf_conadis'),
            'pdf_hojavida' => $request->input('pdf_hojavida'),
            'carta_aceptacion' => $request->input('carta_aceptacion'),

            // Estado y relación
            'maestria_id' => $request->input('maestria_id'),
            'status' => 0,
            'pago_matricula' => $request->input('pago_matricula'),
            'monto_matricula' => $montoMatricula,
            'monto_inscripcion' => $montoInscripcion,
        ]);
        $postulante->save();

        // Crear el usuario asociado
        $usuario = new User();
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo') === 'HOMBRE' ? 'M' : 'F';
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('correo_electronico');
        $usuario->image = $imagenPath;
        $usuario->save();

        // Asignar rol y notificar al usuario
        $usuario->assignRole('Postulante');

        Notification::route('mail', $usuario->email)
            ->notify(new NuevoUsuarioNotification($usuario, $request->input('dni'), $usuario->name));

        Auth::login($usuario);
        return redirect()->route('inicio')->with('success', 'Postulación realizada exitosamente.');
    }

    public function edit()
    {
        $userEmail = Auth::user()->email;

        $postulante = Postulante::where('correo_electronico', $userEmail)->firstOrFail();

        $maestrias = Maestria::where('status', 'ACTIVO')->get();
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        $tipo_colegio = ['FISCAL', 'FISCOMISIONAL', 'PARTICULAR', 'MUNICIPAL', 'EXTRANJERO', 'NO REGISTRA'];
        $ingreso_hogar = ['RANGO 1 - HASTA 1 SBU', 'RANGO 2 - MÁS DE 1 A MENOS DE 2 SBU', 'RANGO 3 - MÁS DE 2 A MENOS DE 3 SBU', 'RANGO 4 - MÁS DE 3 A MENOS DE 4 SBU', 'RANGO 5 - MÁS DE 4 A MENOS DE 5 SBU', 'RANGO 6 - MÁS DE 5 A MENOS DE 6 SBU', 'RANGO 7 - MÁS DE 6 A MENOS DE 7 SBU', 'RANGO 8 - MÁS DE 7 A MENOS DE 8 SBU', 'RANGO 9 - MÁS DE 8 A MENOS DE 9 SBU', 'RANGO 10-DE 9 EN ADELANTE', 'NO REGISTRA'];
        $formacion_padre = ['NINGUNO', 'CENTRO DE ALFABETIZACIÓN', 'JARDIN INFANTES', 'EDUCACIÓN BÁSICA', 'EDUCACIÓN MEDIA', 'SUPERIOR NO UNIVERSITARIA COMPLETA', 'SUPERIOR NO UNIVERSITARIA INCOMPLETA', 'SUPERIOR UNIVERSITARIA COMPLETA', 'SUPERIOR UNIVERSITARIA INCOMPLETA', 'DIPLOMADO', 'ESPECIALIDAD', 'POSGRADO MAESTRÍA', 'POSGRADO ESPECIALIDAD ÁREA SALUD', 'POSGRADO PHD', 'NO SABE', 'NO REGISTRA'];
        $origen_recursos = ['RECURSOS PROPIOS', 'PADRES TUTORES', 'PAREJA SENTIMENTAL', 'HERMANOS', 'OTROS MIEMBROS DEL HOGAR', 'OTROS FAMILIARES', 'BECA ESTUDIO', 'CRÉDITO EDUCATIVO', 'NO REGISTRA'];

        return view('postulantes.edit', compact(
            'postulante',
            'maestrias',
            'provincias',
            'tipo_colegio',
            'ingreso_hogar',
            'formacion_padre',
            'origen_recursos'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $postulante = Postulante::where('correo_electronico', $user->email)->firstOrFail();

        // Detectar cambios reales
        $dniCambiado = $request->dni !== $postulante->dni;
        $correoCambiado = $request->correo_electronico !== $postulante->correo_electronico;

        // Validación condicional
        $request->validate([
            'dni' => array_filter([
                'required',
                $dniCambiado ? Rule::unique('postulantes', 'dni')->ignore($postulante->id) : null,
            ]),
            'correo_electronico' => array_filter([
                'required',
                'email',
                $correoCambiado ? Rule::unique('postulantes', 'correo_electronico')->ignore($postulante->id) : null,
            ]),
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Imagen
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes_usuarios', 'public');
            $postulante->imagen = $imagenPath;
            $user->image = $imagenPath;
        }

        $postulante->fill($request->except('imagen'));
        $postulante->save();

        // Sincronizar con tabla users si hay cambios
        if ($correoCambiado) {
            $user->email = $request->correo_electronico;
        }

        $user->name = $request->input('nombre1');
        $user->apellido = $request->input('apellidop');
        $user->sexo = $request->input('sexo') === 'HOMBRE' ? 'M' : 'F';
        $user->save();

        return redirect()->route('postulaciones.edit')->with('success', 'Postulación actualizada correctamente.');
    }

    public function show($dni)
    {
        $postulante = Postulante::findOrFail($dni);

        return view('postulantes.show', compact('postulante'));
    }
    public function destroy($dni)
    {
        $postulante = Postulante::findOrFail($dni);

        $user = User::where('name', $postulante->nombre1)
            ->where('apellido', $postulante->apellidop)
            ->where('email', $postulante->correo_electronico)
            ->first();

        if ($user) {
            $user->delete();
        }

        $postulante->delete();

        return redirect()->route('postulaciones.index')->with('success', 'Postulante y usuario eliminado exitosamente.');
    }

    public function acep_neg($dni)
    {
        $postulante = Postulante::findOrFail($dni);
        $postulante->status = true;
        $postulante->save();

        $usuario = User::where('name', $postulante->nombre1)
            ->where('apellido', $postulante->apellidop)
            ->where('email', $postulante->correo_electronico)
            ->first();

        if ($usuario) {

            $usuario->notify(new PostulanteAceptadoNotification($postulante));
            return redirect()->back()->with('message', 'Postulante aceptado y notificación enviada.');
        } else {
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }
    }


    public function convertirEnEstudiante($dni)
    {
        $postulante = Postulante::find($dni);

        if (!$postulante) {
            return redirect()->back()->with('error', 'El postulante no existe.');
        }

        if (!$postulante->status) {
            return redirect()->back()->with('error', 'El postulante no puede ser convertido en estudiante.');
        }

        // Crear email institucional
        $email_institucional = strtolower($postulante->apellidop) . '-' . strtolower($postulante->nombre1) . substr($postulante->dni, -4) . '@unesum.edu.ec';

        // Directorio de PDFs
        $rutaDirectorio = 'public/alumnos/pdf';
        if (!Storage::exists($rutaDirectorio)) {
            Storage::makeDirectory($rutaDirectorio);
        }

        // Crear el alumno con todos los campos
        $estudiante = Alumno::create([
            'dni' => $postulante->dni,
            'nombre1' => $postulante->nombre1,
            'nombre2' => $postulante->nombre2,
            'apellidop' => $postulante->apellidop,
            'apellidom' => $postulante->apellidom,
            'email_personal' => $postulante->correo_electronico,
            'email_institucional' => $email_institucional,
            'estado_civil' => $postulante->estado_civil,
            'fecha_nacimiento' => $postulante->fecha_nacimiento,
            'provincia' => $postulante->provincia,
            'canton' => $postulante->canton,
            'barrio' => $postulante->barrio,
            'direccion' => $postulante->direccion,
            'nacionalidad' => $postulante->nacionalidad,
            'etnia' => $postulante->etnia,
            'carnet_discapacidad' => $postulante->carnet_discapacidad,
            'tipo_discapacidad' => $postulante->tipo_discapacidad,
            'porcentaje_discapacidad' => $postulante->porcentaje_discapacidad,
            'contra' => bcrypt($postulante->dni),
            'image' => $postulante->imagen,
            'celular' => $postulante->celular,
            'titulo_profesional' => $postulante->titulo_profesional,
            'universidad_titulo' => $postulante->universidad_titulo,
            'tipo_colegio' => $postulante->tipo_colegio,
            'cantidad_miembros_hogar' => $postulante->cantidad_miembros_hogar,
            'ingreso_total_hogar' => $postulante->ingreso_total_hogar,
            'nivel_formacion_padre' => $postulante->nivel_formacion_padre,
            'nivel_formacion_madre' => $postulante->nivel_formacion_madre,
            'origen_recursos_estudios' => $postulante->origen_recursos_estudios,
            'sexo' => $postulante->sexo,
            'pdf_cedula' => $postulante->pdf_cedula,
            'pdf_papelvotacion' => $postulante->pdf_papelvotacion,
            'pdf_titulouniversidad' => $postulante->pdf_titulouniversidad,
            'pdf_conadis' => $postulante->pdf_conadis,
            'pdf_hojavida' => $postulante->pdf_hojavida,
            'carta_aceptacion' => $postulante->carta_aceptacion,
            'telefono_convencional' => $postulante->telefono_convencional,
            'edad' => $postulante->edad,
            'tipo_sangre' => $postulante->tipo_sangre,
            'anios_residencia' => $postulante->anios_residencia,
            'libreta_militar' => $postulante->libreta_militar,
            'pais_residencia' => $postulante->pais_residencia,
            'parroquia' => $postulante->parroquia,
            'calle_principal' => $postulante->calle_principal,
            'numero_direccion' => $postulante->numero_direccion,
            'calle_secundaria' => $postulante->calle_secundaria,
            'referencia_direccion' => $postulante->referencia_direccion,
            'telefono_domicilio' => $postulante->telefono_domicilio,
            'celular_residencia' => $postulante->celular_residencia,
            'contacto_apellidos' => $postulante->contacto_apellidos,
            'contacto_nombres' => $postulante->contacto_nombres,
            'contacto_parentesco' => $postulante->contacto_parentesco,
            'contacto_telefono' => $postulante->contacto_telefono,
            'contacto_celular' => $postulante->contacto_celular,
            'especialidad_bachillerato' => $postulante->especialidad_bachillerato,
            'colegio_bachillerato' => $postulante->colegio_bachillerato,
            'ciudad_bachillerato' => $postulante->ciudad_bachillerato,
            'especialidad_mencion' => $postulante->especialidad_mencion,
            'ciudad_universidad' => $postulante->ciudad_universidad,
            'pais_universidad' => $postulante->pais_universidad,
            'registro_senescyt' => $postulante->registro_senescyt,
            'titulo_posgrado' => $postulante->titulo_posgrado,
            'denominacion_posgrado' => $postulante->denominacion_posgrado,
            'universidad_posgrado' => $postulante->universidad_posgrado,
            'ciudad_posgrado' => $postulante->ciudad_posgrado,
            'pais_posgrado' => $postulante->pais_posgrado,
            'lugar_trabajo' => $postulante->lugar_trabajo,
            'funcion_laboral' => $postulante->funcion_laboral,
            'ciudad_trabajo' => $postulante->ciudad_trabajo,
            'direccion_trabajo' => $postulante->direccion_trabajo,
            'telefono_trabajo' => $postulante->telefono_trabajo,
        ]);

        $estudiante->maestrias()->attach($postulante->maestria_id);

        // Guardar los montos en alumno_maestria_monto
        $estudiante->montos()->attach($postulante->maestria_id, [
            'monto_arancel' => $postulante->maestria->arancel ?? 0,
            'monto_matricula' => $postulante->monto_matricula ?? 0,
            'monto_inscripcion' => $postulante->monto_inscripcion ?? 0,
        ]);

        // Actualizar usuario y roles
        if ($usuario) {
            $usuario->assignRole('Alumno');
            $usuario->removeRole('Postulante');
            $usuario->email = $email_institucional;
            $usuario->save();

            DB::table('sessions')->where('user_id', $usuario->id)->delete();

            Notification::route('mail', $usuario->email)
                ->notify(new MatriculaExito($usuario, $email_institucional, $usuario->name, $postulante->dni, $usuario->id));
        }

        // Eliminar postulante
        $postulante->delete();

        return redirect()->back()->with('success', 'El postulante ha sido convertido en estudiante con maestría y montos asignados.');
    }

    public function fichaInscripcionPdf($dni)
    {
        $postulante = Postulante::findOrFail($dni);

        // Buscar coordinador de la maestría
        $coordinadorDni = $postulante->maestria->coordinador ?? null;
        $coordinador = $coordinadorDni ? \App\Models\Docente::where('dni', $coordinadorDni)->first() : null;
        $nombreCompleto = $coordinador ? $coordinador->getFullNameAttribute() : 'Coordinador no encontrado';

        // Buscar director del instituto de posgrado
        $directorUser = \App\Models\User::role('director')->first();
        $directorDocente = $directorUser ? \App\Models\Docente::where('email', $directorUser->email)->first() : null;

        // Buscar secretario académico (opcional, si aplica)
        $secretario = null;
        if ($postulante->maestria && $postulante->maestria->secciones->first()) {
            $seccion = $postulante->maestria->secciones->first();
            $secretario = \App\Models\Secretario::where('seccion_id', $seccion->id)->first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('postulantes.ficha_incripcion_pdf', compact(
            'postulante',
            'nombreCompleto',
            'directorDocente',
            'secretario'
        ));

        return $pdf->stream('ficha_inscripcion_' . $postulante->dni . '.pdf');
    }
}
