<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cohorte;
use App\Models\Maestria;
use App\Models\Secretario;
use App\Models\PeriodoAcademico;
use App\Models\Aula;

class CohorteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('perPage', 10);

        if ($user->hasRole('Administrador')) {
            // Si el usuario es administrador, muestra todos los cohortes
            $cohortes = Cohorte::with(['maestria', 'periodo_academico', 'aula']);
        } else {
            // Si el usuario no es administrador, asume que es un secretario
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();

            // Obtén los identificadores de las maestrías asociadas a la sección del secretario
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');

            // Filtra los cohortes que pertenecen a esas maestrías
            $cohortes = Cohorte::with(['maestria', 'periodo_academico', 'aula'])
                ->whereIn('maestria_id', $maestriasIds);
        }

        if ($request->ajax()) {
            return datatables()->eloquent($cohortes)
                ->addColumn('aula_nombre', function ($cohorte) {
                    return $cohorte->aula && $cohorte->aula->nombre ? $cohorte->aula->nombre : 'No asignada';
                })
                ->addColumn('acciones', function ($cohorte) {
                    return '
                        <a href="' . route('cohortes.edit', $cohorte->id) . '" class="btn btn-outline-primary btn-sm" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="' . route('cohortes.destroy', $cohorte->id) . '" method="POST" style="display: inline-block;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este cohorte?\');">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })                
                
                ->rawColumns(['acciones'])
                ->toJson();
        }        

        return view('cohortes.index', compact('perPage'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole('Secretario')) {
            $secretario = Secretario::where('nombre1', $user->name)
                ->where('apellidop', $user->apellido)
                ->where('email', $user->email)
                ->firstOrFail();
            //
            $maestriasIds = $secretario->seccion->maestrias->pluck('id');
            $maestrias = Maestria::whereIn('id', $maestriasIds)
                ->where('status', 'ACTIVO')
                ->get();
        } else {
            $maestrias = Maestria::where('status', 'ACTIVO')->get();
        }
        $periodos_academicos = PeriodoAcademico::all();
        $aulas = Aula::all();

        return view('cohortes.create', compact('maestrias', 'periodos_academicos', 'aulas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'maestria_id' => 'required|exists:maestrias,id',
            'periodo_academico_id' => 'required|exists:periodos_academicos,id',
            'aula_id' => 'nullable',
            'aforo' => 'required|integer',
            'modalidad' => 'required|in:presencial,hibrida,virtual',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        Cohorte::create($request->only([
            'nombre',
            'maestria_id',
            'periodo_academico_id',
            'aula_id',
            'aforo',
            'modalidad',
            'fecha_inicio',
            'fecha_fin',
        ]));

        return redirect()->route('cohortes.index')->with('success', 'La cohorte ha sido creada exitosamente.');
    }


    public function edit($cohorte)
    {
        $cohorte = Cohorte::where('id', $cohorte)->firstOrFail();
        $maestrias = Maestria::all();
        $periodos_academicos = PeriodoAcademico::all();
        $aulas = Aula::all();

        return view('cohortes.edit', compact('cohorte', 'maestrias', 'periodos_academicos', 'aulas'));
    }

    public function update(Request $request, $cohorte)
    {
        $request->validate([
            'nombre' => 'required|string',
            'maestria_id' => 'required|exists:maestrias,id',
            'periodo_academico_id' => 'required|exists:periodos_academicos,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'aforo' => 'required|integer',
            'modalidad' => 'required|in:presencial,hibrida,virtual',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $cohorte = Cohorte::findOrFail($cohorte);

        $cohorte->update([
            'nombre' => $request->input('nombre'),
            'maestria_id' => $request->input('maestria_id'),
            'periodo_academico_id' => $request->input('periodo_academico_id'),
            'aula_id' => $request->input('aula_id'),
            'aforo' => $request->input('aforo'),
            'modalidad' => $request->input('modalidad'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
        ]);


        return redirect()->route('cohortes.index')->with('success', 'La cohorte ha sido actualizada exitosamente.');
    }


    public function destroy($cohorte)
    {
        $cohorte = Cohorte::where('id', $cohorte)->firstOrFail();
        try {
            $cohorte->delete();
            return redirect()->route('cohortes.index')->with('success', 'El cohorte ha sido eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('cohortes.index')->with('error', 'Error al eliminar el cohorte: ' . $e->getMessage());
        }
    }
}
