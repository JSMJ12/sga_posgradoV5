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

        if (!$docente || !$docente->maestria()->exists()) {
            return $request->ajax()
                ? response()->json(['error' => 'No estás asignado a ninguna maestría.'], 403)
                : redirect()->back()->withErrors(['error' => 'No estás asignado a ninguna maestría.']);
        }

        $maestria = $docente->maestria()->first();

        $maestriaId = $maestria->id;

        $cohortes = $maestria->cohortes;

        if ($request->ajax()) {
            $solicitudes = Tesis::with('alumno', 'tutor')
                ->whereHas('alumno', function ($query) use ($maestriaId) {
                    $query->where('maestria_id', $maestriaId);
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
                        : 'default.jpg'; // Si no tiene imagen, usa una imagen predeterminada
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

            // Redirigir a la página anterior con un mensaje de éxito
            return redirect()->back()->with('success', 'Tutor asignado correctamente.');
        } else {
            // Redirigir a la página anterior con un mensaje de error
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

        $validatedData = $request->validate([
            'tema' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
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
        $tesis->tema = $validatedData['tema'] ?? null; // Asigna null si no está presente
        $tesis->descripcion = $validatedData['descripcion'] ?? null; // Asigna null si no está presente
        $tesis->solicitud_pdf = $hasFile ? $pdfPath : null; // Si no hay archivo, será null
        $tesis->estado = 'pendiente';
        $tesis->save();


        // Determinar el tipo
        if (empty($validatedData['tema']) && empty($validatedData['descripcion']) && !$hasFile) {
            $tesis->tipo = 'examen complexivo';
            // Obtener la primera matrícula del alumno
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
                $tasaTitulacion->examen_complexivo += 1;
                $tasaTitulacion->save();
            } else {
                // Si no existe, lo creamos con valores iniciales
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
        $dniAlumno = $alumno->dni;

        $tesis = Tesis::where('alumno_dni', $dniAlumno)->with('tutorias')->first();

        return view('titulacion.proceso', compact('tesis', 'alumno'));
    }

    public function downloadPDF()
    {
        $alumno = Alumno::where('email_institucional', Auth::user()->email)->first();

        if (!$alumno) {
            abort(404, 'Alumno no encontrado');
        }

        $filename = 'Tema_Tesis_' . $alumno->nombre1 . '_' . $alumno->apellidop . '_' . $alumno->dni . '.pdf';
        $coordinadorDni = $alumno->maestria->coordinador;

        // Buscar al docente utilizando el DNI
        $coordinador = Docente::where('dni', $coordinadorDni)->first();

        if ($coordinador) {
            // Acceder al nombre completo utilizando el método getFullNameAttribute
            $nombreCompleto = $coordinador->getFullNameAttribute();
        } else {
            $nombreCompleto = 'Coordinador no encontrado';
        }

        return PDF::loadView('titulacion.solicitud', compact('alumno', 'nombreCompleto'))
            ->setPaper('A4', 'portrait')
            ->download($filename); // Descargar directamente
    }
    public function show($id)
    {
        $tesis = Tesis::with('alumno', 'tutor')->findOrFail($id);

        return view('tesis.show', compact('tesis'));
    }

    public function certificacion(Request $request)
    {
        $user = auth()->user();
        $docente = Docente::where('email', $user->email)->first();

        if (!$docente) {
            return response()->json(['error' => 'No se encontró al docente asociado al usuario.'], 404);
        }

        $alumnoDni = $request->input('alumno_dni');
        $alumno = Alumno::with('tesis.tutorias')
            ->where('dni', $alumnoDni)
            ->first();

        if (!$alumno) {
            return response()->json(['error' => 'No se encontró al alumno con el DNI proporcionado.'], 404);
        }

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL');

        $pdfFileName = preg_replace('/[^A-Za-z0-9_\-]/', '', $docente->apellidop . $docente->nombre1 . $alumno->dni) . '_certificacion_titulacion.pdf';

        return PDF::loadView('titulacion.certificado_tutor', compact(
            'alumno',
            'docente',
            'fechaActual',
        ))
            ->setPaper('A4', 'portrait')
            ->download($pdfFileName);
    }
}
