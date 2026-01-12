<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmployeeInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public string $inviteUrl) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitación a BeautyCRM – Conecta tu Google Calendar')
            ->greeting('Hola ' . ($notifiable->name ?? '') . ' 👋')
            ->line('Has sido registrado(a) como empleado(a) en BeautyCRM.')
            ->line('Para activar tu acceso y conectar tu Google Calendar, da click en el botón:')
            ->action('Activar y conectar Google', $this->inviteUrl)
            ->line('Este enlace expira por seguridad.');
    }
}
