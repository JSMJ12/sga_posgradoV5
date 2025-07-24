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

    public function __construct($user, $email_institucional, $nombre, $dni)
    {
        $this->user = $user;
        $this->email = $email_institucional;
        $this->nombre = $nombre;
        $this->dni = $dni;
    }

    /**
     * Solo se enviará por correo electrónico.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Contenido del correo electrónico.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Matrícula Exitosa - Bienvenido como Estudiante')
            ->greeting('¡Hola ' . $this->nombre . '!')
            ->line('¡Felicitaciones! Tu matrícula ha sido exitosa y el pago ha sido procesado correctamente.')
            ->line('Tu correo electrónico institucional ha sido creado: ' . $this->email)
            ->line('Puedes iniciar sesión con tu correo institucional y tu cédula como contraseña.')
            ->line('Tu contraseña es: ' . $this->dni)
            ->action('Iniciar sesión', route('login'))
            ->line('Si tienes alguna pregunta, no dudes en contactarnos.')
            ->line('¡Te damos la bienvenida a la comunidad universitaria!');
    }
}
