<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DeliverySignature extends Model
{
    protected $fillable = [
        'delivery_id',
        'signature_data',
        'signer_name',
        'signer_position',
        'signature_date',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'uwagi',
    ];

    protected $casts = [
        'signature_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relacje
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    // Akcesory
    protected function signatureImage(): Attribute
    {
        return Attribute::make(
            get: fn () => 'data:image/png;base64,' . $this->signature_data
        );
    }

    protected function hasSignature(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->signature_data)
        );
    }

    protected function signerInfo(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->signer_name . ($this->signer_position ? ' (' . $this->signer_position . ')' : ''))
        );
    }

    protected function locationInfo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->latitude && $this->longitude ?
                         "Lat: {$this->latitude}, Lng: {$this->longitude}" : null
        );
    }

    // Metody pomocnicze
    public function isValid(): bool
    {
        return !empty($this->signature_data) &&
               !empty($this->signer_name) &&
               !empty($this->signature_date);
    }

    public function getSignatureSize(): int
    {
        return strlen($this->signature_data);
    }

    public function hasLocation(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->whereNotNull('signature_data')
                    ->whereNotNull('signer_name')
                    ->whereNotNull('signature_date');
    }

    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('signature_date', $date);
    }
}

