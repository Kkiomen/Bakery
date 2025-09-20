<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RecipeStep extends Model
{
    protected $fillable = [
        'recipe_id',
        'kolejnosc',
        'typ',
        'nazwa',
        'opis',
        'czas_min',
        'temperatura_c',
        'wilgotnosc_proc',
        'narzedzia',
        'wskazowki',
        'uwagi',
        'kryteria_oceny',
        'czeste_bledy',
        'obowiazkowy',
        'automatyczny',
    ];

    protected $casts = [
        'kolejnosc' => 'integer',
        'czas_min' => 'integer',
        'temperatura_c' => 'integer',
        'wilgotnosc_proc' => 'integer',
        'obowiazkowy' => 'boolean',
        'automatyczny' => 'boolean',
    ];

    // Relacje
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'recipe_step_materials')
                    ->withPivot([
                        'ilosc', 'jednostka', 'uwagi', 'kolejnosc',
                        'opcjonalny', 'sposob_przygotowania', 'temperatura_c',
                        'zamienniki', 'ma_zamienniki'
                    ])
                    ->withTimestamps()
                    ->orderBy('recipe_step_materials.kolejnosc');
    }

    // Akcesory
    protected function czasFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatTime($this->czas_min),
        );
    }

    protected function temperaturaFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->temperatura_c ? $this->temperatura_c . '°C' : null,
        );
    }

    protected function wilgotnoscFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->wilgotnosc_proc ? $this->wilgotnosc_proc . '%' : null,
        );
    }

    // Metody pomocnicze
    private function formatTime(?int $minutes): string
    {
        if (!$minutes) return '';

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

    public function getTypeColor(): string
    {
        return match($this->typ) {
            'przygotowanie' => 'blue',
            'mieszanie' => 'green',
            'wyrabianie' => 'yellow',
            'wyrastanie' => 'purple',
            'formowanie' => 'indigo',
            'odpoczynek' => 'pink',
            'wypiekanie' => 'red',
            'chłodzenie' => 'cyan',
            'dekorowanie' => 'orange',
            'pakowanie' => 'gray',
            default => 'gray',
        };
    }

    public function getTypeIcon(): string
    {
        return match($this->typ) {
            'przygotowanie' => '📋',
            'mieszanie' => '🥄',
            'wyrabianie' => '👐',
            'wyrastanie' => '⏰',
            'formowanie' => '🤲',
            'odpoczynek' => '😴',
            'wypiekanie' => '🔥',
            'chłodzenie' => '❄️',
            'dekorowanie' => '🎨',
            'pakowanie' => '📦',
            default => '⚙️',
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->typ) {
            'przygotowanie' => 'Przygotowanie składników',
            'mieszanie' => 'Mieszanie składników',
            'wyrabianie' => 'Wyrabianie ciasta',
            'wyrastanie' => 'Wyrastanie',
            'formowanie' => 'Formowanie',
            'odpoczynek' => 'Odpoczynek',
            'wypiekanie' => 'Wypiekanie',
            'chłodzenie' => 'Chłodzenie',
            'dekorowanie' => 'Dekorowanie',
            'pakowanie' => 'Pakowanie',
            default => ucfirst($this->typ),
        };
    }

    public function isTimeDependent(): bool
    {
        return in_array($this->typ, ['wyrastanie', 'odpoczynek', 'wypiekanie', 'chłodzenie']);
    }

    public function isTemperatureDependent(): bool
    {
        return in_array($this->typ, ['wyrastanie', 'wypiekanie', 'chłodzenie']);
    }

    public function isHumidityDependent(): bool
    {
        return in_array($this->typ, ['wyrastanie']);
    }

    public function getRequiredParameters(): array
    {
        $parameters = [];

        if ($this->isTimeDependent()) {
            $parameters[] = 'czas';
        }

        if ($this->isTemperatureDependent()) {
            $parameters[] = 'temperatura';
        }

        if ($this->isHumidityDependent()) {
            $parameters[] = 'wilgotność';
        }

        return $parameters;
    }

    // Statyczne metody
    public static function getAvailableTypes(): array
    {
        return [
            'przygotowanie' => 'Przygotowanie składników',
            'mieszanie' => 'Mieszanie składników',
            'wyrabianie' => 'Wyrabianie ciasta',
            'wyrastanie' => 'Wyrastanie',
            'formowanie' => 'Formowanie',
            'odpoczynek' => 'Odpoczynek po formowaniu',
            'wypiekanie' => 'Wypiekanie',
            'chłodzenie' => 'Chłodzenie',
            'dekorowanie' => 'Dekorowanie/glazurowanie',
            'pakowanie' => 'Pakowanie',
        ];
    }

    public static function getTypesRequiringTime(): array
    {
        return ['wyrastanie', 'odpoczynek', 'wypiekanie', 'chłodzenie'];
    }

    public static function getTypesRequiringTemperature(): array
    {
        return ['wyrastanie', 'wypiekanie', 'chłodzenie'];
    }

    public static function getTypesRequiringHumidity(): array
    {
        return ['wyrastanie'];
    }

    // Metody pomocnicze dla składników
    public function addMaterial(Material $material, float $amount, string $unit, array $options = []): void
    {
        $this->materials()->attach($material->id, array_merge([
            'ilosc' => $amount,
            'jednostka' => $unit,
            'kolejnosc' => $this->materials()->count(),
        ], $options));
    }

    public function removeMaterial(Material $material): void
    {
        $this->materials()->detach($material->id);
    }

    public function hasMaterials(): bool
    {
        return $this->materials()->count() > 0;
    }

    public function getTotalMaterialsCost(): float
    {
        $total = 0;

        foreach ($this->materials as $material) {
            if ($material->cena_zakupu_gr) {
                // Konwersja jednostek do podstawowej jednostki materiału
                $baseAmount = $this->convertToBaseUnit(
                    $material->pivot->ilosc,
                    $material->pivot->jednostka,
                    $material->jednostka_podstawowa
                );

                $total += ($material->cena_zakupu_gr / 100) * $baseAmount;
            }
        }

        return $total;
    }

    private function convertToBaseUnit(float $amount, string $fromUnit, string $toUnit): float
    {
        // Konwersje jednostek
        $conversions = [
            'g' => ['kg' => 0.001],
            'kg' => ['g' => 1000],
            'ml' => ['l' => 0.001],
            'l' => ['ml' => 1000],
        ];

        if ($fromUnit === $toUnit) {
            return $amount;
        }

        if (isset($conversions[$fromUnit][$toUnit])) {
            return $amount * $conversions[$fromUnit][$toUnit];
        }

        return $amount; // Jeśli nie ma konwersji, zwróć oryginalną wartość
    }

    // Metody pomocnicze dla zamienników
    public function addSubstituteToMaterial(Material $material, Material $substitute, float $conversionFactor = 1.0, ?string $notes = null): void
    {
        $pivot = $this->materials()->where('material_id', $material->id)->first();
        if (!$pivot) {
            return;
        }

        $currentSubstitutes = json_decode($pivot->pivot->zamienniki ?? '[]', true);

        // Sprawdź czy zamiennik już nie istnieje
        $existingIndex = collect($currentSubstitutes)->search(function ($sub) use ($substitute) {
            return $sub['material_id'] == $substitute->id;
        });

        $substituteData = [
            'material_id' => $substitute->id,
            'material_name' => $substitute->nazwa,
            'wspolczynnik_przeliczenia' => $conversionFactor,
            'uwagi' => $notes,
            'jednostka' => $substitute->jednostka_podstawowa,
        ];

        if ($existingIndex !== false) {
            $currentSubstitutes[$existingIndex] = $substituteData;
        } else {
            $currentSubstitutes[] = $substituteData;
        }

        $this->materials()->updateExistingPivot($material->id, [
            'zamienniki' => json_encode($currentSubstitutes),
            'ma_zamienniki' => count($currentSubstitutes) > 0,
        ]);
    }

    public function removeSubstituteFromMaterial(Material $material, Material $substitute): void
    {
        $pivot = $this->materials()->where('material_id', $material->id)->first();
        if (!$pivot) {
            return;
        }

        $currentSubstitutes = json_decode($pivot->pivot->zamienniki ?? '[]', true);
        $currentSubstitutes = collect($currentSubstitutes)->reject(function ($sub) use ($substitute) {
            return $sub['material_id'] == $substitute->id;
        })->values()->toArray();

        $this->materials()->updateExistingPivot($material->id, [
            'zamienniki' => json_encode($currentSubstitutes),
            'ma_zamienniki' => count($currentSubstitutes) > 0,
        ]);
    }

    public function getSubstitutesForMaterial(Material $material): array
    {
        $pivot = $this->materials()->where('material_id', $material->id)->first();
        if (!$pivot) {
            return [];
        }

        return json_decode($pivot->pivot->zamienniki ?? '[]', true);
    }

    public function hasSubstitutes(Material $material): bool
    {
        $pivot = $this->materials()->where('material_id', $material->id)->first();
        return $pivot ? (bool) $pivot->pivot->ma_zamienniki : false;
    }

    // Pobierz wszystkie dostępne zamienniki na podstawie typu materiału
    public static function getAvailableSubstitutes(Material $material): array
    {
        $substitutes = [];

        // Logika zamienników na podstawie typu materiału
        switch ($material->typ) {
            case 'mąka':
                $substitutes = [
                    ['typ' => 'mąka', 'wspolczynnik' => 1.0, 'uwagi' => 'Zamiana 1:1'],
                ];
                break;
            case 'nabiał':
                if (str_contains(strtolower($material->nazwa), 'mleko')) {
                    $substitutes = [
                        ['typ' => 'nabiał', 'nazwa_zawiera' => 'mleko', 'wspolczynnik' => 1.0, 'uwagi' => 'Inne rodzaje mleka'],
                        ['typ' => 'nabiał', 'nazwa_zawiera' => 'śmietana', 'wspolczynnik' => 0.8, 'uwagi' => 'Śmietana + woda'],
                    ];
                }
                break;
            case 'drożdże':
                $substitutes = [
                    ['typ' => 'drożdże', 'wspolczynnik_swieze_suche' => 0.3, 'uwagi' => 'Drożdże świeże → suche (1:0.3)'],
                    ['typ' => 'drożdże', 'wspolczynnik_suche_swieze' => 3.0, 'uwagi' => 'Drożdże suche → świeże (1:3)'],
                ];
                break;
            case 'tłuszcze':
                $substitutes = [
                    ['typ' => 'tłuszcze', 'wspolczynnik' => 1.0, 'uwagi' => 'Inne tłuszcze'],
                ];
                break;
            case 'cukier':
                $substitutes = [
                    ['typ' => 'cukier', 'wspolczynnik' => 1.0, 'uwagi' => 'Inne rodzaje cukru'],
                ];
                break;
        }

        return $substitutes;
    }
}
