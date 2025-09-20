<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Recipe extends Model
{
    protected $fillable = [
        'kod',
        'nazwa',
        'opis',
        'product_id',
        'ilosc_porcji',
        'waga_jednostkowa_g',
        'czas_przygotowania_min',
        'czas_wypiekania_min',
        'czas_calkowity_min',
        'temperatura_c',
        'instrukcje_wypiekania',
        'poziom_trudnosci',
        'kategoria',
        'uwagi',
        'wskazowki',
        'aktywny',
        'testowany',
        'autor',
        'wersja',
    ];

    protected $casts = [
        'ilosc_porcji' => 'integer',
        'waga_jednostkowa_g' => 'decimal:2',
        'czas_przygotowania_min' => 'integer',
        'czas_wypiekania_min' => 'integer',
        'czas_calkowity_min' => 'integer',
        'temperatura_c' => 'integer',
        'aktywny' => 'boolean',
        'testowany' => 'boolean',
    ];

    // Relacje
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('kolejnosc');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'recipe_materials')
            ->withPivot(['ilosc', 'jednostka', 'uwagi', 'kolejnosc', 'opcjonalny', 'sposob_przygotowania', 'temperatura_c'])
            ->withTimestamps()
            ->orderBy('recipe_materials.kolejnosc');
    }

    // Akcesory
    protected function wagaJednostkowaKg(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->waga_jednostkowa_g ? $this->waga_jednostkowa_g / 1000 : 0,
        );
    }

    protected function czasCalkowityFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatTime($this->czas_calkowity_min),
        );
    }

    protected function czasPrzygotowaniaFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatTime($this->czas_przygotowania_min),
        );
    }

    protected function czasWypiekaniaFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatTime($this->czas_wypiekania_min),
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('aktywny', true);
    }

    public function scopeTested($query)
    {
        return $query->where('testowany', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('kategoria', $category);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('poziom_trudnosci', $difficulty);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nazwa', 'like', "%{$search}%")
              ->orWhere('kod', 'like', "%{$search}%")
              ->orWhere('kategoria', 'like', "%{$search}%")
              ->orWhere('autor', 'like', "%{$search}%");
        });
    }

    public function scopeByTimeRange($query, $minTime, $maxTime)
    {
        return $query->whereBetween('czas_calkowity_min', [$minTime, $maxTime]);
    }

    // Metody pomocnicze
    private function formatTime(?int $minutes): string
    {
        if (!$minutes) return '0 min';

        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = intval($minutes / 60);
        $mins = $minutes % 60;

        if ($mins === 0) {
            return $hours . ' godz';
        }

        return $hours . ' godz ' . $mins . ' min';
    }

    public function getDifficultyColor(): string
    {
        return match($this->poziom_trudnosci) {
            'łatwy' => 'green',
            'średni' => 'yellow',
            'trudny' => 'red',
            default => 'gray',
        };
    }

    public function getStatusColor(): string
    {
        if (!$this->aktywny) return 'gray';
        if ($this->testowany) return 'green';
        return 'blue';
    }

    public function getStatusLabel(): string
    {
        if (!$this->aktywny) return 'Nieaktywna';
        if ($this->testowany) return 'Przetestowana';
        return 'W opracowaniu';
    }

    public function calculateTotalCost(): float
    {
        $totalCost = 0;

        foreach ($this->materials as $material) {
            if ($material->cena_zakupu_gr && $material->pivot->ilosc) {
                // Przelicz ilość na jednostkę podstawową materiału
                $quantity = $this->convertToBaseUnit(
                    $material->pivot->ilosc,
                    $material->pivot->jednostka,
                    $material->jednostka_podstawowa
                );

                $cost = ($material->cena_zakupu_gr / 100) * $quantity;
                $totalCost += $cost;
            }
        }

        return $totalCost;
    }

    public function calculateCostPerPortion(): float
    {
        if ($this->ilosc_porcji <= 0) return 0;
        return $this->calculateTotalCost() / $this->ilosc_porcji;
    }

    private function convertToBaseUnit(float $amount, string $fromUnit, string $toUnit): float
    {
        // Prosta konwersja jednostek - można rozszerzyć
        if ($fromUnit === $toUnit) return $amount;

        // kg -> g
        if ($fromUnit === 'kg' && $toUnit === 'g') return $amount * 1000;
        if ($fromUnit === 'g' && $toUnit === 'kg') return $amount / 1000;

        // l -> ml
        if ($fromUnit === 'l' && $toUnit === 'ml') return $amount * 1000;
        if ($fromUnit === 'ml' && $toUnit === 'l') return $amount / 1000;

        return $amount; // Jeśli nie można skonwertować, zwróć oryginalną wartość
    }

    public function addMaterial(Material $material, float $amount, string $unit, array $options = []): void
    {
        $this->materials()->attach($material->id, array_merge([
            'ilosc' => $amount,
            'jednostka' => $unit,
            'kolejnosc' => $options['kolejnosc'] ?? 0,
            'opcjonalny' => $options['opcjonalny'] ?? false,
            'uwagi' => $options['uwagi'] ?? null,
            'sposob_przygotowania' => $options['sposob_przygotowania'] ?? null,
            'temperatura_c' => $options['temperatura_c'] ?? null,
        ], $options));
    }

    public function removeMaterial(Material $material): void
    {
        $this->materials()->detach($material->id);
    }

    public function addStep(array $stepData): RecipeStep
    {
        return $this->steps()->create($stepData);
    }

    // Statyczne metody
    public static function getAvailableCategories(): array
    {
        return [
            'chleby' => 'Chleby',
            'bułki' => 'Bułki',
            'rogale' => 'Rogale',
            'ciasta_drożdżowe' => 'Ciasta drożdżowe',
            'ciasta_biszkoptowe' => 'Ciasta biszkoptowe',
            'ciasta_kruche' => 'Ciasta kruche',
            'torty' => 'Torty',
            'desery' => 'Desery',
            'ciasteczka' => 'Ciasteczka',
            'pierniki' => 'Pierniki',
            'makarony' => 'Makarony',
            'pizza' => 'Pizza',
            'inne' => 'Inne',
        ];
    }

    public static function getAvailableDifficulties(): array
    {
        return [
            'łatwy' => 'Łatwy',
            'średni' => 'Średni',
            'trudny' => 'Trudny',
        ];
    }
}
