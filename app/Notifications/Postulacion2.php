<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;
use App\Models\Postulante;
use Illuminate\Broadcasting\PrivateChannel;

class Postulacion2 extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $postulante;
    protected $userId;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;

        // Buscar al usuario por su email y extraer el DNI
        $user = User::where('email', $postulante->correo_electronico)->first();
        $this->userId = $user ? $user->id : null;
    }

    /**
     * Definir los canales de entrega de la notificación.
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast']; // Agregamos broadcast para Pusher
    }

    /**
     * Notificación por correo.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('¡Gracias por proporcionar tus datos!')
            ->greeting('¡Hola ' . $this->postulante->nombre1 . ' ' . $this->postulante->apellidop . '!')
            ->line('Hemos guardado tus archivos para completar la postulación.')
            ->action('Iniciar Sesión', route('login'))
            ->line('¡Gracias por unirte a nosotros!')
            ->salutation('Saludos, POSGRADOSGA');
    }

    /**
     * Notificación para la base de datos.
     */
    public function toArray($notifiable)
    {
        return [
            'mensaje' => 'Hemos guardado tus archivos para completar la postulación.',
            'postulante' => $this->postulante->nombre1 . ' ' . $this->postulante->apellidop,
            'url' => route('login'),
        ];
    }

    /**
     * Notificación para Pusher (broadcast en tiempo real).
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }
    public function broadcastAs()
    {
        return 'archivo.subido';
    }
    public function broadcastWith()
    {
        return [
            'message' => 'Hemos guardado tus archivos para completar la postulación.',
        ];
    }
}
