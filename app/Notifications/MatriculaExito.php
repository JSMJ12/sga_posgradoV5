<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MatriculaExito extends Notification
{
    use Queueable;

    protected $user;
    protected $email;
    protected $nombre;
    protected $dni;

    public function __construct($user, $email, $nombre, $dni)
    {
        $this->user = $user;
        $this->email = $email;
        $this->nombre = $nombre;
        $this->dni = $dni;
    }

    /**
     * Métodos para determinar cómo se enviará la notificación
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function via($notifiable)
    {
        return ['mail']; // Puedes agregar más canales como base de datos o SMS si lo deseas
    }

    /**
     * Construir el mensaje para la notificación (correo)
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Matrícula Exitosa - Bienvenido como Estudiante')
                    ->greeting('¡Hola ' . $this->nombre . '!')
                    ->line('¡Felicitaciones! Tu matrícula ha sido exitosa y el pago ha sido procesado correctamente.')
                    ->line('Tu correo electrónico institucional ha sido creado: ' . $this->email)
                    ->line('Puedes iniciar sesión con tu correo institucional y tu cedula como contraseña.')
                    ->line('Tu contraseña es: ' . $this->dni)
                    ->action('Iniciar sesión', url('/login')) 
                    ->line('Si tienes alguna pregunta, no dudes en contactarnos.')
                    ->line('¡Te damos la bienvenida a la comunidad universitaria!');
    }

    /**
     * Opcionalmente, puedes almacenar la notificación en la base de datos si lo deseas.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Tu matrícula ha sido exitosa y el pago ha sido procesado. Ahora puedes acceder con tu correo institucional.',
            'dni' => $this->dni,
        ];
    }
}
