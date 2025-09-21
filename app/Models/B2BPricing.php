<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class B2BPricing extends Model
{
    protected $fillable = [
        'product_id',
        'pricing_tier',
        'price_net',
        'price_gross',
        'tax_rate',
        'min_quantity',
        'max_quantity',
        'discount_percent',
        'valid_from',
        'valid_to',
        'conditions',
        'is_active',
        'priority',
        'b2_b_client_id',
    ];

    protected $casts = [
        'price_net' => 'decimal:2',
        'price_gross' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'discount_percent' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'conditions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // Relacje
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(B2BClient::class, 'b2_b_client_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query, $date = null)
    {
        $date = $date ?: now();

        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
              ->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_to')
              ->orWhere('valid_to', '>=', $date);
        });
    }

    public function scopeForQuantity($query, int $quantity)
    {
        return $query->where('min_quantity', '<=', $quantity)
                    ->where(function ($q) use ($quantity) {
                        $q->whereNull('max_quantity')
                          ->orWhere('max_quantity', '>=', $quantity);
                    });
    }

    public function scopeForTier($query, string $tier)
    {
        return $query->where('pricing_tier', $tier);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('b2_b_client_id', $clientId);
    }

    public function scopeGeneral($query)
    {
        return $query->whereNull('b2_b_client_id');
    }

    // Accessory
    protected function pricingTierLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->pricing_tier) {
                'standard' => 'Standard',
                'bronze' => 'Brązowy',
                'silver' => 'Srebrny',
                'gold' => 'Złoty',
                'platinum' => 'Platynowy',
                default => 'Standard',
            }
        );
    }

    protected function quantityRange(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->min_quantity .
                         ($this->max_quantity ? ' - ' . $this->max_quantity : '+') .
                         ' szt'
        );
    }

    protected function isCurrentlyValid(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->isValidForDate(now())
        );
    }

    protected function discountedPriceNet(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price_net * (1 - $this->discount_percent / 100)
        );
    }

    protected function discountedPriceGross(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price_gross * (1 - $this->discount_percent / 100)
        );
    }

    // Metody pomocnicze
    public function isValidForDate(Carbon $date): bool
    {
        if ($this->valid_from && $date->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && $date->gt($this->valid_to)) {
            return false;
        }

        return true;
    }

    public function isValidForQuantity(int $quantity): bool
    {
        if ($quantity < $this->min_quantity) {
            return false;
        }

        if ($this->max_quantity && $quantity > $this->max_quantity) {
            return false;
        }

        return true;
    }

    public function calculatePrice(int $quantity): array
    {
        $unitPriceNet = $this->discounted_price_net;
        $unitPriceGross = $this->discounted_price_gross;

        $totalNet = $unitPriceNet * $quantity;
        $totalGross = $unitPriceGross * $quantity;
        $taxAmount = $totalGross - $totalNet;

        return [
            'unit_price_net' => $unitPriceNet,
            'unit_price_gross' => $unitPriceGross,
            'total_net' => $totalNet,
            'total_gross' => $totalGross,
            'tax_amount' => $taxAmount,
            'tax_rate' => $this->tax_rate,
            'discount_percent' => $this->discount_percent,
        ];
    }

    public function createSnapshot(): array
    {
        return [
            'pricing_id' => $this->id,
            'price_net' => $this->price_net,
            'price_gross' => $this->price_gross,
            'tax_rate' => $this->tax_rate,
            'discount_percent' => $this->discount_percent,
            'pricing_tier' => $this->pricing_tier,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'snapshot_date' => now(),
        ];
    }
}
