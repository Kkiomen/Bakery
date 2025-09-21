<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Delivery extends Model
{
    protected $fillable = [
        'numer_dostawy',
        'production_order_id',
        'driver_id',
        'status',
        'priorytet',
        'data_dostawy',
        'godzina_planowana',
        'godzina_rozpoczecia',
        'godzina_zakonczenia',
        'klient_nazwa',
        'klient_adres',
        'klient_telefon',
        'klient_email',
        'osoba_kontaktowa',
        'telefon_kontaktowy',
        'kod_pocztowy',
        'miasto',
        'latitude',
        'longitude',
        'uwagi_dostawy',
        'uwagi_kierowcy',
        'kolejnosc_dostawy',
        'dystans_km',
        'czas_dojazdu_min',
    ];

    protected $casts = [
        'data_dostawy' => 'date',
        'godzina_planowana' => 'datetime',
        'godzina_rozpoczecia' => 'datetime',
        'godzina_zakonczenia' => 'datetime',
        'kolejnosc_dostawy' => 'integer',
        'dystans_km' => 'decimal:2',
        'czas_dojazdu_min' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Scope dla filtrowania po statusie
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope dla filtrowania po dacie
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('data_dostawy', $date);
    }

    // Relacje
    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DeliveryPhoto::class);
    }

    public function signature(): HasMany
    {
        return $this->hasMany(DeliverySignature::class);
    }

    // Akcesory
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujaca' => 'Oczekująca',
                'przypisana' => 'Przypisana',
                'w_drodze' => 'W drodze',
                'dostarczona' => 'Dostarczona',
                'anulowana' => 'Anulowana',
                'problem' => 'Problem',
                default => $this->status
            }
        );
    }

    protected function priorytetLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->priorytet) {
                'niski' => 'Niski',
                'normalny' => 'Normalny',
                'wysoki' => 'Wysoki',
                'pilny' => 'Pilny',
                default => $this->priorytet
            }
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujaca' => 'gray',
                'przypisana' => 'blue',
                'w_drodze' => 'yellow',
                'dostarczona' => 'green',
                'anulowana' => 'red',
                'problem' => 'orange',
                default => 'gray'
            }
        );
    }

    protected function priorytetColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->priorytet) {
                'niski' => 'gray',
                'normalny' => 'blue',
                'wysoki' => 'orange',
                'pilny' => 'red',
                default => 'gray'
            }
        );
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->klient_adres . ', ' . $this->kod_pocztowy . ' ' . $this->miasto)
        );
    }

    protected function googleMapsUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => 'https://www.google.com/maps/dir/?api=1&destination=' .
                         urlencode($this->full_address)
        );
    }

    // Scopes (duplikaty usunięte - są już wcześniej w pliku)

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priorytet', $priority);
    }

    public function scopeOrderedBySequence($query)
    {
        return $query->orderBy('kolejnosc_dostawy')->orderBy('godzina_planowana');
    }

    public function scopeForToday($query)
    {
        return $query->where('data_dostawy', now()->toDateString());
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['oczekujaca', 'przypisana']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'w_drodze');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'dostarczona');
    }

    // Metody pomocnicze
    public function generateDeliveryNumber(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now())->count() + 1;
        return "DOS-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function canBeAssigned(): bool
    {
        return $this->status === 'oczekujaca';
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'przypisana';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'w_drodze';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['oczekujaca', 'przypisana']);
    }

    public function assignToDriver(int $driverId): void
    {
        if ($this->canBeAssigned()) {
            $this->update([
                'driver_id' => $driverId,
                'status' => 'przypisana',
            ]);
        }
    }

    public function startDelivery(): void
    {
        if ($this->canBeStarted()) {
            $this->update([
                'status' => 'w_drodze',
                'godzina_rozpoczecia' => now(),
            ]);
        }
    }

    public function completeDelivery(): void
    {
        if ($this->canBeCompleted()) {
            $this->update([
                'status' => 'dostarczona',
                'godzina_zakonczenia' => now(),
            ]);
        }
    }

    public function cancelDelivery(?string $reason = null): void
    {
        if ($this->canBeCancelled()) {
            $this->update([
                'status' => 'anulowana',
                'uwagi_kierowcy' => $reason ? "Anulowano: {$reason}" : 'Anulowano',
            ]);
        }
    }

    public function reportProblem(string $description): void
    {
        $this->update([
            'status' => 'problem',
            'uwagi_kierowcy' => $description,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->data_dostawy < now()->toDateString() &&
               !in_array($this->status, ['dostarczona', 'anulowana']);
    }

    public function getDeliveryDuration(): ?int
    {
        if ($this->godzina_rozpoczecia && $this->godzina_zakonczenia) {
            return $this->godzina_rozpoczecia->diffInMinutes($this->godzina_zakonczenia);
        }
        return null;
    }

    public function getTotalItems(): int
    {
        return $this->items()->sum('ilosc');
    }

    public function getTotalDelivered(): int
    {
        return $this->items()->sum('ilosc_dostarczona');
    }

    public function getDeliveryProgress(): int
    {
        $total = $this->getTotalItems();
        if ($total === 0) return 0;

        return round(($this->getTotalDelivered() / $total) * 100);
    }

    // Boot method dla automatycznego generowania numeru dostawy
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($delivery) {
            if (empty($delivery->numer_dostawy)) {
                $delivery->numer_dostawy = $delivery->generateDeliveryNumber();
            }
        });
    }
}
