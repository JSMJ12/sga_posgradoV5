<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class NewMessageNotification2 extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $message;
    protected $userId;

    public function __construct($message)
    {
        $this->message = $message;
        $this->userId = $message->receiver->id; // El ID del receptor
    }

    /**
     * Define los canales de notificación.
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast']; // Se envía por correo, base de datos y Pusher.
    }

    /**
     * Define el formato de la notificación para email.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo mensaje recibido')
            ->greeting('¡Hola ' . $this->message->receiver->name . '!')
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
            'url' => route('messages.index'),
        ];
    }

    /**
     * El canal de transmisión para Pusher.
     *
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId); // Canal privado del receptor
    }

    /**
     * Nombre del evento para Pusher.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new.message'; // Evento para Pusher
    }

    /**
     * Datos que se enviarán a través de Pusher.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message->message,
            'sender' => $this->message->sender->name,
            'receiver' => $this->message->receiver->name,
            'time' => Carbon::now()->toDateTimeString(),
        ];
    }
}
