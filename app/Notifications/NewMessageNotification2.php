<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;

class NewMessageNotification2 extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Define los canales de notificación.
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Se envía solo por email y se guarda en la base de datos.
    }

    /**
     * Define el formato de la notificación para email.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('¡Tienes un nuevo mensaje!')
            ->action('Ver mensaje', route('messages.index'))
            ->line('Gracias por usar nuestra aplicación.');
    }

    /**
     * Define el formato de la notificación para la base de datos.
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'NewMessageNotification',
            'message' => $this->message->message,
            'sender' => [
                'name' => $this->message->sender->name,
            ],
            'receiver' => [
                'name' => $this->message->receiver->name,
            ],
            'time' => Carbon::now()->toDateTimeString(),
        ];
    }
}
