<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Retiro;
use App\Models\Docente;
use App\Models\Secretario;
use App\Models\TasaTitulacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function subirFichaSocioeconomica(Request $request, $id)
    {
        try {
            $request->validate([
                'ficha_socioeconomica' => 'required|mimes:pdf,doc,docx|max:2048', // máximo 2MB
            ]);

            $alumno = Alumno::findOrFail($id);

            $file = $request->file('ficha_socioeconomica');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ficha_' . Str::slug($alumno->nombre1 . '_' . $alumno->apellidop) . '_' . time() . '.' . $extension;

            $ruta = $file->storeAs('private/ficha_socioeconomica', $filename);

            $alumno->ficha_socioeconomica = $ruta;
            $alumno->save();

            return back()->with('success', 'Ficha socioeconómica subida correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al subir la ficha socioeconómica: ' . $e->getMessage());

            return back()->with('error', 'Ocurrió un error al subir la ficha socioeconómica. Por favor, inténtalo nuevamente.');
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();

            // Filtrar los alumnos según el rol del usuario
            if ($user->hasRole('Administrador')) {
                $query = Alumno::with('maestria', 'matriculas.asignatura', 'matriculas.docente', 'matriculas.cohorte.aula')
                    ->withCount('matriculas')
                    ->orderBy('matriculas_count')
                    ->orderBy('created_at', 'desc');
            } elseif ($user->hasRole('Secretario')) {
                $secretario = Secretario::where('nombre1', $user->name)
                    ->where('apellidop', $user->apellido)
                    ->where('email', $user->email)
                    ->firstOrFail();

                $maestriasIds = $secretario->seccion->maestrias->pluck('id');
                $query = Alumno::with('maestria', 'matriculas.asignatura', 'matriculas.docente', 'matriculas.cohorte.aula')
                    ->withCount('matriculas')
                    ->whereIn('maestria_id', $maestriasIds)
                    ->orderBy('matriculas_count')
                    ->orderBy('created_at', 'desc');
            } elseif ($user->hasRole('Coordinador')) {
                $docente = Docente::where('nombre1', $user->name)
                    ->where('apellidop', $user->apellido)
                    ->where('email', $user->email)
                    ->firstOrFail();

                $maestria = $docente->maestria->first();
                if (!$maestria) {
                    abort(403, 'El coordinador no tiene ninguna maestría asignada.');
                }

                $query = Alumno::with('maestria', 'matriculas.asignatura', 'matriculas.docente', 'matriculas.cohorte.aula')
                    ->withCount('matriculas')
                    ->where('maestria_id', $maestria->id)
                    ->orderBy('matriculas_count')
                    ->orderBy('created_at', 'desc');
            } else {
                abort(403, 'No autorizado');
            }

            // Configurar DataTables
            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestria->nombre ?? 'Sin Maestría';
                })
                ->addColumn('foto', function ($alumno) {
                    return '<img src="' . asset('storage/' . $alumno->image) . '" alt="Foto de ' . $alumno->nombre1 . '" class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return trim("{$alumno->nombre1} {$alumno->nombre2} {$alumno->apellidop} {$alumno->apellidom}");
                })
                ->filterColumn('nombre_completo', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(nombre1, ' ', nombre2, ' ', apellidop, ' ', apellidom) like ?", ["%{$keyword}%"]);
                })
                ->orderColumn('nombre_completo', function ($query, $order) {
                    $query->orderByRaw("CONCAT(nombre1, ' ', nombre2, ' ', apellidop, ' ', apellidom) {$order}");
                })
                ->addColumn('acciones', function ($alumno) {
                    $acciones = '<div style="display: flex; gap: 10px; align-items: center;">';

                    if ($alumno->ficha_socioeconomica) {
                        $acciones .= '<a href="' . route('alumnos.ficha.ver', $alumno->dni) . '" target="_blank" class="btn btn-outline-secondary btn-sm" title="Ver ficha socioeconómica">
                                        <i class="fas fa-file-word"></i>
                                    </a>';
                    }

                    
                    if ($alumno->maestria && $alumno->maestria->cohortes && $alumno->matriculas->isEmpty()) {
                        $acciones .= '<a href="' . url('/matriculas/create', $alumno->dni) . '" class="btn btn-outline-success btn-sm" title="Matricular"><i class="fas fa-plus-circle"></i></a>';
                    }

                    if ($alumno->matriculas->count() > 0) {
                        $acciones .= '<button type="button" class="btn btn-outline-info btn-sm view-matriculas" 
                        data-id="' . $alumno->dni . '" 
                        data-matriculas=\'' . json_encode($alumno->matriculas->map(function ($matricula) {
                            return [
                                'asignatura' => $matricula->asignatura->nombre ?? 'No disponible',
                                'docente' => $matricula->docente
                                    ? $matricula->docente->nombre1 . ' ' . $matricula->docente->apellidop
                                    : 'No disponible',
                                'cohorte' => $matricula->cohorte->nombre ?? 'No disponible',
                                'aula' => $matricula->cohorte->aula->nombre ?? 'No disponible',
                                'paralelo' => $matricula->cohorte->aula->paralelo ?? 'No disponible',
                            ];
                        })) . '\' title="Ver Matrícula">
                        <i class="fas fa-eye"></i>
                    </button>';

                        $acciones .= '<a href="' . route('certificado.matricula', $alumno->dni) . '" target="_blank" class="btn btn-outline-danger btn-sm" title="Certificado de Matrícula"><i class="fas fa-file-pdf"></i></a>';
                        $acciones .= '<a href="' . route('record.show', $alumno->dni) . '" class="btn btn-outline-warning btn-sm" title="Record Académico" target="_blank"><i class="fas fa-file-alt"></i></a>';
                        $acciones .= '<a href="' . route('certificado', $alumno->dni) . '" target="_blank" class="btn btn-outline-info btn-sm" title="Certificado"><i class="fas fa-file-pdf"></i></a>';
                        $acciones .= '<a href="' . route('certificado_culminacion', $alumno->dni) . '" target="_blank" class="btn btn-outline-success btn-sm" title="Certificado de Culminación"><i class="fas fa-file-pdf"></i></a>';
                    }

                    if (auth()->user()->can('dashboard_admin') && $alumno->matriculas->count() > 0) {
                        $acciones .= '<a href="' . url('/notas/create', $alumno->dni) . '" class="btn btn-outline-info btn-sm" title="Calificar"><i class="fas fa-pencil-alt"></i></a>';
                    }

                    if (auth()->user()->can('dashboard_admin')) {
                        $acciones .= '<a href="' . route('alumnos.edit', $alumno->dni) . '" class="btn btn-outline-primary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>';
                    }

                    $acciones .= '</div>';
                    return $acciones;
                })
                ->rawColumns(['foto', 'acciones'])
                ->make(true);
        }

        return view('alumnos.index');
    }

    public function create()
    {
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        $user = auth()->user();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)->get();
        } else {
            $maestrias = Maestria::all(); // Obtener todas las maestrías sin filtrar por estado
        }

        return view('alumnos.create', compact('provincias', 'maestrias'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'maestria_id' => 'required|exists:maestrias,id',
                'image' => 'nullable|image|max:2048',
                'dni' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('alumnos', 'dni'),
                    Rule::unique('postulantes', 'dni'),
                ],
                'email_institucional' => [
                    'required',
                    'email',
                    Rule::notIn(User::pluck('email')->toArray()),
                ],
            ], [
                // Mensajes personalizados
                'maestria_id.required' => 'La maestría es obligatoria.',
                'maestria_id.exists' => 'La maestría seleccionada no existe.',
                'image.image' => 'El archivo debe ser una imagen.',
                'image.max' => 'La imagen no puede superar los 2MB.',
                'dni.required' => 'El DNI es obligatorio.',
                'dni.unique' => 'Este DNI ya está registrado.',
                'email_institucional.required' => 'El correo institucional es obligatorio.',
                'email_institucional.email' => 'El correo institucional debe tener un formato válido.',
                'email_institucional.not_in' => 'Este correo institucional ya está en uso.',
            ]);

            $maestria = Maestria::findOrFail($request->input('maestria_id'));
            $arancel = $maestria->arancel ?? 0;
            $inscripcion = $maestria->incripcion ?? 0;
            $matricula = $maestria->matricula ?? 0;
            $nuevoRegistro = Alumno::where('maestria_id', $maestria->id)->count() + 1;

            $alumno = new Alumno();
            $alumno->fill($request->only([
                'nombre1', 'nombre2', 'apellidop', 'apellidom',
                'sexo', 'dni', 'email_institucional', 'email_personal',
                'estado_civil', 'fecha_nacimiento', 'provincia', 'canton',
                'barrio', 'direccion', 'nacionalidad', 'etnia',
                'carnet_discapacidad', 'tipo_discapacidad', 'porcentaje_discapacidad'
            ]));

            $alumno->contra = bcrypt($request->input('dni'));
            $alumno->maestria_id = $maestria->id;
            $alumno->registro = $nuevoRegistro;
            $alumno->monto_total = $arancel;
            $alumno->monto_inscripcion = $inscripcion;
            $alumno->monto_matricula = $matricula;

            if ($request->hasFile('image')) {
                $alumno->image = $request->file('image')->store('imagenes_usuarios', 'public');
            } else {
                $alumno->image = 'https://ui-avatars.com/api/?name=' . urlencode(substr($alumno->nombre1, 0, 1));
            }

            $alumno->save();

            $usuario = new User();
            $usuario->fill([
                'name' => $alumno->nombre1,
                'apellido' => $alumno->apellidop,
                'sexo' => $alumno->sexo,
                'email' => $alumno->email_institucional,
                'status' => $request->input('estatus', 'ACTIVO'),
                'image' => $alumno->image,
            ]);
            $usuario->password = bcrypt($alumno->dni);
            $usuario->save();

            $usuario->assignRole(Role::findById(4));

            return redirect()->route('alumnos.index')->with('success', 'Usuario creado exitosamente.');

        } catch (ValidationException $e) {
            // Devuelve errores de validación de vuelta al formulario con mensajes
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Error general
            Log::error('Error al crear alumno: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error inesperado al crear el usuario. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function edit($dni)
    {
        $maestrias = Maestria::where('status', 'ACTIVO')->get();
        $alumno = Alumno::where('dni', $dni)->firstOrFail();
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        return view('alumnos.edit', compact('alumno', 'provincias', 'maestrias'));
    }

    public function update(Request $request, $dni)
    {

        try {
            $maestria = Maestria::findOrFail($request->input('maestria_id'));
            $arancel = $maestria->arancel;

            $alumno = Alumno::where('dni', $dni)->firstOrFail();

            $alumno->fill($request->only([
                'nombre1', 'nombre2', 'apellidop', 'apellidom', 'dni',
                'estado_civil', 'fecha_nacimiento', 'provincia', 'canton',
                'barrio', 'direccion', 'nacionalidad', 'etnia',
                'email_personal', 'carnet_discapacidad', 'tipo_discapacidad',
                'porcentaje_discapacidad', 'sexo', 'maestria_id'
            ]));

            $alumno->monto_total = $arancel;

            // Procesar imagen si se sube
            if ($request->hasFile('image')) {
                if ($alumno->image) {
                    Storage::disk('public')->delete($alumno->image);
                }

                $path = $request->file('image')->store('imagenes_usuarios', 'public');
                $alumno->image = $path;

                // También actualiza imagen del usuario asociado
                $usuario = User::where('email', $alumno->email_institucional)->first();
                if ($usuario) {
                    $usuario->image = $path;
                    $usuario->save();
                }
            }

            $alumno->save();

            return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el alumno: ' . $e->getMessage());
        }
    }

    public function retirarse(Request $request, $dni)
    {
        try {
            $request->validate([
                'retiro_documento' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'alumno_dni' => 'required|exists:alumnos,dni',
            ]);

            $alumno = Alumno::where('dni', $dni)->firstOrFail();

            $path = $request->file('retiro_documento')->store('retiros', 'public');


            Retiro::create([
                'alumno_dni' => $request->input('alumno_dni'),
                'documento_path' => $path,
                'fecha_retiro' => now(),
            ]);

            $usuario = User::where('email', $alumno->email_institucional)->firstOrFail();
            $usuario->status = 'INACTIVO';
            $usuario->save();

            $matricula = $alumno->matriculas()->first();
            if (!$matricula) {
                return redirect()->back()->with('error', 'Matrícula no encontrada');
            }

            // Obtener el cohorte y la maestría
            $cohorteId = $matricula->cohorte_id;
            $maestriaId = $alumno->maestria_id;

            // Buscar o crear la tasa de titulación para el cohorte y la maestría
            $tasaTitulacion = TasaTitulacion::where('cohorte_id', $cohorteId)
                ->where('maestria_id', $maestriaId)
                ->first();

            if ($tasaTitulacion) {
                $tasaTitulacion->retirados += 1;
                $tasaTitulacion->save();
            } else {
                // Si no existe, lo creamos con valores iniciales
                TasaTitulacion::create([
                    'cohorte_id' => $cohorteId,
                    'maestria_id' => $maestriaId,
                    'retirados' => 1,  // Iniciamos con 1 graduado
                ]);
            }

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('success', 'Solicitud de retiro enviada con éxito.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
