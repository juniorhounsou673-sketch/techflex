<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PerformanceReport extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, array{name: string, quantity: int, amount: int}>  $topProducts
     * @param  array<int, array{name: string, code: string, orders: int, amount: int, profit: int}>  $topResellers
     */
    public function __construct(
        public string $periodLabel,
        public string $periodDates,
        public array $topProducts,
        public array $topResellers,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tops '.$this->periodLabel.' — TECHFLEX')
            ->markdown('emails.performance-report', [
                'notifiable' => $notifiable,
                'periodLabel' => $this->periodLabel,
                'periodDates' => $this->periodDates,
                'topProducts' => $this->topProducts,
                'topResellers' => $this->topResellers,
            ]);
    }
}
