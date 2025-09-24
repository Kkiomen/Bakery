<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class ProductionOrder extends Model
{
    protected $fillable = [
        'numer_zlecenia',
        'nazwa',
        'opis',
        'data_produkcji',
        'user_id',
        'status',
        'priorytet',
        'typ_zlecenia',
        'klient',
        'contractor_id',
        'uwagi',
        'data_rozpoczecia',
        'data_zakonczenia',
        'b2b_order_id',
    ];

    protected $casts = [
        'data_produkcji' => 'date',
        'data_rozpoczecia' => 'datetime',
        'data_zakonczenia' => 'datetime',
    ];

    // Relacje
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionOrderItem::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Contractor::class);
    }

    public function b2bOrder(): BelongsTo
    {
        return $this->belongsTo(B2BOrder::class);
    }

    // Akcesory
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujace' => 'Oczekujące',
                'w_produkcji' => 'W produkcji',
                'zakonczone' => 'Zakończone',
                'anulowane' => 'Anulowane',
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

    protected function typZleceniaLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->typ_zlecenia) {
                'wewnetrzne' => 'Wewnętrzne',
                'sklep' => 'Sklep',
                'b2b' => 'B2B',
                'hotel' => 'Hotel',
                'inne' => 'Inne',
                default => $this->typ_zlecenia
            }
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujace' => 'blue',
                'w_produkcji' => 'yellow',
                'zakonczone' => 'green',
                'anulowane' => 'red',
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

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('data_produkcji', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priorytet', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('typ_zlecenia', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nazwa', 'like', "%{$search}%")
              ->orWhere('numer_zlecenia', 'like', "%{$search}%")
              ->orWhere('klient', 'like', "%{$search}%")
              ->orWhere('opis', 'like', "%{$search}%");
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('data_produkcji', '>=', now()->toDateString());
    }

    public function scopeOverdue($query)
    {
        return $query->where('data_produkcji', '<', now()->toDateString())
                    ->whereIn('status', ['oczekujace', 'w_produkcji']);
    }

    // Metody pomocnicze
    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now())->count() + 1;
        return "ZL-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getTotalItems(): int
    {
        return $this->items()->sum('ilosc');
    }

    public function getTotalProduced(): int
    {
        return $this->items()->sum('ilosc_wyprodukowana');
    }

    public function getProgressPercentage(): int
    {
        $total = $this->getTotalItems();
        if ($total === 0) return 0;

        return round(($this->getTotalProduced() / $total) * 100);
    }

    public function isOverdue(): bool
    {
        return $this->data_produkcji < now()->toDateString() &&
               in_array($this->status, ['oczekujace', 'w_produkcji']);
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'oczekujace';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'w_produkcji';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['oczekujace', 'w_produkcji']);
    }

    public function startProduction(): void
    {
        if ($this->canBeStarted()) {
            $this->update([
                'status' => 'w_produkcji',
                'data_rozpoczecia' => now(),
            ]);
        }
    }

    public function completeProduction(): void
    {
        if ($this->canBeCompleted()) {
            $this->update([
                'status' => 'zakonczone',
                'data_zakonczenia' => now(),
            ]);
        }
    }

    public function cancelOrder(): void
    {
        if ($this->canBeCancelled()) {
            $this->update([
                'status' => 'anulowane',
            ]);
        }
    }

    public function duplicate(array $overrides = []): static
    {
        $newOrder = $this->replicate();
        $newOrder->numer_zlecenia = $this->generateOrderNumber();
        $newOrder->status = 'oczekujace';
        $newOrder->data_rozpoczecia = null;
        $newOrder->data_zakonczenia = null;

        // Zastosuj nadpisania
        foreach ($overrides as $key => $value) {
            $newOrder->$key = $value;
        }

        $newOrder->save();

        // Duplikuj pozycje
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->production_order_id = $newOrder->id;
            $newItem->status = 'oczekujace';
            $newItem->ilosc_wyprodukowana = 0;
            $newItem->save();
        }

        return $newOrder;
    }

    // Boot method dla automatycznego generowania numeru zlecenia
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->numer_zlecenia)) {
                $order->numer_zlecenia = $order->generateOrderNumber();
            }
        });
    }
}
