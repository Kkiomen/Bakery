<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class RecurringOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'b2_b_client_id',
        'name',
        'description',
        'frequency',
        'schedule_config',
        'start_date',
        'end_date',
        'order_items',
        'estimated_total',
        'delivery_address',
        'delivery_postal_code',
        'delivery_city',
        'delivery_notes',
        'preferred_delivery_time_from',
        'preferred_delivery_time_to',
        'auto_confirm',
        'days_before_notification',
        'is_active',
        'total_generated',
        'last_generated_at',
        'next_generation_at',
    ];

    protected $casts = [
        'schedule_config' => 'array',
        'order_items' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_total' => 'decimal:2',
        'preferred_delivery_time_from' => 'datetime:H:i',
        'preferred_delivery_time_to' => 'datetime:H:i',
        'auto_confirm' => 'boolean',
        'is_active' => 'boolean',
        'last_generated_at' => 'datetime',
        'next_generation_at' => 'datetime',
    ];

    // Relacje
    public function client(): BelongsTo
    {
        return $this->belongsTo(B2BClient::class, 'b2_b_client_id');
    }

    public function generatedOrders(): HasMany
    {
        return $this->hasMany(B2BOrder::class, 'recurring_order_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForGeneration($query)
    {
        return $query->active()
                     ->where('next_generation_at', '<=', now())
                     ->where(function ($q) {
                         $q->whereNull('end_date')
                           ->orWhere('end_date', '>=', now()->toDateString());
                     });
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('b2_b_client_id', $clientId);
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    // Accessors
    protected function frequencyLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->frequency) {
                'daily' => 'Codziennie',
                'weekly' => 'Tygodniowo',
                'monthly' => 'Miesięcznie',
                'custom' => 'Niestandardowo',
                default => 'Nieznane',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active ? 'Aktywne' : 'Nieaktywne'
        );
    }

    protected function nextDeliveryDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->next_generation_at ? $this->next_generation_at->toDateString() : null
        );
    }

    // Metody biznesowe
    public function calculateNextGenerationDate(): ?Carbon
    {
        $baseDate = $this->last_generated_at ?? $this->start_date;

        return match($this->frequency) {
            'daily' => $this->calculateDailyNext($baseDate),
            'weekly' => $this->calculateWeeklyNext($baseDate),
            'monthly' => $this->calculateMonthlyNext($baseDate),
            'custom' => $this->calculateCustomNext($baseDate),
            default => null,
        };
    }

    private function calculateDailyNext(Carbon $baseDate): Carbon
    {
        $config = $this->schedule_config;
        $interval = $config['interval'] ?? 1;

        return $baseDate->copy()->addDays($interval);
    }

    private function calculateWeeklyNext(Carbon $baseDate): Carbon
    {
        $config = $this->schedule_config;
        $interval = $config['interval'] ?? 1; // Co ile tygodni
        $weekdays = $config['weekdays'] ?? [1]; // Dni tygodnia (1=poniedziałek)

        $nextDate = $baseDate->copy()->addWeeks($interval);

        // Znajdź najbliższy dzień tygodnia z listy
        foreach ($weekdays as $weekday) {
            $candidate = $nextDate->copy()->startOfWeek()->addDays($weekday - 1);
            if ($candidate > $baseDate) {
                return $candidate;
            }
        }

        // Jeśli żaden dzień w tym tygodniu nie pasuje, przejdź do następnego cyklu
        return $nextDate->copy()->startOfWeek()->addDays($weekdays[0] - 1);
    }

    private function calculateMonthlyNext(Carbon $baseDate): Carbon
    {
        $config = $this->schedule_config;
        $interval = $config['interval'] ?? 1; // Co ile miesięcy
        $dayOfMonth = $config['day_of_month'] ?? $baseDate->day;

        $nextDate = $baseDate->copy()->addMonths($interval);

        // Ustaw dzień miesiąca, obsłuż przypadki gdy dzień nie istnieje
        try {
            $nextDate->day = min($dayOfMonth, $nextDate->daysInMonth);
        } catch (\Exception $e) {
            $nextDate->day = $nextDate->daysInMonth;
        }

        return $nextDate;
    }

    private function calculateCustomNext(Carbon $baseDate): ?Carbon
    {
        $config = $this->schedule_config;

        // Implementacja niestandardowego harmonogramu
        // Może zawierać konkretne daty lub złożone reguły
        if (isset($config['dates'])) {
            $dates = collect($config['dates'])
                ->map(fn($date) => Carbon::parse($date))
                ->filter(fn($date) => $date > $baseDate)
                ->sort();

            return $dates->first();
        }

        return null;
    }

    public function generateOrder(): ?B2BOrder
    {
        if (!$this->is_active) {
            \Log::info('RecurringOrder nie jest aktywne', ['id' => $this->id]);
            return null;
        }

        if (!$this->shouldGenerate()) {
            \Log::info('RecurringOrder nie powinno być wygenerowane', ['id' => $this->id, 'next_generation_at' => $this->next_generation_at]);
            return null;
        }

        $deliveryDate = $this->calculateDeliveryDate();

        $order = B2BOrder::create([
            'recurring_order_id' => $this->id,
            'b2_b_client_id' => $this->b2_b_client_id,
            'order_number' => $this->generateOrderNumber(),
            'order_date' => now()->toDateString(),
            'delivery_date' => $deliveryDate,
            'order_type' => 'recurring',
            'delivery_address' => $this->delivery_address,
            'delivery_postal_code' => $this->delivery_postal_code,
            'delivery_city' => $this->delivery_city,
            'delivery_notes' => $this->delivery_notes,
            'client_notes' => "Zamówienie cykliczne: {$this->name}",
            'payment_method' => 'credit',
            'payment_due_date' => now()->addDays(14),
            'payment_status' => 'pending',
            'status' => $this->auto_confirm ? 'confirmed' : 'pending',
        ]);

        // Dodaj pozycje zamówienia
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($this->order_items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $lineTotalGross = $item['quantity'] * $item['unit_price_gross'];
            $lineTaxAmount = $lineTotalGross - $lineTotal;

            B2BOrderItem::create([
                'b2_b_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit' => 'szt',
                'unit_price_net' => $item['unit_price'],
                'unit_price_gross' => $item['unit_price_gross'],
                'discount_percentage' => $item['discount_percent'] ?? 0,
                'discount_amount' => 0,
                'net_value' => $lineTotal,
                'gross_value' => $lineTotalGross,
                'notes' => null,
                'delivery_status' => 'pending',
            ]);

            $subtotal += $lineTotal;
            $taxAmount += $lineTaxAmount;
        }

        // Aktualizuj totals zamówienia
        $order->update([
            'net_value' => $subtotal,
            'vat_value' => $taxAmount,
            'gross_value' => $subtotal + $taxAmount,
        ]);

        // Aktualizuj statystyki zamówienia cyklicznego
        $this->update([
            'total_generated' => $this->total_generated + 1,
            'last_generated_at' => now(),
            'next_generation_at' => $this->calculateNextGenerationDate(),
        ]);

        return $order;
    }

    public function shouldGenerate(): bool
    {
        // Sprawdź czy nie przekroczono daty końcowej
        if ($this->end_date && $this->end_date < now()->toDateString()) {
            return false;
        }

        // Sprawdź czy nadszedł czas na kolejne zamówienie
        if ($this->next_generation_at && $this->next_generation_at > now()) {
            return false;
        }

        return true;
    }

    public function calculateDeliveryDate(): string
    {
        $config = $this->schedule_config;
        $daysAhead = $config['delivery_days_ahead'] ?? 1;

        return now()->addDays($daysAhead)->toDateString();
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'REC';
        $date = now()->format('Ymd');
        $sequence = str_pad($this->total_generated + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$this->id}-{$date}-{$sequence}";
    }

    public function pause(): void
    {
        $this->update(['is_active' => false]);
    }

    public function resume(): void
    {
        $this->update([
            'is_active' => true,
            'next_generation_at' => $this->calculateNextGenerationDate(),
        ]);
    }

    public function updateSchedule(array $scheduleConfig): void
    {
        $this->update([
            'schedule_config' => $scheduleConfig,
            'next_generation_at' => $this->calculateNextGenerationDate(),
        ]);
    }
}
