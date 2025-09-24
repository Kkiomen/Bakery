<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class B2BOrder extends Model
{
    protected $fillable = [
        'order_number',
        'b2_b_client_id',
        'order_date',
        'delivery_date',
        'delivery_time_from',
        'delivery_time_to',
        'status',
        'order_type',
        'recurring_settings',
        'delivery_address',
        'delivery_postal_code',
        'delivery_city',
        'delivery_notes',
        'subtotal',
        'tax_amount',
        'delivery_cost',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_due_date',
        'customer_notes',
        'internal_notes',
        'special_requirements',
        'confirmed_at',
        'production_started_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'delivery_time_from' => 'datetime:H:i',
        'delivery_time_to' => 'datetime:H:i',
        'recurring_settings' => 'array',
        'special_requirements' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_due_date' => 'date',
        'confirmed_at' => 'datetime',
        'production_started_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relacje
    public function client(): BelongsTo
    {
        return $this->belongsTo(B2BClient::class, 'b2_b_client_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(B2BOrderItem::class);
    }

    public function recurringOrder(): BelongsTo
    {
        return $this->belongsTo(RecurringOrder::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    // Akcesory
    public function getNumerZamowieniaAttribute()
    {
        return $this->order_number;
    }

    public function getWartoscBruttoAttribute()
    {
        return $this->total_amount;
    }

    public function getDataRealizacjiAttribute()
    {
        return $this->delivery_date ? Carbon::parse($this->delivery_date) : null;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('b2_b_client_id', $clientId);
    }

    public function scopeByDeliveryDate($query, $date)
    {
        return $query->whereDate('delivery_date', $date);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'pending']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'in_production', 'ready', 'shipped']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    // Accessory
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'draft' => 'Szkic',
                'pending' => 'Oczekujące',
                'confirmed' => 'Potwierdzone',
                'in_production' => 'W produkcji',
                'ready' => 'Gotowe',
                'shipped' => 'Wysłane',
                'delivered' => 'Dostarczone',
                'cancelled' => 'Anulowane',
                'returned' => 'Zwrócone',
                default => 'Nieznany',
            }
        );
    }

    protected function orderTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->order_type) {
                'one_time' => 'Jednorazowe',
                'recurring' => 'Cykliczne',
                'standing' => 'Stałe',
                default => 'Nieznane',
            }
        );
    }

    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->payment_method) {
                'transfer' => 'Przelew',
                'card' => 'Karta',
                'cash' => 'Gotówka',
                'credit' => 'Na kredyt',
                default => 'Nieznany',
            }
        );
    }

    protected function paymentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->payment_status) {
                'pending' => 'Oczekująca',
                'paid' => 'Opłacone',
                'overdue' => 'Przeterminowane',
                'cancelled' => 'Anulowane',
                default => 'Nieznany',
            }
        );
    }

    protected function fullDeliveryAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->delivery_address . ', ' . $this->delivery_postal_code . ' ' . $this->delivery_city)
        );
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payment_due_date &&
                         $this->payment_status !== 'paid' &&
                         Carbon::parse($this->payment_due_date)->isPast()
        );
    }

    // Metody pomocnicze
    public function generateOrderNumber(): string
    {
        $prefix = 'B2B';
        $date = now()->format('Ymd');
        $sequence = str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'pending', 'confirmed']);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('line_total');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->delivery_cost - $this->discount_amount;
        $this->save();
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function startProduction(): void
    {
        $this->update([
            'status' => 'in_production',
            'production_started_at' => now(),
        ]);
    }

    public function markAsReady(): void
    {
        $this->update(['status' => 'ready']);
    }

    public function ship(): void
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    public function deliver(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'internal_notes' => $this->internal_notes . "\nAnulowane: " . $reason,
        ]);
    }
}
