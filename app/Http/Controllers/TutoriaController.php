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
        // Obtener el docente autenticado usando el email del usuario actual
        $docente = Docente::where('email', auth()->user()->email)->firstOrFail();

        if ($request->ajax()) {
            $tesis = Tesis::where('tutor_dni', $docente->dni)
                ->with(['alumno', 'tutorias']) // Incluir datos del alumno y tutorías
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
                    // Validar si se cumplen las condiciones para titular al alumno
                    $puedeTitular = $item->tutorias &&
                        $item->tutorias->where('estado', 'realizada')->count() >= 3 &&
                        $item->alumno &&
                        $item->alumno->monto_total == 0;
                
                    // Obtener la tesis del alumno (asegúrate de que la relación está correctamente definida)
                    $tesis = $item->alumno ? $item->alumno->tesis()->first() : null;
                
                    // Verificar que la tesis esté aprobada
                    $tesisAprobada = $tesis && $tesis->estado === 'aprobado';
                
                    // Verificar si se cumplen todas las condiciones para titular y si la tesis está aprobada
                    if ($puedeTitular && $tesisAprobada) {
                        return '
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <a href="' . route('certificar.alumno', ['alumno_dni' => $item->alumno->dni]) . '" class="btn btn-success">
                                    <i class="fas fa-download"></i> Certificado
                                </a>
                                <form action="' . route('titulacion_alumno.store') . '" method="POST" id="titularForm_' . $item->alumno->dni . '">
                                    ' . csrf_field() . '
                                    <input type="hidden" name="alumno_dni" value="' . $item->alumno->dni . '">
                                    <button type="button" class="btn btn-danger btn-sm" id="titularBtn_' . $item->alumno->dni . '" onclick="confirmTitularAlumno(\'' . $item->alumno->dni . '\')">
                                        <i class="fas fa-user-graduate"></i> Titular Alumno
                                    </button>
                                </form>
                            </div>
                            <script>
                                function confirmTitularAlumno(dni) {
                                    // Deshabilitar el botón al hacer clic para evitar múltiples clics
                                    var button = document.getElementById("titularBtn_" + dni);
                                    button.disabled = true;
                                    button.innerHTML = "<i class=\'fas fa-spinner fa-spin\'></i> Procesando..."; // Cambiar texto a "Procesando..."
                                
                                    Swal.fire({
                                        title: "¿Estás seguro?",
                                        text: "¿Deseas titular al alumno con DNI " + dni + "?",
                                        icon: "warning",
                                        showCancelButton: true,
                                        confirmButtonColor: "#3085d6",
                                        cancelButtonColor: "#d33",
                                        confirmButtonText: "Sí, titular",
                                        cancelButtonText: "Cancelar"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            document.getElementById("titularForm_" + dni).submit();
                                        } else {
                                            // Si se cancela, habilitar el botón nuevamente
                                            button.disabled = false;
                                            button.innerHTML = "<i class=\'fas fa-user-graduate\'></i> Titular Alumno"; // Restaurar texto
                                        }
                                    });
                                }
                            </script>';
                    }
                
                    // Retornar las acciones normales si no se cumplen las condiciones
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
