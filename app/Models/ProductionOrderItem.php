<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductionOrderItem extends Model
{
    protected $fillable = [
        'production_order_id',
        'product_id',
        'ilosc',
        'jednostka',
        'ilosc_wyprodukowana',
        'status',
        'current_step',
        'step_started_at',
        'step_notes',
        'uwagi',
    ];

    protected $casts = [
        'ilosc' => 'integer',
        'ilosc_wyprodukowana' => 'integer',
        'step_started_at' => 'datetime',
        'step_notes' => 'array',
    ];

    // Relacje
    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Akcesory
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'oczekujace' => 'Oczekujące',
                'w_produkcji' => 'W produkcji',
                'zakonczone' => 'Zakończone',
                default => $this->status
            }
        );
    }

    protected function currentStepLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->current_step) {
                'waiting' => 'Oczekuje',
                'preparing' => 'Przygotowanie składników',
                'mixing' => 'Mieszanie/Zagniatanie',
                'first_rise' => 'Pierwszy wyrośnięcie',
                'shaping' => 'Formowanie',
                'second_rise' => 'Drugi wyrośnięcie',
                'baking' => 'Pieczenie',
                'cooling' => 'Studzenie',
                'packaging' => 'Pakowanie',
                'completed' => 'Ukończone',
                default => $this->current_step
            }
        );
    }

    protected function stepColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->current_step) {
                'waiting' => 'gray',
                'preparing' => 'blue',
                'mixing' => 'indigo',
                'first_rise' => 'purple',
                'shaping' => 'pink',
                'second_rise' => 'purple',
                'baking' => 'orange',
                'cooling' => 'cyan',
                'packaging' => 'green',
                'completed' => 'emerald',
                default => 'gray'
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
                default => 'gray'
            }
        );
    }

    protected function remainingQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->ilosc - $this->ilosc_wyprodukowana)
        );
    }

    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc > 0 ? round(($this->ilosc_wyprodukowana / $this->ilosc) * 100) : 0
        );
    }

    protected function isCompleted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc_wyprodukowana >= $this->ilosc
        );
    }

    protected function formattedQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc . ' ' . $this->jednostka
        );
    }

    protected function formattedProducedQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ilosc_wyprodukowana . ' ' . $this->jednostka
        );
    }

    protected function formattedRemainingQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->remaining_quantity . ' ' . $this->jednostka
        );
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeCompleted($query)
    {
        return $query->whereRaw('ilosc_wyprodukowana >= ilosc');
    }

    public function scopeIncomplete($query)
    {
        return $query->whereRaw('ilosc_wyprodukowana < ilosc');
    }

    // Metody pomocnicze
    public function canBeStarted(): bool
    {
        return $this->status === 'oczekujace' &&
               $this->productionOrder->status !== 'anulowane';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'w_produkcji' &&
               $this->ilosc_wyprodukowana < $this->ilosc;
    }

    public function startProduction(): void
    {
        if ($this->canBeStarted()) {
            $this->update(['status' => 'w_produkcji']);

            // Jeśli to pierwsza pozycja w zleceniu, rozpocznij całe zlecenie
            if ($this->productionOrder->status === 'oczekujace') {
                $this->productionOrder->startProduction();
            }
        }
    }

    public function updateProducedQuantity(int $quantity): void
    {
        $this->update([
            'ilosc_wyprodukowana' => min($quantity, $this->ilosc),
            'status' => $quantity >= $this->ilosc ? 'zakonczone' : 'w_produkcji'
        ]);

        // Sprawdź czy wszystkie pozycje są zakończone
        $this->checkOrderCompletion();
    }

    public function addProducedQuantity(int $quantity): void
    {
        $newQuantity = min($this->ilosc_wyprodukowana + $quantity, $this->ilosc);
        $this->updateProducedQuantity($newQuantity);
    }

    public function completeItem(): void
    {
        $this->updateProducedQuantity($this->ilosc);
    }

    public function moveToNextStep(): void
    {
        $steps = [
            'waiting' => 'preparing',
            'preparing' => 'mixing',
            'mixing' => 'first_rise',
            'first_rise' => 'shaping',
            'shaping' => 'second_rise',
            'second_rise' => 'baking',
            'baking' => 'cooling',
            'cooling' => 'packaging',
            'packaging' => 'completed',
        ];

        $nextStep = $steps[$this->current_step] ?? 'completed';

        $this->update([
            'current_step' => $nextStep,
            'step_started_at' => now(),
            'status' => $nextStep === 'completed' ? 'zakonczone' : 'w_produkcji'
        ]);

        // Jeśli to pierwsza pozycja w zleceniu, rozpocznij całe zlecenie
        if ($this->current_step === 'preparing' && $this->productionOrder->status === 'oczekujace') {
            $this->productionOrder->startProduction();
        }

        // Sprawdź czy wszystkie pozycje są zakończone
        $this->checkOrderCompletion();
    }

    public function moveToPreviousStep(): void
    {
        $steps = [
            'preparing' => 'waiting',
            'mixing' => 'preparing',
            'first_rise' => 'mixing',
            'shaping' => 'first_rise',
            'second_rise' => 'shaping',
            'baking' => 'second_rise',
            'cooling' => 'baking',
            'packaging' => 'cooling',
            'completed' => 'packaging',
        ];

        $previousStep = $steps[$this->current_step] ?? 'waiting';

        $this->update([
            'current_step' => $previousStep,
            'step_started_at' => now(),
            'status' => $previousStep === 'waiting' ? 'oczekujace' : 'w_produkcji'
        ]);

        // Sprawdź czy wszystkie pozycje są zakończone
        $this->checkOrderCompletion();
    }

    public function moveToStep(string $step): void
    {
        $validSteps = [
            'waiting', 'preparing', 'mixing', 'first_rise', 'shaping',
            'second_rise', 'baking', 'cooling', 'packaging', 'completed'
        ];

        if (!in_array($step, $validSteps)) {
            return;
        }

        $this->update([
            'current_step' => $step,
            'step_started_at' => now(),
            'status' => $step === 'completed' ? 'zakonczone' : ($step === 'waiting' ? 'oczekujace' : 'w_produkcji')
        ]);

        $this->checkOrderCompletion();
    }

    public function getAvailableSteps(): array
    {
        return [
            'waiting' => 'Oczekuje',
            'preparing' => 'Przygotowanie składników',
            'mixing' => 'Mieszanie/Zagniatanie',
            'first_rise' => 'Pierwszy wyrośnięcie',
            'shaping' => 'Formowanie',
            'second_rise' => 'Drugi wyrośnięcie',
            'baking' => 'Pieczenie',
            'cooling' => 'Studzenie',
            'packaging' => 'Pakowanie',
            'completed' => 'Ukończone',
        ];
    }

    public function getStepDuration(): ?int
    {
        if (!$this->step_started_at) {
            return null;
        }

        return $this->step_started_at->diffInMinutes(now());
    }

    private function checkOrderCompletion(): void
    {
        $order = $this->productionOrder;

        // Sprawdź czy wszystkie pozycje są zakończone
        $allCompleted = $order->items()->incomplete()->count() === 0;

        if ($allCompleted && $order->status === 'w_produkcji') {
            $order->completeProduction();
        }
    }
}
