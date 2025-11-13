<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // URL de tu frontend donde el usuario reseteará la contraseña
        $frontendUrl = 'http://localhost:3000/reset-password'; // O la URL de tu app React

        // Construimos la URL completa con el token y el email
        $resetUrl = $frontendUrl . '?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
                    ->subject('Recuperación de Contraseña')
                    ->line('Has recibido este correo porque solicitaste un cambio de contraseña para tu cuenta.')
                    ->action('Resetear Contraseña', $resetUrl)
                    ->line('Este enlace de recuperación expirará en 60 minutos.')
                    ->line('Si no solicitaste este cambio, puedes ignorar este correo.');
    }
}