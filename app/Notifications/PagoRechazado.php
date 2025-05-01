<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        
        $fechaPago = optional($this->pago->fecha_pago)->format('d/m/Y') ?? 'Fecha no disponible';

        return (new MailMessage)
            ->subject('Tu comprobante de pago fue rechazado')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Tu comprobante de pago con fecha ' . $fechaPago . ' ha sido rechazado.')
            ->line('Por favor, vuelve a realizar el pago.')
            ->action('Ir a la plataforma', url('/inicio'))
            ->line('Gracias por usar nuestro sistema.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
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

    /**
     * Nombre del evento de transmisión.
     */
    public function broadcastAs()
    {
        return 'pago.rechazado';
    }

    /**
     * Datos que se enviarán con la notificación en tiempo real.
     */
    public function broadcastWith()
    {
        return [
            'message' => 'Tu comprobante de pago fue rechazado.',
        ];
    }
}
