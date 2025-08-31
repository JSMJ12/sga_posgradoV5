<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\TasaTitulacion;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\TesisAceptadaNotificacion;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class TesisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        // Verifica que el docente tenga al menos una maestría asignada
        if (!$docente || !$docente->maestria()->exists()) {
            return $request->ajax()
                ? response()->json(['error' => 'No estás asignado a ninguna maestría.'], 403)
                : redirect()->back()->withErrors(['error' => 'No estás asignado a ninguna maestría.']);
        }

        // Obtiene todas las maestrías del docente
        $maestrias = $docente->maestria()->get();
        $maestriaIds = $maestrias->pluck('id')->toArray();

        // Obtiene los cohortes de todas las maestrías
        $cohortes = \App\Models\Cohorte::whereIn('maestria_id', $maestriaIds)->get();

        if ($request->ajax()) {
            $solicitudes = Tesis::with('alumno', 'tutor')
                ->where('tipo', '!=', 'examen complexivo')
                ->whereHas('alumno.maestrias', function ($query) use ($maestriaIds) {
                    $query->whereIn('maestria_id', $maestriaIds);
                })
                ->orderByRaw('tutor_dni IS NULL DESC')
                ->orderBy('estado', 'asc')
                ->get();

            return DataTables::of($solicitudes)
                ->addColumn('nombre_completo', function ($tesis) {
                    return $tesis->alumno
                        ? $tesis->alumno->nombre1 . ' ' . $tesis->alumno->nombre2 . ' ' . $tesis->alumno->apellidop . ' ' . $tesis->alumno->apellidom
                        : 'Sin alumno asignado';
                })
                ->addColumn('tutor', function ($tesis) {
                    return $tesis->tutor
                        ? $tesis->tutor->nombre1 . ' ' . $tesis->tutor->nombre2 . ' ' . $tesis->tutor->apellidop . ' ' . $tesis->tutor->apellidom
                        : 'Sin tutor asignado';
                })
                ->addColumn('acciones', function ($tesis) {
                    return view('partials.botones_tesis', compact('tesis'))->render();
                })
                ->editColumn('estado', function ($tesis) {
                    $badgeClass = $tesis->estado === 'aprobado' ? 'success' : 'warning';
                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($tesis->estado) . '</span>';
                })
                ->addColumn('alumno_image', function ($tesis) {
                    return $tesis->alumno && $tesis->alumno->image
                        ? asset('storage/' . $tesis->alumno->image)
                        : 'default.jpg';
                })
                ->rawColumns(['acciones', 'estado'])
                ->make(true);
        }

        $docentes = Docente::all();
        return view('titulacion.solicitudes', compact('docentes', 'cohortes'));
    }
    public function aceptarTema($id)
    {
        try {
            $tesis = Tesis::findOrFail($id);

            // Verificar si el usuario está asignado
            if (!$tesis->alumno) {
                Log::error('No se encontró alumno para la tesis con ID: ' . $id);
                return response()->json(['error' => 'No hay usuario asignado a esta tesis.'], 400);
            }

            $usuario = User::where('email', $tesis->alumno->email_institucional)->first();

            if ($usuario) {
                // Pasar tanto la tesis como el usuario al constructor de la notificación
                Notification::route('mail', $usuario->email)
                    ->notify(new TesisAceptadaNotificacion($tesis, $usuario));
            }

            $tesis->estado = 'aprobado';
            $tesis->save();

            return response()->json(['success' => 'Tema aceptado correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al aceptar tema: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error en el servidor.'], 500);
        }
    }
    public function asignarTutor(Request $request, $id)
    {
        $tesis = Tesis::findOrFail($id);
        $dni = $request->input('dni');
        $docente = Docente::where('dni', $dni)->first();
        $tutor = User::where('email', $docente->email)->first();  // Corregido aquí

        // Verificar si el docente y el tutor existen
        if ($docente && $tutor) {
            $tutor->assignRole('Tutor');  // Asignar el rol de tutor
            $tesis->tutor_dni = $docente->dni;  // Asignar el tutor al campo correspondiente
            $tesis->save();  // Guardar la tesis con el nuevo tutor asignado

            // Redirigir a la página anterior with un mensaje de éxito
            return redirect()->back()->with('success', 'Tutor asignado correctamente.');
        } else {
            // Redirigir a la página anterior with un mensaje de error
            return redirect()->back()->with('error', 'No se encontró el tutor o el docente.');
        }
    }
    public function rechazarTema($id)
    {
        $tesis = Tesis::findOrFail($id);
        $tesis->estado = 'rechazado';
        $tesis->save();

        return response()->json(['success' => 'Tema rechazado correctamente.']);
    }

    public function store(Request $request)
    {
        $alumno = Alumno::where('email_institucional', Auth::user()->email)->first();

        if (!$alumno) {
            return redirect()->route('tesis.create')->with('error', 'Alumno no encontrado. Verifique que su correo institucional esté registrado.');
        }

        // Recibe el id de la maestría desde el formulario
        $maestriaId = $request->input('maestria_id');
        $maestria = $alumno->maestrias()->where('maestria_id', $maestriaId)->first();

        $validatedData = $request->validate([
            'tema' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'maestria_id' => 'required|exists:maestrias,id',
        ]);

        $hasFile = $request->hasFile('solicitud_pdf');
        if ($hasFile) {
            $request->validate([
                'solicitud_pdf' => 'required|file|mimes:pdf|max:2048',
            ]);
            $pdfPath = $request->file('solicitud_pdf')->store('solicitudes_pdf', 'public');
        }

        $tesis = new Tesis();
        $tesis->alumno_dni = $alumno->dni;
        $tesis->tema = $validatedData['tema'] ?? null;
        $tesis->descripcion = $validatedData['descripcion'] ?? null;
        $tesis->solicitud_pdf = $hasFile ? $pdfPath : null;
        $tesis->estado = 'pendiente';
        $tesis->maestria_id = $maestriaId;
        $tesis->save();

        if (empty($validatedData['tema']) && empty($validatedData['descripcion']) && !$hasFile) {
            $tesis->tipo = 'examen complexivo';
            $matricula = $alumno->matriculas()->first();
            if (!$matricula) {
                return redirect()->back()->with('error', 'Matrícula no encontrada');
            }

            $cohorteId = $matricula->cohorte_id;

            $tasaTitulacion = TasaTitulacion::where('cohorte_id', $cohorteId)
                ->where('maestria_id', $maestriaId)
                ->first();

            if ($tasaTitulacion) {
                $tasaTitulacion->examen_complexivo += 1;
                $tasaTitulacion->save();
            } else {
                TasaTitulacion::create([
                    'cohorte_id' => $cohorteId,
                    'maestria_id' => $maestriaId,
                    'examen_complexivo' => 1,
                ]);
            }
        } else {
            $tesis->tipo = $request->input('tipo');
        }

        $tesis->save();

        if ($hasFile) {
            return redirect()->route('dashboard_alumno')->with('success', 'Solicitud de aprobación de tema enviada correctamente.');
        }

        return redirect()->route('tesis.create')->with('warning', 'Por favor complete todos los pasos del formulario.');
    }

    public function create()
    {
        $email = Auth::user()->email;
        $alumno = Alumno::where('email_institucional', $email)->first();

        // Maestrías del alumno
        $maestrias = $alumno->maestrias;

        // Filtrar maestrías donde NO tiene titulación y todas las notas aprobadas
        $maestriasPendientes = $maestrias->filter(function($maestria) use ($alumno) {

            // Verificar si ya tiene tesis en esta maestría
            $tesis = Tesis::where('alumno_dni', $alumno->dni)
                ->where('maestria_id', $maestria->id)
                ->with('titulaciones')
                ->first();

            // Si ya existe una titulación en esa tesis, se excluye
            if ($tesis && $tesis->titulaciones->isNotEmpty()) {
                return false;
            }

            // Obtener asignaturas y notas de esa maestría
            $asignaturas = $maestria->asignaturas;
            $notas = $alumno->notas->whereIn('asignatura_id', $asignaturas->pluck('id'));

            // Si no tiene todas las notas, no mostrar
            if ($notas->count() != $asignaturas->count()) return false;

            // Verificar si todas las notas alcanzan >= 7 considerando recuperación
            return $notas->every(function($nota) {
                $campos = [
                    'actividades'  => $nota->nota_actividades ?? 0,
                    'practicas'    => $nota->nota_practicas ?? 0,
                    'autonomo'     => $nota->nota_autonomo ?? 0,
                    'examen_final' => $nota->examen_final ?? 0,
                ];

                $total = array_sum($campos);

                if (!is_null($nota->recuperacion) && $nota->recuperacion > 0) {
                    $minKey = array_keys($campos, min($campos))[0];
                    if ($nota->recuperacion > $campos[$minKey]) {
                        $campos[$minKey] = $nota->recuperacion;
                    }
                    $total = array_sum($campos);
                }

                return $total >= 7;
            });
        });

        // Traer la tesis actual del alumno con tutorías
        $tesis = Tesis::where('alumno_dni', $alumno->dni)->with('tutorias', 'titulaciones')->first();

        return view('titulacion.proceso', compact('tesis', 'alumno', 'maestriasPendientes'));
    }

    public function downloadPDF(Request $request)
    {
        $alumno = Alumno::where('email_institucional', Auth::user()->email)->first();

        if (!$alumno) {
            abort(404, 'Alumno no encontrado');
        }

        // Recibe el id de la maestría desde el request
        $maestriaId = $request->input('maestria_id');
        $maestria = $alumno->maestrias()->where('maestria_id', $maestriaId)->first();

        $filename = 'Tema_Tesis_' . $alumno->nombre1 . '_' . $alumno->apellidop . '_' . $alumno->dni . '.pdf';
        $coordinadorDni = $maestria ? $maestria->coordinador : null;

        $coordinador = $coordinadorDni ? Docente::where('dni', $coordinadorDni)->first() : null;

        $nombreCompleto = $coordinador ? $coordinador->getFullNameAttribute() : 'Coordinador no encontrado';

        return PDF::loadView('titulacion.solicitud', compact('alumno', 'nombreCompleto', 'maestria'))
            ->setPaper('A4', 'portrait')
            ->download($filename);
    }
    public function show($id)
    {
        $tesis = Tesis::with('alumno', 'tutor')->findOrFail($id);

        return view('tesis.show', compact('tesis'));
    }

    public function certificacion($tesis_id)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente) {
            return response()->json(['error' => 'No se encontró al docente asociado al usuario.'], 404);
        }

        // Buscar la tesis con el id
        $tesis = Tesis::with(['alumno', 'tutorias', 'maestria'])
            ->find($tesis_id);

        if (!$tesis) {
            return response()->json(['error' => 'No se encontró la tesis con el ID proporcionado.'], 404);
        }

        // Alumno de la tesis
        $alumno = $tesis->alumno;

        if (!$alumno) {
            return response()->json(['error' => 'La tesis no tiene un alumno asociado.'], 404);
        }

        // Obtener la maestría directamente de la relación
        $maestria = $tesis->maestria;

        // Fecha actual en español
        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        // Nombre de archivo
        $pdfFileName = preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '',
            $docente->apellidop . $docente->nombre1 . $alumno->dni
        ) . '_certificacion_titulacion.pdf';

        
        // Generar el PDF
        return PDF::loadView('titulacion.certificado_tutor', compact(
            'alumno',
            'docente',
            'fechaActual',
            'maestria',
            'tesis'
        ))
            ->setPaper('A4', 'portrait')
            ->stream($pdfFileName); // 👈 Esto abre en el navegador

            }

}
