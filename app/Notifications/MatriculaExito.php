<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class MatriculaExito extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $user;
    protected $email;
    protected $nombre;
    protected $dni;
    protected $userId;
    protected $email_institucional;

    public function __construct($user, $email_institucional, $nombre, $dni, $userId)
    {
        $this->user = $user;
        $this->email = $email_institucional;
        $this->nombre = $nombre;
        $this->dni = $dni;
        $this->userId = $userId;
    }

    /**
     * Métodos para determinar cómo se enviará la notificación
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Construir el mensaje para la notificación (correo)
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Matrícula Exitosa - Bienvenido como Estudiante')
            ->greeting('¡Hola ' . $this->nombre . '!')
            ->line('¡Felicitaciones! Tu matrícula ha sido exitosa y el pago ha sido procesado correctamente.')
            ->line('Tu correo electrónico institucional ha sido creado: ' . $this->email)
            ->line('Puedes iniciar sesión con tu correo institucional y tu cedula como contraseña.')
            ->line('Tu contraseña es: ' . $this->dni)
            ->action('Iniciar sesión', route('login'))
            ->line('Si tienes alguna pregunta, no dudes en contactarnos.')
            ->line('¡Te damos la bienvenida a la comunidad universitaria!');
    }

    /**
     * Opcionalmente, puedes almacenar la notificación en la base de datos si lo deseas.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Tu matrícula ha sido exitosa y el pago ha sido procesado. Ahora puedes acceder con tu correo institucional.',
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
        return 'matricula.exito';
    }

    /**
     * Datos que se enviarán con la notificación en tiempo real.
     */
    public function broadcastWith()
    {
        return [
            'message' => "🎓 Tu matrícula ha sido exitosa y el pago ha sido procesado. Ahora puedes acceder con tu correo institucional: {$this->email}. Tu contraseña temporal es tu número de cédula o pasaporte registrado. Toca esta notificación o cierra sesión para ingresar con tus nuevas credenciales.",
        ];
    }
}
