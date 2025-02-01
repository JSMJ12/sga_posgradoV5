<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Postulante;

class SubirArchivoNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $postulante;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recordatorio: Subir Archivo')
            ->line('Hola ' . $this->postulante->nombre1 . ',')
            ->line('Recuerda subir tus archivos para completar tu proceso de postulación.')
            ->action('Ir al Dashboard', url('/inicio'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'SubirArchivoNotification',
            'message' => 'Recuerda subir tus archivos para completar tu proceso de postulación.',
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('canal_p');
    }

    public function broadcastWith()
    {
        return [
            'postulante' => $this->postulante,
            'message' => 'Recuerda subir tus archivos para completar tu proceso de postulación.',
        ];
    }
}