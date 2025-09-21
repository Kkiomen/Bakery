<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DeliveryItem extends Model
{
    protected $fillable = [
        'delivery_id',
        'product_id',
        'production_order_item_id',
        'nazwa_produktu',
        'ilosc',
        'jednostka',
        'ilosc_dostarczona',
        'waga_kg',
        'uwagi',
        'status',
    ];

    protected $casts = [
        'ilosc' => 'integer',
        'ilosc_dostarczona' => 'integer',
        'waga_kg' => 'decimal:3',
    ];

    // Relacje
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productionOrderItem(): BelongsTo
    {
        return $this->belongsTo(ProductionOrderItem::class);
    }

    // Akcesory
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujacy' => 'Oczekujący',
                'przygotowany' => 'Przygotowany',
                'dostarczony' => 'Dostarczony',
                'brakuje' => 'Brakuje',
                'uszkodzony' => 'Uszkodzony',
                default => $this->status
            }
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujacy' => 'gray',
                'przygotowany' => 'blue',
                'dostarczony' => 'green',
                'brakuje' => 'red',
                'uszkodzony' => 'orange',
                default => 'gray'
            }
        );
    }

    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc > 0 ? round(($this->ilosc_dostarczona / $this->ilosc) * 100) : 0
        );
    }

    protected function isFullyDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc_dostarczona >= $this->ilosc
        );
    }

    protected function isPartiallyDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc_dostarczona > 0 && $this->ilosc_dostarczona < $this->ilosc
        );
    }

    protected function remainingQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->ilosc - $this->ilosc_dostarczona)
        );
    }

    // Metody pomocnicze
    public function canBeDelivered(): bool
    {
        return $this->status === 'przygotowany' && $this->remaining_quantity > 0;
    }

    public function markAsDelivered(?int $quantity = null): void
    {
        $deliveredQuantity = $quantity ?? $this->remaining_quantity;
        $newTotal = min($this->ilosc, $this->ilosc_dostarczona + $deliveredQuantity);

        $this->update([
            'ilosc_dostarczona' => $newTotal,
            'status' => $newTotal >= $this->ilosc ? 'dostarczony' : 'brakuje'
        ]);
    }

    public function markAsMissing(?string $reason = null): void
    {
        $this->update([
            'status' => 'brakuje',
            'uwagi' => $reason ? "Brakuje: {$reason}" : 'Brakuje'
        ]);
    }

    public function markAsDamaged(?string $description = null): void
    {
        $this->update([
            'status' => 'uszkodzony',
            'uwagi' => $description ? "Uszkodzony: {$description}" : 'Uszkodzony'
        ]);
    }
}
