<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Carbon\Carbon;

class PagoRechazado extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $pago;
    protected $user;
    protected $userId;

    public function __construct($pago, $user)
    {
        $this->pago = $pago;
        $this->user = $user;
        $this->userId = $user->id;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $fechaPago = 'Fecha no disponible';
        if ($this->pago && $this->pago->fecha_pago) {
            $fechaPago = Carbon::parse($this->pago->fecha_pago)->format('d/m/Y');
        }


        return (new MailMessage)
            ->subject('Tu comprobante de pago fue rechazado')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Tu comprobante de pago con fecha ' . $fechaPago . ' ha sido rechazado.')
            ->line('Por favor, vuelve a realizar el pago.')
            ->action('Ir a la plataforma', url('/inicio'))
            ->line('Gracias por usar nuestro sistema.');
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
        return new PrivateChannel('user.' . $this->userId);
    }
    public function broadcastAs()
    {
        return 'subir.archivo';
    }
    public function broadcastWith()
    {
        return [
            'message' => 'Recuerda subir tus archivos para completar tu proceso de postulación.',
        ];
    }
}
