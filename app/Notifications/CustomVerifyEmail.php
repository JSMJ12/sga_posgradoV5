<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('¡Activa tu cuenta en nuestro sistema!')
            ->greeting('👋 ¡Hola ' . $notifiable->name . '!')
            ->line('Gracias por registrarte. Estamos encantados de tenerte con nosotros. 🥳')
            ->line('Antes de comenzar, necesitamos que confirmes tu dirección de correo electrónico para activar tu cuenta.')
            ->action('✔️ Verificar mi correo', $verifyUrl)
            ->line('Si tú no realizaste este registro, puedes ignorar este mensaje con total confianza.')
            ->salutation('Saludos cordiales, 🌟' . PHP_EOL . 'El equipo de soporte');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }
}
