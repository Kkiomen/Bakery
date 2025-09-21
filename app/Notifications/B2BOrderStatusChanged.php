<?php

namespace App\Notifications;

use App\Models\B2BOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class B2BOrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public B2BOrder $order,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];

        // Dodaj email jeśli klient ma włączone powiadomienia email
        if ($notifiable->email_notifications ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $statusMessage = $this->getStatusMessage();

        return (new MailMessage)
            ->subject("Zmiana statusu zamówienia {$this->order->order_number}")
            ->greeting("Dzień dobry!")
            ->line("Status Twojego zamówienia {$this->order->order_number} został zmieniony.")
            ->line("**Nowy status:** {$statusMessage}")
            ->line("**Wartość zamówienia:** " . number_format($this->order->total_amount, 2) . " zł")
            ->when($this->order->delivery_date, function ($message) {
                $message->line("**Data dostawy:** " . $this->order->delivery_date->format('d.m.Y'));
            })
            ->action('Zobacz szczegóły zamówienia', route('b2b.orders.show', $this->order))
            ->line('Dziękujemy za współpracę!')
            ->salutation('Pozdrawiamy, Zespół Piekarni');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Zmiana statusu zamówienia',
            'message' => "Zamówienie {$this->order->order_number} ma nowy status: {$this->getStatusMessage()}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'total_amount' => $this->order->total_amount,
            'delivery_date' => $this->order->delivery_date?->toDateString(),
            'action_url' => route('b2b.orders.show', $this->order),
            'icon' => $this->getStatusIcon(),
            'priority' => $this->getStatusPriority(),
        ];
    }

    private function getStatusMessage(): string
    {
        return match($this->newStatus) {
            'pending' => 'Oczekujące na potwierdzenie',
            'confirmed' => 'Potwierdzone',
            'in_production' => 'W produkcji',
            'ready' => 'Gotowe do odbioru',
            'shipped' => 'Wysłane',
            'delivered' => 'Dostarczone',
            'cancelled' => 'Anulowane',
            default => 'Status nieznany',
        };
    }

    private function getStatusIcon(): string
    {
        return match($this->newStatus) {
            'pending' => '⏳',
            'confirmed' => '✅',
            'in_production' => '🏭',
            'ready' => '📦',
            'shipped' => '🚚',
            'delivered' => '✅',
            'cancelled' => '❌',
            default => 'ℹ️',
        };
    }

    private function getStatusPriority(): string
    {
        return match($this->newStatus) {
            'cancelled' => 'high',
            'delivered' => 'high',
            'shipped' => 'high',
            'ready' => 'medium',
            'confirmed' => 'medium',
            default => 'normal',
        };
    }
}
