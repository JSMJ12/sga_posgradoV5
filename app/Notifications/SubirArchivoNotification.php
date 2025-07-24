<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Postulante;

class SubirArchivoNotification extends Notification
{
    use Queueable;

    protected $postulante;
    protected $userId;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;

        $user = User::where('email', $postulante->correo_electronico)->first();
        $this->userId = $user ? $user->id : null;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📎 Recordatorio de Postulación')
            ->greeting('¡Hola ' . $this->postulante->nombre1 . '!')
            ->line('Queremos recordarte que aún tienes archivos pendientes por subir para completar tu proceso de postulación.')
            ->line('Es importante que los subas lo antes posible para continuar con tu inscripción.')
            ->action('Ir al Panel de Postulante', route('dashboard_postulante'))
            ->line('Si tienes alguna duda o inconveniente, no dudes en comunicarte con nosotros.')
            ->salutation('Saludos cordiales, ' . config('app.name'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'SubirArchivo',
            'message' => 'Recuerda subir tus archivos para completar tu proceso de postulación.',
        ];
    }
}
