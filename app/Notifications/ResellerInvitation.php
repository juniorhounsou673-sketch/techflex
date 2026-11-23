<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResellerInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public string $url,
        public int $days,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre accès revendeur TECHFLEX')
            ->markdown('emails.reseller-invitation', [
                'user' => $notifiable,
                'url' => $this->url,
                'days' => $this->days,
            ]);
    }
}
