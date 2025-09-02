<?php

namespace App\Http\Controllers;

use App\Models\Tutoria;
use App\Models\Tesis;
use Illuminate\Http\Request;
use App\Models\Docente;
use Yajra\DataTables\Contracts\DataTable;

class TutoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $docente = Docente::where('email', auth()->user()->email)->firstOrFail();

        if ($request->ajax()) {
            $tesis = Tesis::where('tutor_dni', $docente->dni)
                ->with(['alumno', 'tutorias', 'titulaciones']) 
                ->get();

            return datatables()->of($tesis)
                ->addColumn('alumno', function ($item) {
                    return $item->alumno ?
                        "{$item->alumno->nombre1} {$item->alumno->nombre2} {$item->alumno->apellidop} {$item->alumno->apellidom}" :
                        'Sin asignar';
                })
                ->addColumn('contacto', function ($item) {
                    return $item->alumno ?
                        "Correo: {$item->alumno->email_institucional} <br> Celular: {$item->alumno->celular}" :
                        'Sin contacto';
                })
                ->addColumn('acciones', function ($item) {
                    $puedeTitular = $item->tutorias &&
                        $item->tutorias->where('estado', 'realizada')->count() >= 3 &&
                        $item->alumno &&
                        $item->alumno->monto_total == 0;

                    $tesisAprobada = $item->estado === 'aprobado';

                    if ($puedeTitular && $tesisAprobada) {
                        return view('tutorias.partials.titular_button', compact('item'))->render();
                    }

                    return view('tutorias.actions', compact('item'))->render();
                })

                ->rawColumns(['acciones', 'contacto'])
                ->make(true);
        }

        return view('tutorias.index', compact('docente'));
    }

    public function create($tesisId)
    {
        return view('tutorias.create', compact('tesisId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'detalle' => 'required|string',
            'observaciones' => 'nullable|string',
            'tipo' => 'required|in:presencial,virtual',
        ]);

        // Buscar el docente por el email del usuario autenticado
        $docente = Docente::where('email', auth()->user()->email)->first();

        if (!$docente) {
            return redirect()->back()->with('error', 'Docente no encontrado.');
        }

        // Verificar si ya existen 3 tutorías para esta tesis
        $tutoriasCount = Tutoria::where('tesis_id', $request->tesis_id)->count();

        if ($tutoriasCount >= 3) {
            return redirect()->back()->with('error', 'Ya se han creado 3 tutorías para esta tesis. No se pueden agregar más.');
        }

        // Asignar lugar o link según el tipo de tutoría
        $lugar = $request->tipo === 'presencial' ? $validated['detalle'] : null;
        $linkReunion = $request->tipo === 'virtual' ? $validated['detalle'] : null;

        Tutoria::create([
            'tesis_id' => $request->tesis_id,
            'tutor_dni' => $docente->dni,
            'fecha' => $validated['fecha'],
            'observaciones' => $validated['observaciones'],
            'tipo' => $request->tipo,
            'lugar' => $lugar,
            'link_reunion' => $linkReunion,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('tutorias.index')
            ->with('success', 'Tutoría creada correctamente.');
    }


    public function updateEstado(Request $request, $id)
    {
        $tutoria = Tutoria::findOrFail($id);
        $tutoria->update(['estado' => 'realizada']);

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }
    public function listar($tesisId)
    {
        $tesis = Tesis::with('tutorias.tutor')->findOrFail($tesisId);
        return view('tutorias.listar', compact('tesis'));
    }

    public function destroy($id)
    {

        $tutoria = Tutoria::findOrFail($id);

        $tutoria->delete();

        return redirect()->route('tutorias.listar', $tutoria->tesis_id)
            ->with('success', 'Tutoría eliminada correctamente.');
    }
}
