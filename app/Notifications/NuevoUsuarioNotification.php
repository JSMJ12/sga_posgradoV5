<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoUsuarioNotification extends Notification
{
    use Queueable;
    protected $usuario;
    protected $contrasena;
    protected $nombre;

    public function __construct($usuario, $contrasena, $nombre)
    {
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
        $this->nombre = $nombre;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('¡Bienvenido a POSGRADO SGA!')
            ->greeting('¡Hola ' . $this->nombre . '!')
            ->line('Se ha creado una cuenta para ti en nuestra aplicación.')
            ->line('Correo electrónico: ' . $this->usuario->email)
            ->line('Contraseña temporal: ' . $this->contrasena)
            ->action('Iniciar Sesión', route('login'))
            ->line('¡Gracias por unirte a nosotros!')
            ->salutation('Regards, POSGRADOSGA');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toMailUsing($notifiable, $recipient)
    {
        return parent::toMailUsing($notifiable, $recipient)->introLines([]);
    }
}
