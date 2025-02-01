<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        // Obtener las notificaciones del usuario autenticado
        $notificaciones = Auth::user()->unreadNotifications;

        // Marcar las notificaciones como leídas
        $notificaciones->markAsRead();

        // Retornar los datos de las notificaciones como JSON
        return response()->json([
            'notificaciones' => $notificaciones,
        ]);
    }

    public function contador()
    {
        // Obtener las notificaciones del usuario autenticado
        $notificaciones = Auth::user()->unreadNotifications;

        // Obtener la cantidad de notificaciones nuevas
        $cantidadNotificacionesNuevas = $notificaciones->count();

        // Retornar la cantidad de notificaciones nuevas como JSON
        return response()->json([
            'cantidadNotificacionesNuevas' => $cantidadNotificacionesNuevas
        ]);
    }

}
