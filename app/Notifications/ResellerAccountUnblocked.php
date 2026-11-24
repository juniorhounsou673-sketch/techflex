<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResellerAccountUnblocked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $resellerName,
        public string $resellerCode,
        public string $unblockedAt,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Compte revendeur TECHFLEX réactivé')
            ->markdown('emails.reseller-account-unblocked', [
                'notifiable' => $notifiable,
                'resellerName' => $this->resellerName,
                'resellerCode' => $this->resellerCode,
                'unblockedAt' => $this->unblockedAt,
            ]);
    }
}
