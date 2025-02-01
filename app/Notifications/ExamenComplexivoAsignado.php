<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExamenComplexivoAsignado extends Notification
{
    use Queueable;

    public $fecha;
    public $lugar;
    public $hora;

    /**
     * Crear una nueva instancia de la notificación.
     *
     * @param string $fecha
     * @param string $lugar
     * @param string $hora
     */
    public function __construct($fecha, $lugar, $hora)
    {
        $this->fecha = $fecha;
        $this->lugar = $lugar;
        $this->hora = $hora;
    }

    /**
     * Canal de entrega: Base de datos o correo electrónico.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Mensaje para el correo electrónico.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Detalles de tu Examen Complexivo')
            ->line('Se te ha asignado la fecha, lugar y hora de tu examen complexivo.')
            ->line("Fecha: {$this->fecha}")
            ->line("Lugar: {$this->lugar}")
            ->line("Hora: {$this->hora}")
            ->action('Ver Detalles', url('/examen-complexivo'))
            ->line('¡Te deseamos éxito!');
    }

    /**
     * Mensaje para la base de datos.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => 'Se te ha asignado la fecha, lugar y hora de tu examen complexivo.',
            'fecha' => $this->fecha,  
            'lugar' => $this->lugar,  
            'hora' => $this->hora,   
        ];
    }
}
