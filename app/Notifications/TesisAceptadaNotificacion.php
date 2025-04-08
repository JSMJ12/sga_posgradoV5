<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;

class TesisAceptadaNotificacion extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $tesis;
    protected $usuario;  
    protected $email;    

    public function __construct($tesis, $usuario)
    {
        $this->tesis = $tesis;
        $this->usuario = $usuario;  
        $this->email = $usuario->email;  
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notificación de Aprobación de Tema de Tesis')
            ->greeting('¡Hola, ' . $this->usuario->nombre1 . '!')
            ->line('El tema de tesis "' . $this->tesis->tema . '" ha sido aceptado.')
            ->action('Ver Detalles', route('tesis.create')) 
            ->line('¡Felicitaciones por tu avance en el proceso de titulación!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'El tema de tesis ha sido aceptado.',
        ];
    }
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->usuario->id);
    }

    /**
     * Nombre del evento de transmisión.
     */
    public function broadcastAs()
    {
        return 'tesis.aceptada';
    }

    /**
     * Datos que se enviarán con la notificación en tiempo real.
     */
    public function broadcastWith()
    {
        return [
            'message' => "📚 Tu tema de tesis \"{$this->tesis->tema}\" ha sido aprobado. ",
        ];
    }
}
