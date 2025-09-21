<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class B2BOrderItem extends Model
{
    protected $fillable = [
        'b2_b_order_id',
        'product_id',
        'product_name',
        'product_sku',
        'product_description',
        'quantity',
        'delivered_quantity',
        'unit',
        'unit_weight',
        'unit_price',
        'unit_price_gross',
        'discount_percent',
        'discount_amount',
        'line_total',
        'line_total_gross',
        'tax_rate',
        'tax_amount',
        'status',
        'notes',
        'customizations',
        'requested_delivery_date',
        'production_order_item_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'delivered_quantity' => 'integer',
        'unit_weight' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'unit_price_gross' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'line_total_gross' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'customizations' => 'array',
        'requested_delivery_date' => 'date',
    ];

    // Relacje
    public function order(): BelongsTo
    {
        return $this->belongsTo(B2BOrder::class, 'b2_b_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productionOrderItem(): BelongsTo
    {
        return $this->belongsTo(ProductionOrderItem::class);
    }

    // Accessory
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'pending' => 'Oczekująca',
                'confirmed' => 'Potwierdzona',
                'in_production' => 'W produkcji',
                'ready' => 'Gotowa',
                'delivered' => 'Dostarczona',
                'cancelled' => 'Anulowana',
                default => 'Nieznany',
            }
        );
    }

    protected function totalWeight(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->unit_weight ? $this->quantity * $this->unit_weight : null
        );
    }

    protected function remainingQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity - $this->delivered_quantity
        );
    }

    protected function isFullyDelivered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->delivered_quantity >= $this->quantity
        );
    }

    protected function deliveryProgress(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity > 0 ? round(($this->delivered_quantity / $this->quantity) * 100, 2) : 0
        );
    }

    // Metody pomocnicze
    public function calculateLineTotals(): void
    {
        // Oblicz wartość netto
        $lineSubtotal = $this->quantity * $this->unit_price;
        $this->line_total = $lineSubtotal - $this->discount_amount;

        // Oblicz VAT
        $this->tax_amount = $this->line_total * ($this->tax_rate / 100);

        // Oblicz wartość brutto
        $this->line_total_gross = $this->line_total + $this->tax_amount;

        $this->save();
    }

    public function applyPricing(B2BPricing $pricing): void
    {
        $this->unit_price = $pricing->price_net;
        $this->unit_price_gross = $pricing->price_gross;
        $this->tax_rate = $pricing->tax_rate;
        $this->discount_percent = $pricing->discount_percent;

        // Oblicz kwotę rabatu
        $this->discount_amount = ($this->quantity * $this->unit_price) * ($this->discount_percent / 100);

        $this->calculateLineTotals();
    }

    public function updateDeliveredQuantity(int $deliveredQuantity): void
    {
        $this->delivered_quantity = min($deliveredQuantity, $this->quantity);

        if ($this->is_fully_delivered) {
            $this->status = 'delivered';
        }

        $this->save();
    }
}
