<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResellerAccountBlocked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $resellerName,
        public string $resellerCode,
        public ?string $reason,
        public string $blockedAt,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Compte revendeur TECHFLEX bloqué')
            ->markdown('emails.reseller-account-blocked', [
                'notifiable' => $notifiable,
                'resellerName' => $this->resellerName,
                'resellerCode' => $this->resellerCode,
                'reason' => $this->reason,
                'blockedAt' => $this->blockedAt,
            ]);
    }
}
