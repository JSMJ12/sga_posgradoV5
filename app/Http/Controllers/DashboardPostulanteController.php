<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postulante;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Postulacion2;
use App\Notifications\SubirArchivoNotification;
use App\Notifications\NuevoPagoMatricula;

class DashboardPostulanteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = auth()->user();
        $postulante = Postulante::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('correo_electronico', $user->email)
            ->with(['maestria.cohortes' => function ($query) {
                $query->latest();
            }])
            ->firstOrFail();
        if (
            is_null($postulante->pdf_cedula) ||
            is_null($postulante->pdf_papelvotacion) ||
            is_null($postulante->pdf_titulouniversidad) ||
            is_null($postulante->pdf_hojavida) ||
            ($postulante->discapacidad == "Si" && is_null($postulante->pdf_conadis))
        ) {
            $usuario = User::where('name', $postulante->nombre1)
                ->where('apellido', $postulante->apellidop)
                ->where('email', $postulante->correo_electronico)
                ->first();

            if ($usuario) {
                $usuario->notify(new SubirArchivoNotification($postulante));
            }
        }
        return view('dashboard.postulante', compact('postulante'));
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        // Buscar al postulante en la base de datos
        $postulante = Postulante::where('nombre1', $user->name)
            ->where('apellidop', $user->apellido)
            ->where('correo_electronico', $user->email)
            ->first();

        if (!$postulante) {
            return back()->with('error', 'No se encontró al postulante en la base de datos.');
        }

        // Validar los archivos
        $rules = [
            'Cédula' => 'nullable|file|mimes:pdf|max:5120',
            'Papel_de_Votación' => 'nullable|file|mimes:pdf|max:5120',
            'Título_de_Universidad' => 'nullable|file|mimes:pdf|max:5120',
            'Hoja_de_Vida' => 'nullable|file|mimes:pdf|max:5120',
            'CONADIS' => 'nullable|file|mimes:pdf|max:5120',
            'Carta_de_Aceptación' => 'nullable|file|mimes:pdf|max:5120',
            'pago_matricula' => 'nullable|file|mimes:pdf|max:5120',
        ];

        $messages = [
            'file' => 'El archivo debe ser un archivo válido.',
            'mimes' => 'Solo se permiten archivos en formato PDF.',
            'max' => 'El archivo no debe superar los 5MB.',
        ];

        $request->validate($rules, $messages);

        // Definir los archivos esperados y sus nombres de columna en la BD
        $files = [
            'Cédula' => 'pdf_cedula',
            'Papel_de_Votación' => 'pdf_papelvotacion',
            'Título_de_Universidad' => 'pdf_titulouniversidad',
            'CONADIS' =>  'pdf_conadis',
            'Hoja_de_Vida' => 'pdf_hojavida',
            'Carta_de_Aceptación' => 'carta_aceptacion',
            'pago_matricula' => 'pago_matricula',
        ];

        $updateData = [];
        $uploadedFiles = 0;
        $pagoMatriculaSubido = false; // Para verificar si se subió "pago_matricula"

        foreach ($files as $inputName => $column) {
            if ($request->hasFile($inputName)) {
                try {
                    $path = $request->file($inputName)->store('postulantes/pdf', 'public');
                    $updateData[$column] = $path;
                    $uploadedFiles++;

                    // Verificar si el archivo subido es "pago_matricula"
                    if ($column === 'pago_matricula') {
                        $pagoMatriculaSubido = true;
                    }
                } catch (\Exception $e) {
                    return back()->with('error', "Error al subir {$inputName}: " . $e->getMessage());
                }
            }
        }

        // Si se subió el pago de matrícula, enviar la notificación
        if ($pagoMatriculaSubido) {
            $postulante->notify(new NuevoPagoMatricula($postulante));
        }


        // Si no se subió ningún archivo, mostrar advertencia
        if ($uploadedFiles === 0) {
            return back()->with('warning', 'No se subió ningún archivo válido.');
        }

        // Actualizar la base de datos con los archivos subidos
        $postulante->update($updateData);

        // Enviar notificación solo si al menos un archivo fue subido
        try {
            Notification::route('mail', $postulante->correo_electronico)
                ->notify(new Postulacion2($postulante));
        } catch (\Exception $e) {
        }

        return back()->with('success', 'Archivo(s) subido(s) exitosamente.');
    }


    public function carta_aceptacionPdf(Request $request, $dni)
    {
        $postulante = Postulante::find($dni);

        $filename = 'Carta_de_Aceptacion_' . $postulante->nombre1 . '_' . $postulante->apellidop . '_' . $postulante->dni . '.pdf';

        return PDF::loadView('postulantes.carta_aceptacion', compact('postulante'))
            ->setPaper('A4', 'portrait')
            ->stream($filename);
    }
}
