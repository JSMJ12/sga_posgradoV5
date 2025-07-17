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

use NotificationChannels\WebPush\WebPushMessage; // Importa WebPushMessage

class SubirArchivoNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $postulante;
    protected $userId;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;

        $user = User::where('email', $postulante->correo_electronico)->first();
        $this->userId = $user ? $user->id : null;
    }

    public function via($notifiable)
    {
        // Añade 'webpush' para notificaciones push
        return ['mail', 'database', 'broadcast', 'webpush'];
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
            'type' => 'SubirArchivo',
            'message' => 'Recuerda subir tus archivos para completar tu proceso de postulación.',
        ];
    }

    // Aquí agregamos el método para la notificación webpush
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Recordatorio: Subir Archivo')
            ->icon('/icono-notificacion.png') // Opcional: ruta al icono en public
            ->body('Hola ' . $this->postulante->nombre1 . ', recuerda subir tus archivos para completar el proceso.')
            ->action('Ver detalles', 'view_app')
            ->data(['url' => url('/inicio')]); // Puedes enviar datos extra, como url para abrir al click
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
