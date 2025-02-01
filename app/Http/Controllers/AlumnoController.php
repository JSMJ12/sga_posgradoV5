<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Maestria;
use App\Models\Retiro;
use App\Models\Secretario;
use App\Models\TasaTitulacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Verificar si la solicitud es AJAX para DataTables
        if ($request->ajax()) {
            $user = auth()->user();

            // Filtrar los alumnos según el rol del usuario
            if ($user->hasRole('Administrador')) {
                $query = Alumno::with('maestria');
            } else {
                $secretario = Secretario::where('nombre1', $user->name)
                    ->where('apellidop', $user->apellido)
                    ->where('email', $user->email)
                    ->firstOrFail();
                $maestriasIds = $secretario->seccion->maestrias->pluck('id');
                $query = Alumno::whereIn('maestria_id', $maestriasIds);
            }

            // Configurar DataTables con las columnas necesarias
            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestria ? $alumno->maestria->nombre : 'Sin Maestría';
                })
                ->addColumn('foto', function ($alumno) {
                    return '<img src="' . asset('storage/' . $alumno->image) . '" alt="Foto de ' . $alumno->nombre1 . '" class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return "{$alumno->nombre1}<br>{$alumno->nombre2}<br>{$alumno->apellidop}<br>{$alumno->apellidom}";
                })
                ->addColumn('acciones', function ($alumno) {
                    $acciones = '<div style="display: flex; gap: 10px; align-items: center;">';

                    // Botón Ver Matrícula
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

                    // Botón Editar (solo para administradores)
                    if (auth()->user()->can('dashboard_admin')) {
                        $acciones .= '<a href="' . route('alumnos.edit', $alumno->dni) . '" class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                  </a>';
                    }

                    // Botón Matricular
                    if ($alumno->maestria && $alumno->maestria->cohortes && $alumno->matriculas->count() == 0) {
                        $acciones .= '<a href="' . url('/matriculas/create', $alumno->dni) . '" class="btn btn-outline-success btn-sm" title="Matricular">
                                        <i class="fas fa-plus-circle"></i>
                                      </a>';
                    }

                    // Botón Record Académico
                    if ($alumno->notas->count() > 0 && $alumno->maestria->asignaturas->count() > 0 && $alumno->notas->count() == $alumno->maestria->asignaturas->count()) {

                        $acciones .= '<a href="' . route('record.show', $alumno->dni) . '" class="btn btn-outline-warning btn-sm" title="Record Académico" target="_blank">
                                        <i class="fas fa-file-alt"></i>
                                      </a>';
                    }

                    // Botón Calificar (solo para administradores)
                    if (auth()->user()->can('dashboard_admin')) {
                        $acciones .= '<a href="' . url('/notas/create', $alumno->dni) . '" class="btn btn-outline-info btn-sm" title="Calificar">
                                    <i class="fas fa-pencil-alt"></i>
                                  </a>';
                    }

                    $acciones .= '</div>';
                    return $acciones;
                })
                ->rawColumns(['foto', 'acciones', 'nombre_completo']) // Permitir HTML en estas columnas
                ->toJson();
        }

        // Retornar la vista si no es una solicitud AJAX
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
            $maestrias = Maestria::whereIn('id', $maestriasIds)
                ->where('status', 'ACTIVO')
                ->get();
        } else {
            $maestrias = Maestria::where('status', 'ACTIVO')->get();
        }

        return view('alumnos.create', compact('provincias', 'maestrias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maestria_id' => 'required|exists:maestrias,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // Obtener el ID de la maestría
        $maestriaId = $request->input('maestria_id');

        // Obtener la maestría y su arancel
        $maestria = Maestria::findOrFail($maestriaId);
        $arancel = $maestria->arancel;

        // Obtener el próximo número de registro
        $nuevoRegistro = Alumno::where('maestria_id', $maestriaId)->count() + 1;

        // Crear un nuevo objeto Alumno
        $alumno = new Alumno;
        $alumno->nombre1 = $request->input('nombre1');
        $alumno->nombre2 = $request->input('nombre2');
        $alumno->apellidop = $request->input('apellidop');
        $alumno->apellidom = $request->input('apellidom');
        $alumno->contra = bcrypt($request->input('dni')); // Encriptar la contraseña
        $alumno->sexo = $request->input('sexo');
        $alumno->dni = $request->input('dni');
        $alumno->email_institucional = $request->input('email_ins');
        $alumno->email_personal = $request->input('email_per');
        $alumno->estado_civil = $request->input('estado_civil');
        $alumno->fecha_nacimiento = $request->input('fecha_nacimiento');
        $alumno->provincia = $request->input('provincia');
        $alumno->canton = $request->input('canton');
        $alumno->barrio = $request->input('barrio');
        $alumno->direccion = $request->input('direccion');
        $alumno->nacionalidad = $request->input('nacionalidad');
        $alumno->etnia = $request->input('etnia');
        $alumno->carnet_discapacidad = $request->input('carnet_discapacidad');
        $alumno->tipo_discapacidad = $request->input('tipo_discapacidad');
        $alumno->maestria_id = $request->input('maestria_id');
        $alumno->porcentaje_discapacidad = $request->input('porcentaje_discapacidad');
        $alumno->registro = $nuevoRegistro;
        $alumno->monto_total = $arancel; // Asignar el valor del arancel

        // Procesar la imagen
        $primeraLetra = substr($alumno->nombre1, 0, 1);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $alumno->image = $path;
        } else {
            $alumno->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }

        if (!$alumno->registro) {
            $alumno->registro = Alumno::getNextRegistro();
        }
        // Almacenar el alumno
        $alumno->save();

        // Crear un nuevo objeto User
        $usuario = new User;
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('email_ins');
        $usuario->image = $alumno->image;
        $alumnoRole = Role::findById(4);
        $usuario->assignRole($alumnoRole);
        $usuario->save();

        return redirect()->route('alumnos.index')->with('success', 'Usuario creado exitosamente.');
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
        // Obtener el ID de la maestría
        $maestriaId = $request->input('maestria_id');

        // Obtener la maestría y su arancel
        $maestria = Maestria::findOrFail($maestriaId);
        $arancel = $maestria->arancel;
        $alumno = Alumno::where('dni', $dni)->firstOrFail();
        $alumno->nombre1 = $request->input('nombre1');
        $alumno->nombre2 = $request->input('nombre2');
        $alumno->apellidop = $request->input('apellidop');
        $alumno->apellidom = $request->input('apellidom');
        $alumno->dni = $request->input('dni');
        $alumno->estado_civil = $request->input('estado_civil');
        $alumno->fecha_nacimiento = $request->input('fecha_nacimiento');
        $alumno->provincia = $request->input('provincia');
        $alumno->canton = $request->input('canton');
        $alumno->barrio = $request->input('barrio');
        $alumno->direccion = $request->input('direccion');
        $alumno->nacionalidad = $request->input('nacionalidad');
        $alumno->etnia = $request->input('etnia');
        $alumno->email_personal = $request->input('email_personal');
        $alumno->carnet_discapacidad = $request->input('carnet_discapacidad');
        $alumno->tipo_discapacidad = $request->input('tipo_discapacidad');
        $alumno->porcentaje_discapacidad = $request->input('porcentaje_discapacidad');
        $alumno->sexo = $request->input('sexo');
        $alumno->maestria_id = $request->input('maestria_id');
        $alumno->monto_total = $arancel;
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($alumno->image) {
                Storage::disk('public')->delete($alumno->image);
            }

            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $alumno->image = $path;
            $usuario = User::where('email', $alumno->email_institucional)->firstOrFail();

            $usuario->image = $alumno->image;
            $usuario->save();
        }
        $alumno->save();

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente');
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
