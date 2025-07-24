<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Postulante;

class PostulanteAceptadoNotification extends Notification
{
    use Queueable;

    protected $postulante;
    protected $userId;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;

        // Buscar al usuario por su email
        $user = User::where('email', $postulante->correo_electronico)->first();
        $this->userId = $user ? $user->id : null;
    }

    /**
     * Canales de entrega de la notificación.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Representación de la notificación en correo electrónico.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('¡Felicidades! Has sido aceptado como alumno')
            ->greeting('¡Enhorabuena!')
            ->line('Tu solicitud ha sido aceptada y ahora eres oficialmente un alumno.')
            ->action('Pagar Matrícula', route('inicio'))
            ->line('Por favor, haz clic en el botón para pagar la matrícula y completar tu proceso de inscripción.')
            ->salutation('Atentamente, el equipo de admisiones.');
    }

    /**
     * Representación de la notificación en base de datos.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'PostulanteAceptadoNotification',
            'message' => '¡Felicidades! Tu solicitud ha sido aceptada y ahora eres oficialmente un alumno. Para completar tu proceso de ingreso, te pedimos que realices el pago de la matrícula.',
        ];
    }
}
