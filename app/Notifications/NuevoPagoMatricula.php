<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Postulante;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class NuevoPagoMatricula extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $postulante;
    protected $userId;

    public function __construct(Postulante $postulante)
    {
        $this->postulante = $postulante;

        $user = User::role('Secretario/a EPSU')->first();
        $this->userId = $user ? $user->id : null;
    }

    /**
     * Canales de entrega de la notificación.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Representación de la notificación en base de datos.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => "El postulante con cédula {$this->postulante->dni}, nombre {$this->postulante->nombre1} {$this->postulante->apellidop}, ha realizado el pago de la matrícula para la maestría {$this->postulante->maestria->nombre}.",
        ];
    }

    /**
     * Definir el canal de transmisión en tiempo real.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    /**
     * Nombre del evento de transmisión.
     */
    public function broadcastAs()
    {
        return 'pago.matricula';
    }

    /**
     * Datos que se enviarán con la notificación en tiempo real.
     */
    public function broadcastWith()
    {
        return [
            'message' => "El postulante con cédula {$this->postulante->dni}, nombre {$this->postulante->nombre1} {$this->postulante->apellidop}, ha realizado el pago de la matrícula para la maestría {$this->postulante->maestria->nombre}.",
        ];
    }
}
