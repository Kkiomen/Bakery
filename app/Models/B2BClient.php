<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class B2BClient extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_name',
        'nip',
        'regon',
        'email',
        'password',
        'address',
        'postal_code',
        'city',
        'phone',
        'website',
        'contact_person',
        'contact_phone',
        'contact_email',
        'business_type',
        'business_description',
        'delivery_addresses',
        'preferred_delivery_time',
        'delivery_days',
        'status',
        'pricing_tier',
        'credit_limit',
        'current_balance',
        'notes',
        'contract_start_date',
        'contract_end_date',
        'email_notifications',
        'sms_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'delivery_addresses' => 'array',
        'delivery_days' => 'array',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
    ];

    // Relacje
    public function orders(): HasMany
    {
        return $this->hasMany(B2BOrder::class);
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(B2BPricing::class);
    }

    public function recurringOrders(): HasMany
    {
        return $this->hasMany(RecurringOrder::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByBusinessType($query, $type)
    {
        return $query->where('business_type', $type);
    }

    public function scopeByPricingTier($query, $tier)
    {
        return $query->where('pricing_tier', $tier);
    }

    // Accessory
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->address . ', ' . $this->postal_code . ' ' . $this->city)
        );
    }

    protected function businessTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->business_type) {
                'hotel' => 'Hotel',
                'restaurant' => 'Restauracja',
                'cafe' => 'Kawiarnia',
                'shop' => 'Sklep',
                'catering' => 'Catering',
                'other' => 'Inne',
                default => 'Nieznane',
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'pending' => 'Oczekujący',
                'active' => 'Aktywny',
                'suspended' => 'Zawieszony',
                'inactive' => 'Nieaktywny',
                default => 'Nieznany',
            }
        );
    }

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

    protected function availableCredit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->credit_limit - $this->current_balance
        );
    }

    // Metody pomocnicze
    public function canPlaceOrder(): bool
    {
        return $this->status === 'active' && $this->available_credit >= 0;
    }

    public function getPriceForProduct(Product $product, int $quantity = 1): ?B2BPricing
    {
        // Najpierw sprawdź ceny dedykowane dla tego klienta
        $pricing = $this->pricings()
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->where('min_quantity', '<=', $quantity)
            ->where(function($query) use ($quantity) {
                $query->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('priority', 'desc')
            ->first();

        if ($pricing) {
            return $pricing;
        }

        // Następnie sprawdź ceny dla poziomu cenowego klienta
        return B2BPricing::where('product_id', $product->id)
            ->where('pricing_tier', $this->pricing_tier)
            ->whereNull('b2_b_client_id')
            ->where('is_active', true)
            ->where('min_quantity', '<=', $quantity)
            ->where(function($query) use ($quantity) {
                $query->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('priority', 'desc')
            ->first();
    }
}
