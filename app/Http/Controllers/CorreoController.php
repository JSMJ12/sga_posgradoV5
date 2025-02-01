<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\Correo;

use Illuminate\Http\Request;

class CorreoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // Método para mostrar el formulario de envío de correo
    public function formulario()
    {
        return view('correo.enviar_correo');
    }

    // Método para enviar el correo
    public function enviarCorreo(Request $request)
    {
        // Valida los campos del formulario según tus necesidades.
        $request->validate([
            'destinatario' => 'required|email',
            'remitente' => 'required|email',
        ]);

        // Accede al valor del remitente desde el formulario.
        $remitente = $request->input('remitente');

        // Envía el correo utilizando el remitente.
        Mail::to($request->destinatario)->send(new Correo($remitente));

        // Puedes agregar un mensaje de éxito si lo deseas.
        return redirect()->route('formulario-correo')->with('success', 'Correo enviado con éxito');
    }



    // Método para cancelar el envío de correo
    public function cancelarEnvio()
    {
        // Puedes agregar lógica para redirigir a una página de cancelación o realizar otras acciones si es necesario.
        return redirect()->route('formulario-correo');
    }

}
