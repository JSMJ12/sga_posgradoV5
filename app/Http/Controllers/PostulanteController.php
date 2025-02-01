<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Postulante;
use App\Models\Maestria;
use App\Models\Secretario;
use App\Models\Alumno;
use App\Notifications\MatriculaExito;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NuevoUsuarioNotification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PostulanteAceptadoNotification;

class PostulanteController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $user = auth()->user();

        if ($user->hasRole('Administrador')) {
            $postulantes = Postulante::with('documentos_verificados')->get();
        } else {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $postulantes = Postulante::whereIn('maestria_id', $maestriasIds)
                ->with('documentos_verificados')
                ->get();
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
            'dni' => 'required|unique:postulantes',
            'correo_electronico' => 'required|email|unique:postulantes',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
            'dni' => $request->input('dni'),
            'apellidop' => $request->input('apellidop'),
            'apellidom' => $request->input('apellidom'),
            'nombre1' => $request->input('nombre1'),
            'nombre2' => $request->input('nombre2'),
            'correo_electronico' => $request->input('correo_electronico'),
            'celular' => $request->input('celular'),
            'titulo_profesional' => $request->input('titulo_profesional'),
            'universidad_titulo' => $request->input('universidad_titulo'),
            'sexo' => $request->input('sexo'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'nacionalidad' => $request->input('nacionalidad'),
            'discapacidad' => $request->input('discapacidad'),
            'porcentaje_discapacidad' => $request->input('porcentaje_discapacidad'),
            'codigo_conadis' => $request->input('codigo_conadis'),
            'provincia' => $request->input('provincia'),
            'etnia' => $request->input('etnia'),
            'nacionalidad_indigena' => $request->input('nacionalidad_indigena'),
            'canton' => $request->input('canton'),
            'direccion' => $request->input('direccion'),
            'tipo_colegio' => $request->input('tipo_colegio'),
            'cantidad_miembros_hogar' => $request->input('cantidad_miembros_hogar'),
            'ingreso_total_hogar' => $request->input('ingreso_total_hogar'),
            'nivel_formacion_padre' => $request->input('nivel_formacion_padre'),
            'nivel_formacion_madre' => $request->input('nivel_formacion_madre'),
            'origen_recursos_estudios' => $request->input('origen_recursos_estudios'),
            'maestria_id' => $request->input('maestria_id'),
            'imagen' => $imagenPath,
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

        return redirect()->route('postulantes.index')->with('success', 'Postulante y usuario eliminado exitosamente.');
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

        $rutaDirectorio = 'public/alumnos/pdf';
        if (!Storage::exists($rutaDirectorio)) {
            Storage::makeDirectory($rutaDirectorio);
        }

        $pdf_cedula_path = $postulante->pdf_cedula;
        $pdf_papelvotacion_path = $postulante->pdf_papelvotacion;
        $pdf_titulouniversidad_path = $postulante->pdf_titulouniversidad;
        $pdf_conadis_path = $postulante->pdf_conadis;
        $pdf_hojavida_path = $postulante->pdf_hojavida;
        $carta_aceptacion_path = $postulante->carta_aceptacion;
        $pago_matricula_path = $postulante->pago_matricula;

        if ($postulante->status) {
            $email_institucional = strtolower($postulante->apellidop) . '-' . strtolower($postulante->nombre1) . substr($postulante->dni, -4) . '@unesum.edu.ec';

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
                'maestria_id' => $postulante->maestria_id,
                'celular' => $postulante->celular,
                'titulo_profesional' => $postulante->titulo_profesional,
                'universidad_titulo' => $postulante->universidad_titulo,
                'tipo_colegio' => $postulante->tipo_colegio,
                'cantidad_miembros_hogar' => $postulante->cantidad_miembros_hogar,
                'ingreso_total_hogar' => $postulante->ingreso_total_hogar,
                'nivel_formacion_padre' => $postulante->nivel_formacion_padre,
                'nivel_formacion_madre' => $postulante->nivel_formacion_madre,
                'origen_recursos_estudios' => $postulante->origen_recursos_estudios,
                'sexo'  => $postulante->sexo,
                'pdf_cedula' => $pdf_cedula_path,
                'pdf_papelvotacion' => $pdf_papelvotacion_path,
                'pdf_titulouniversidad' => $pdf_titulouniversidad_path,
                'pdf_conadis' => $pdf_conadis_path,
                'pdf_hojavida' => $pdf_hojavida_path,
                'carta_aceptacion' => $carta_aceptacion_path,
                'pago_matricula' => $pago_matricula_path,
                'monto_total' => $postulante->maestria->arancel,
            ]);

            $user = User::where('name', $postulante->nombre1)
                ->where('apellido', $postulante->apellidop)
                ->where('email', $postulante->correo_electronico)
                ->first();

            if ($user) {
                \Log::info('Usuario encontrado', ['usuario' => $user->toArray()]);
                $user->assignRole('Alumno');
                $user->removeRole('Postulante');

                $user->email = $email_institucional;
                $user->save();

                Notification::route('mail', $user->email)
                    ->notify(new MatriculaExito($user, $email_institucional, $user->name, $postulante->dni));
            }

            $postulante->delete();
            return redirect()->back()->with('success', 'El postulante ha sido convertido en estudiante.');
        }

        return redirect()->back()->with('error', 'El postulante no puede ser convertido en estudiante.');
    }
}
