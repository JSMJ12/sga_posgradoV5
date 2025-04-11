<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class BienvenidaNotificacion extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $user;

    // Cambia el constructor para aceptar el usuario completo
    public function __construct($user)
    {
        $this->user = $user;
    }

    // Define los canales por los que se enviará la notificación
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // Datos de la notificación en la base de datos
    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => 'Gracias por iniciar sesión, ' . $this->user->name . '.',  // Usamos $this->user para acceder al nombre
        ];
    }

    // Canal privado para la transmisión en tiempo real
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);  // Usamos el id directamente de $this->user
    }

    // Definir un nombre para el evento de transmisión
    public function broadcastAs()
    {
        return 'bienvenida.enviada';
    }

    // Personalizar los datos enviados durante la transmisión
    public function broadcastWith()
    {
        return [
            'message' => 'Has iniciado sesión exitosamente. ¡Bienvenido ' . $this->user->name . '!',
        ];
    }
}
