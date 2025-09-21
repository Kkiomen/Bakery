<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Contractor extends Model
{
    protected $fillable = [
        'nazwa',
        'nip',
        'regon',
        'adres',
        'kod_pocztowy',
        'miasto',
        'telefon',
        'email',
        'osoba_kontaktowa',
        'telefon_kontaktowy',
        'typ',
        'aktywny',
        'uwagi',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'aktywny' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Accessor dla pełnego adresu
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->adres . ', ' . $this->kod_pocztowy . ' ' . $this->miasto)
        );
    }

    // Accessor dla linku Google Maps
    protected function googleMapsUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => 'https://www.google.com/maps/dir/?api=1&destination=' .
                         urlencode($this->full_address)
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('aktywny', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('typ', $type);
    }

    public function scopeClients($query)
    {
        return $query->whereIn('typ', ['klient', 'obydwa']);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('typ', ['dostawca', 'obydwa']);
    }

    // Relacje
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'contractor_id');
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(\App\Models\ProductionOrder::class);
    }

    // Metody pomocnicze
    public function getTypeLabelAttribute(): string
    {
        return match($this->typ) {
            'klient' => 'Klient',
            'dostawca' => 'Dostawca',
            'obydwa' => 'Klient i Dostawca',
            default => 'Nieznany',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->aktywny ? 'Aktywny' : 'Nieaktywny';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->aktywny ? 'green' : 'red';
    }
}
