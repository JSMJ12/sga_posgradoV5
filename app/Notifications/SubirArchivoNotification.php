<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Postulante;

class SubirArchivoNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $postulante;
    protected $userId;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;

        // Buscar al usuario por su email y extraer el id
        $user = User::where('email', $postulante->correo_electronico)->first();
        $this->userId = $user ? $user->id : null;
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
            'type' => 'PagoRechazado',
            'message' => 'Tu comprobante de pago fue rechazado.',
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }
    public function broadcastAs()
    {
        return 'pago.rechazado';
    }
    public function broadcastWith()
    {
        return [
            'message' => 'Tu comprobante de pago fue rechazado.',
        ];
    }
}