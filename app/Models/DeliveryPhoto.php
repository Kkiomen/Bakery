<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class DeliveryPhoto extends Model
{
    protected $fillable = [
        'delivery_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'opis',
        'typ_zdjecia',
        'kolejnosc',
        'latitude',
        'longitude',
        'data_wykonania',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'kolejnosc' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'data_wykonania' => 'datetime',
    ];

    // Relacje
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    // Akcesory
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url($this->file_path)
        );
    }

    protected function fullUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => asset('storage/' . $this->file_path)
        );
    }

    // Alternative URL method similar to ProductImage
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    protected function typZdjeciaLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->typ_zdjecia) {
                'produkty' => 'Zdjęcia produktów',
                'dowod_dostawy' => 'Dowód dostawy',
                'problem' => 'Problem/uszkodzenie',
                'lokalizacja' => 'Lokalizacja',
                'inne' => 'Inne',
                default => $this->typ_zdjecia
            }
        );
    }

    protected function fileSizeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatBytes($this->file_size)
        );
    }

    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: fn () => str_starts_with($this->mime_type, 'image/')
        );
    }

    // Metody pomocnicze
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function delete()
    {
        // Usuń plik z dysku
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        return parent::delete();
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('typ_zdjecia', $type);
    }

    public function scopeOrderedBySequence($query)
    {
        return $query->orderBy('kolejnosc')->orderBy('created_at');
    }

    public function scopeProductPhotos($query)
    {
        return $query->where('typ_zdjecia', 'produkty');
    }

    public function scopeDeliveryProofPhotos($query)
    {
        return $query->where('typ_zdjecia', 'dowod_dostawy');
    }

    public function scopeProblemPhotos($query)
    {
        return $query->where('typ_zdjecia', 'problem');
    }
}

