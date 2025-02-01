<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Postulacion2 extends Notification
{
    use Queueable;
    protected $postulante;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($postulante)
    {
        $this->postulante = $postulante;
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
        ->subject('¡Gracias por proporcionar tus datos!')
        ->greeting('¡Hola ' . $this->postulante->nombre1 . ' ' . $this->postulante->apellidop . '!')
        ->line('Hemos guardado tus archivos para competar la postulación.')
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
