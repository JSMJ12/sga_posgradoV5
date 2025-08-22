<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\CalificacionVerificacion;
use App\Models\Cohorte;
use App\Models\CohorteDocente;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        $docentes = Docente::query();

        if ($user->hasRole('Coordinador')) {
            $coordinador = Docente::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();

            $asignaturaIds = $coordinador->maestria()
                ->with('asignaturas')
                ->get()
                ->pluck('asignaturas')
                ->flatten()
                ->pluck('id')
                ->unique();
            $docentes = $docentes->whereHas('asignaturas', function ($query) use ($asignaturaIds) {
                $query->whereIn('asignatura_id', $asignaturaIds);
            });
        }

        if ($request->ajax()) {
            return datatables()->eloquent($docentes)
                ->addColumn('foto', function ($docente) {
                    return '<img src="' . asset('storage/' . $docente->image) . '" 
                    alt="Imagen de ' . $docente->nombre1 . '" 
                    style="max-width: 60px; border-radius: 50%;">';
                })
                ->addColumn('nombre_completo', function ($docente) {
                    return $docente->nombre1 . ' ' . $docente->nombre2 . ' ' . $docente->apellidop . ' ' . $docente->apellidom;
                })
                ->filterColumn('nombre_completo', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(nombre1, ' ', nombre2, ' ', apellidop, ' ', apellidom) like ?", ["%{$keyword}%"]);
                })
                ->orderColumn('nombre_completo', function ($query, $order) {
                    $query->orderByRaw("CONCAT(nombre1, ' ', nombre2, ' ', apellidop, ' ', apellidom) {$order}");
                })
                ->addColumn('asignaturas', function ($docente) use ($user) {
                    if ($user->hasRole('Coordinador')) {
                        // Solo botón de ver
                        return '<button type="button" 
                                    class="btn btn-outline-secondary btn-sm btn-modal-asignatura btn-action"  
                                    data-id="' . $docente->dni . '" 
                                    data-toggle="modal" 
                                    data-target="#asignaturasModal" 
                                    title="Ver Asignaturas">
                                    <i class="fas fa-eye"></i>
                                </button>';
                    }

                    // Coordinador ≠, todos los botones
                    return '<div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <a href="' . route('asignaturas_docentes.create1', $docente->dni) . '" 
                                    class="btn btn-outline-success btn-sm btn-action"  
                                    title="Agregar Asignaturas">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <button type="button" 
                                    class="btn btn-outline-secondary btn-sm btn-modal-asignatura btn-action"  
                                    data-id="' . $docente->dni . '" 
                                    data-toggle="modal" 
                                    data-target="#asignaturasModal" 
                                    title="Ver Asignaturas">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>';
                })
                ->addColumn('cohortes', function ($docente) use ($user) {
                    if ($user->hasRole('Coordinador')) {
                        // Solo botón de ver
                        return '<button type="button" 
                                    class="btn btn-outline-info btn-sm btn-modal-cohortes btn-action"  
                                    data-dni="' . $docente->dni . '"
                                    data-toggle="modal" 
                                    data-target="#cohortesModal"  
                                    data-type="cohortes" 
                                    title="Ver Cohortes">
                                    <i class="fas fa-eye"></i>
                                </button>';
                    }

                    // Coordinador ≠, todos los botones
                    return '<div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <a href="' . route('cohortes_docentes.create1', $docente->dni) . '" 
                                    class="btn btn-outline-warning btn-sm btn-action"  
                                    title="Agregar Cohortes">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <button type="button" 
                                    class="btn btn-outline-info btn-sm btn-modal-cohortes btn-action"  
                                    data-dni="' . $docente->dni . '"
                                    data-toggle="modal" 
                                    data-target="#cohortesModal"  
                                    data-type="cohortes" 
                                    title="Ver Cohortes">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>';
                })
                ->addColumn('editar', function ($docente) {
                    return '<a href="' . route('docentes.edit', $docente->dni) . '" 
                            class="btn btn-outline-primary btn-sm btn-action" 
                            title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>';
                })
                ->rawColumns(['foto', 'asignaturas', 'cohortes', 'editar'])
                ->toJson();
        }

        return view('docentes.index');
    }

    public function cargarAsignaturas($dni)
    {
        $docente = Docente::with('asignaturas')->where('dni', $dni)->first();
        return response()->json($docente ? $docente->asignaturas : []);
    }

    public function obtenerCohortes($dni)
    {
        $docente = Docente::where('dni', $dni)->first();

        if (!$docente) {
            return response()->json(['error' => 'Docente no encontrado.'], 404);
        }

        $cohortesDocente = CohorteDocente::where('docente_dni', $dni)->get();

        $cohortes = $cohortesDocente->groupBy('cohort_id')->map(function ($items, $cohortId) {
            $cohorte = Cohorte::find($cohortId);

            if (!$cohorte) {
                return null;
            }

            return [
                'id' => $cohorte->id,
                'nombre' => $cohorte->nombre,
                'modalidad' => $cohorte->modalidad,
                'aula' => $cohorte->aula ? $cohorte->aula->nombre : 'No disponible',
                'paralelo' => $cohorte->aula && $cohorte->aula->paralelo ? $cohorte->aula->paralelo : 'No disponible',
                'maestria' => $cohorte->maestria->nombre,
                'asignaturas' => $items->map(function ($item) use ($cohorte) {
                    $asignatura = Asignatura::find($item->asignatura_id);

                    if (!$asignatura) {
                        return null;
                    }

                    // Obtener la calificación de acuerdo al cohorte_id y asignatura_id
                    $calificacion = CalificacionVerificacion::where('asignatura_id', $item->asignatura_id)
                        ->where('docente_dni', $item->docente_dni)
                        ->whereHas('asignatura', function ($query) use ($cohorte) {
                            // Suponiendo que la asignatura tiene un cohorte_id que corresponde al cohorte actual
                            $query->where('cohorte_id', $cohorte->id);
                        })
                        ->first();

                    return [
                        'id' => $asignatura->id,
                        'nombre' => $asignatura->nombre,
                        'calificado' => $calificacion ? ($calificacion->calificado ? 'Calificado' : 'No calificado') : 'No disponible',
                        'editar' => $calificacion ? $calificacion->editar : 0,
                    ];
                })->filter(), // Filtrar nulls si hay asignaturas no válidas
            ];
        })->filter(); // Filtrar nulls si hay cohortes no válidos


        // Incluir el nombre del docente en la respuesta
        return response()->json([
            'docente_nombre' => $docente->nombre_completo, // Asegúrate de que 'nombre_completo' es un atributo del modelo Docente
            'cohortes' => $cohortes->values(), // Convertir a array indexado
        ]);
    }


    public function guardarCambios(Request $request)
    {
        // Recuperar los datos del formulario
        $docenteDni = $request->input('docente_dni');
        $permisosEditar = $request->input('permiso_editar', []);

        // Iterar sobre los cohortes y asignaturas dentro de 'permiso_editar'
        foreach ($permisosEditar as $cohorteId => $asignaturas) {
            foreach ($asignaturas as $asignaturaId => $value) {
                // Convertir el valor a booleano
                $editar = $value == '1' ? true : false;

                // Buscar la calificación en la base de datos y actualizarla
                CalificacionVerificacion::updateOrCreate(
                    [
                        'docente_dni' => $docenteDni,
                        'asignatura_id' => $asignaturaId,
                        'cohorte_id' => $cohorteId
                    ],
                    [
                        'editar' => $editar
                    ]
                );
            }
        }

        // Redireccionar de vuelta o realizar otra acción después de guardar los cambios
        return redirect()->back()->with('success', 'Cambios guardados exitosamente');
    }



    public function create()
    {
        return view('docentes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'docen_foto' => 'nullable|image|max:2048', // máximo tamaño 2MB
            'nombre1'    => 'required|string|max:255',
            'apellidop'  => 'required|string|max:255',
            'dni'        => 'required|string|max:20|unique:docentes,dni',
            'email'      => 'required|email|unique:users,email',
        ]);

        // Crear docente
        $docente = new Docente();
        $docente->fill($request->only([
            'nombre1', 'nombre2', 'apellidop', 'apellidom', 'sexo', 'dni', 'tipo', 'email'
        ]));
        $docente->contra = Hash::make($request->input('dni')); // Hash seguro de Laravel

        // Manejo de foto
        if ($request->hasFile('docen_foto')) {
            $docente->image = $request->file('docen_foto')->store('imagenes_usuarios', 'public');
        } else {
            $docente->image = 'https://ui-avatars.com/api/?name=' . urlencode(substr($docente->nombre1, 0, 1));
        }

        $docente->save();

        // Crear usuario vinculado
        $usuario = new User();
        $usuario->fill([
            'name'     => $docente->nombre1,
            'apellido' => $docente->apellidop,
            'sexo'     => $docente->sexo,
            'email'    => $docente->email,
            'status'   => $request->input('estatus', 'ACTIVO'),
            'image'    => $docente->image,
        ]);
        $usuario->password = Hash::make($request->input('dni'));

        $usuario->save();

        // Asignar rol
        $docenteRole = Role::findById(2);
        $usuario->assignRole($docenteRole);

        return redirect()->route('docentes.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($dni)
    {
        $docente = Docente::where('dni', $dni)->first();
        return view('docentes.edit', compact('docente'));
    }

    public function update(Request $request, $dni)
    {
        $docente = Docente::where('dni', $dni)->firstOrFail();

        // Guardamos el email viejo antes de modificar nada
        $emailViejo = $docente->email;

        // Buscar usuario con el email viejo
        $usuario = User::where('email', $emailViejo)->first();

        $request->validate([
            'docen_foto' => 'nullable|image|max:2048',
        ]);

        // Actualizamos docente
        $docente->nombre1   = $request->input('nombre1');
        $docente->nombre2   = $request->input('nombre2');
        $docente->apellidop = $request->input('apellidop');
        $docente->apellidom = $request->input('apellidom');
        $docente->sexo      = $request->input('sexo');
        $docente->dni       = $request->input('dni');
        $docente->tipo      = $request->input('tipo');
        $docente->email     = $request->input('email');

        if ($request->hasFile('docen_foto')) {
            if ($docente->image) {
                \Storage::disk('public')->delete($docente->image);
            }
            $path = $request->file('docen_foto')->store('imagenes_usuarios', 'public');
            $docente->image = $path;
        }

        $docente->save();

        // Actualizamos usuario (si existe)
        if ($usuario) {
            $usuario->name     = $docente->nombre1;
            $usuario->apellido = $docente->apellidop;
            $usuario->sexo     = $docente->sexo;
            $usuario->email    = $docente->email; // aquí sí el nuevo
            $usuario->image    = $docente->image;
            $usuario->save();
        }

        return redirect()->route('docentes.index')->with('success', 'Docente actualizado exitosamente.');
    }

}
