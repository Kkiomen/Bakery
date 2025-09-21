<?php

namespace App\Notifications;

use App\Models\B2BOrder;
use App\Models\RecurringOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class B2BRecurringOrderGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public B2BOrder $order,
        public RecurringOrder $recurringOrder
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->email_notifications ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nowe zamówienie cykliczne: {$this->order->order_number}")
            ->greeting("Dzień dobry!")
            ->line("Zostało wygenerowane nowe zamówienie z Twojego harmonogramu cyklicznego.")
            ->line("**Zamówienie cykliczne:** {$this->recurringOrder->name}")
            ->line("**Numer zamówienia:** {$this->order->order_number}")
            ->line("**Wartość:** " . number_format($this->order->total_amount, 2) . " zł")
            ->line("**Data dostawy:** " . $this->order->delivery_date->format('d.m.Y'))
            ->when(!$this->recurringOrder->auto_confirm, function ($message) {
                $message->line("⚠️ **Zamówienie wymaga potwierdzenia** - zaloguj się do panelu, aby je potwierdzić.");
            })
            ->when($this->recurringOrder->auto_confirm, function ($message) {
                $message->line("✅ Zamówienie zostało automatycznie potwierdzone.");
            })
            ->action('Zobacz zamówienie', route('b2b.orders.show', $this->order))
            ->action('Zarządzaj zamówieniami cyklicznymi', route('b2b.recurring-orders'))
            ->line('Dziękujemy za współpracę!')
            ->salutation('Pozdrawiamy, Zespół Piekarni');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Nowe zamówienie cykliczne',
            'message' => "Wygenerowano zamówienie {$this->order->order_number} z harmonogramu \"{$this->recurringOrder->name}\"",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'recurring_order_id' => $this->recurringOrder->id,
            'recurring_order_name' => $this->recurringOrder->name,
            'total_amount' => $this->order->total_amount,
            'delivery_date' => $this->order->delivery_date->toDateString(),
            'auto_confirmed' => $this->recurringOrder->auto_confirm,
            'requires_confirmation' => !$this->recurringOrder->auto_confirm,
            'action_url' => route('b2b.orders.show', $this->order),
            'icon' => '🔄',
            'priority' => $this->recurringOrder->auto_confirm ? 'normal' : 'high',
        ];
    }
}
