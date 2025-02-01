<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\User;
use App\Models\Nota;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\CalificacionVerificacion;
use App\Models\Cohorte;
use App\Models\CohorteDocente;

class DocenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $docentes = Docente::query();

            return datatables()->eloquent($docentes)
                ->addColumn('foto', function ($docente) {
                    return '<img src="' . asset('storage/' . $docente->image) . '" 
                        alt="Imagen de ' . $docente->nombre1 . '" 
                        style="max-width: 60px; border-radius: 50%;">';
                })
                ->addColumn('nombre_completo', function ($docente) {
                    return $docente->nombre1 . '<br>' . $docente->nombre2 . '<br>' .
                        $docente->apellidop . '<br>' . $docente->apellidom;
                })
                ->addColumn('acciones', function ($docente) {
                    $acciones = '<div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">';
                    $acciones .= '<a href="' . route('docentes.edit', $docente->dni) . '" 
                                     class="btn btn-outline-primary btn-sm" 
                                     style="display: flex; align-items: center; gap: 5px;" 
                                     title="Editar">
                                     <i class="fas fa-edit"></i><span>Editar</span>
                                 </a>';
                    $acciones .= '<a href="' . route('asignaturas_docentes.create1', $docente->dni) . '" 
                                     class="btn btn-outline-success btn-sm" 
                                     style="display: flex; align-items: center; gap: 5px;" 
                                     title="Agregar Asignaturas">
                                     <i class="fas fa-plus"></i><span>Asignaturas</span>
                                 </a>';
                    $acciones .= '<a href="' . route('cohortes_docentes.create1', $docente->dni) . '" 
                                     class="btn btn-outline-warning btn-sm" 
                                     style="display: flex; align-items: center; gap: 5px;" 
                                     title="Agregar Cohortes">
                                     <i class="fas fa-plus"></i><span>Cohortes</span>
                                 </a>';
                    $acciones .= '<button type="button" 
                                           class="btn btn-outline-secondary btn-sm btn-modal-asignatura" 
                                           style="display: flex; align-items: center; gap: 5px;" 
                                           data-id="' . $docente->dni . '" 
                                           data-type="asignaturas" 
                                           title="Ver Asignaturas">
                                           <i class="fas fa-eye"></i><span>Ver Asignaturas</span>
                                       </button>';
                    $acciones .= '<button type="button" 
                                           class="btn btn-outline-info btn-sm btn-modal-cohortes" 
                                           style="display: flex; align-items: center; gap: 5px;" 
                                           data-dni="' . $docente->dni . '" 
                                           data-type="cohortes" 
                                           title="Ver Cohortes">
                                           <i class="fas fa-eye"></i><span>Ver Cohortes</span>
                                       </button>';
                    $acciones .= '</div>';

                    return $acciones;
                })

                ->rawColumns(['foto', 'nombre_completo', 'acciones']) // Permitir contenido HTML
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
        $docente = new Docente;
        $docente->nombre1 = $request->input('nombre1');
        $docente->nombre2 = $request->input('nombre2');
        $docente->apellidop = $request->input('apellidop');
        $docente->apellidom = $request->input('apellidom');
        $docente->contra = bcrypt($request->input('dni')); // Encriptar la contraseña
        $docente->sexo = $request->input('sexo');
        $docente->dni = $request->input('dni');
        $docente->tipo = $request->input('tipo');
        $docente->email = $request->input('email');
        $request->validate([
            'docen_foto' => 'nullable|image|max:2048', //máximo tamaño 2MB
        ]);
        $primeraLetra = substr($docente->nombre1, 0, 1);
        if ($request->hasFile('docen_foto')) {
            $path = $request->file('docen_foto')->store('imagenes_usuarios', 'public');
            $docente->image = $path;
        } else {
            $docente->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }
        //Almacenar la imagen
        $docente->save();

        $usuario = new User;
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('email');
        $usuario->image = $docente->image;
        $docenteRole = Role::findById(2);
        $usuario->assignRole($docenteRole);
        $usuario->save();



        return redirect()->route('docentes.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($dni)
    {
        $docente = Docente::where('dni', $dni)->first();
        return view('docentes.edit', compact('docente'));
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::findOrFail($id);
        $docente->nombre1 = $request->input('nombre1');
        $docente->nombre2 = $request->input('nombre2');
        $docente->apellidop = $request->input('apellidop');
        $docente->apellidom = $request->input('apellidom');
        $docente->sexo = $request->input('sexo');
        $docente->dni = $request->input('dni');
        $docente->tipo = $request->input('tipo');
        $docente->email = $request->input('email');

        $request->validate([
            'docen_foto' => 'nullable|image|max:2048', // Máximo tamaño 2MB
        ]);

        if ($request->hasFile('docen_foto')) {
            // Eliminar la imagen anterior si existe
            if ($docente->image) {
                \Storage::disk('public')->delete($docente->image);
            }

            $path = $request->file('docen_foto')->store('imagenes_usuarios', 'public');
            $docente->image = $path;
        }

        $docente->save();

        $usuario = User::where('email', $docente->email)->firstOrFail();
        $usuario->name = $docente->nombre1;
        $usuario->apellido = $docente->apellidop;
        $usuario->sexo = $docente->sexo;
        $usuario->email = $docente->email;
        $usuario->image = $docente->image;
        $usuario->save();

        return redirect()->route('docentes.index')->with('success', 'Docente actualizado exitosamente.');
    }
}
