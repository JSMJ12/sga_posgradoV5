<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function marcarMensajesComoLeidos()
    {
        $user = auth()->user();

        $user->unreadNotifications->filter(function ($notification) {
            return Str::contains($notification->type, 'NewMessageNotification');
        })->each->markAsRead();

        return response()->json(['success' => true]);
    }

}
