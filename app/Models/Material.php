<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Material extends Model
{
    protected $fillable = [
        'kod',
        'nazwa',
        'opis',
        'typ',
        'jednostka_podstawowa',
        'waga_opakowania',
        'dostawca',
        'stan_aktualny',
        'stan_minimalny',
        'stan_optymalny',
        'cena_zakupu_gr',
        'stawka_vat',
        'dni_waznosci',
        'data_ostatniej_dostawy',
        'uwagi',
        'aktywny',
    ];

    protected $casts = [
        'stan_aktualny' => 'decimal:3',
        'stan_minimalny' => 'decimal:3',
        'stan_optymalny' => 'decimal:3',
        'waga_opakowania' => 'decimal:3',
        'cena_zakupu_gr' => 'integer',
        'dni_waznosci' => 'integer',
        'data_ostatniej_dostawy' => 'date',
        'aktywny' => 'boolean',
    ];

    // Relacje
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_materials')
            ->withPivot(['ilosc', 'jednostka', 'uwagi', 'kolejnosc', 'opcjonalny', 'sposob_przygotowania', 'temperatura_c'])
            ->withTimestamps();
    }

    public function recipeSteps(): BelongsToMany
    {
        return $this->belongsToMany(RecipeStep::class, 'recipe_step_materials')
            ->withPivot([
                'ilosc', 'jednostka', 'uwagi', 'kolejnosc',
                'opcjonalny', 'sposob_przygotowania', 'temperatura_c',
                'zamienniki', 'ma_zamienniki'
            ])
            ->withTimestamps();
    }

    // Akcesory
    protected function cenaZakupu(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cena_zakupu_gr ? number_format($this->cena_zakupu_gr / 100, 2, ',', '') : '0,00',
        );
    }

    protected function cenaZakupuZl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cena_zakupu_gr ? $this->cena_zakupu_gr / 100 : 0,
        );
    }

    protected function wartoscMagazynu(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stan_aktualny * $this->cena_zakupu_zl,
        );
    }

    protected function wartoscMagazynuFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->wartosc_magazynu, 2, ',', '') . ' zł',
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

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stan_aktualny', '<=', 'stan_minimalny');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stan_aktualny', '<=', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nazwa', 'like', "%{$search}%")
              ->orWhere('kod', 'like', "%{$search}%")
              ->orWhere('dostawca', 'like', "%{$search}%");
        });
    }

    // Metody pomocnicze
    public function isLowStock(): bool
    {
        return $this->stan_aktualny <= $this->stan_minimalny;
    }

    public function isOutOfStock(): bool
    {
        return $this->stan_aktualny <= 0;
    }

    public function getStockStatus(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        }

        if ($this->isLowStock()) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function getStockStatusLabel(): string
    {
        return match($this->getStockStatus()) {
            'out_of_stock' => 'Brak w magazynie',
            'low_stock' => 'Niski stan',
            'in_stock' => 'W magazynie',
        };
    }

    public function getStockStatusColor(): string
    {
        return match($this->getStockStatus()) {
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'in_stock' => 'green',
        };
    }

    public function addStock(float $quantity, ?string $note = null): void
    {
        $this->increment('stan_aktualny', $quantity);
        $this->update(['data_ostatniej_dostawy' => now()]);

        // Tutaj można dodać log ruchu magazynowego
    }

    public function removeStock(float $quantity, ?string $note = null): bool
    {
        if ($this->stan_aktualny >= $quantity) {
            $this->decrement('stan_aktualny', $quantity);
            return true;
        }

        return false; // Niewystarczający stan
    }

    // Statyczne metody
    public static function getAvailableTypes(): array
    {
        return [
            'mąka' => 'Mąka',
            'cukier' => 'Cukier',
            'drożdże' => 'Drożdże',
            'tłuszcze' => 'Tłuszcze',
            'nabiał' => 'Nabiał',
            'jajka' => 'Jajka',
            'dodatki' => 'Dodatki',
            'przyprawy' => 'Przyprawy',
            'owoce' => 'Owoce',
            'orzechy' => 'Orzechy',
            'dekoracje' => 'Dekoracje',
            'opakowania' => 'Opakowania',
        ];
    }

    public static function getAvailableUnits(): array
    {
        return [
            'kg' => 'Kilogram',
            'g' => 'Gram',
            'l' => 'Litr',
            'ml' => 'Mililitr',
            'szt' => 'Sztuka',
            'opak' => 'Opakowanie',
        ];
    }

    /**
     * Zwraca wszystkie receptury, które używają tego materiału
     * (bezpośrednio, w krokach lub jako zamiennik)
     */
    public function getAllRelatedRecipes()
    {
        $recipeIds = collect();

        // 1. Receptury używające materiału bezpośrednio
        $directRecipeIds = $this->recipes()->pluck('recipes.id');
        $recipeIds = $recipeIds->merge($directRecipeIds);

        // 2. Receptury używające materiału w krokach
        $stepRecipeIds = $this->recipeSteps()
            ->with('recipe')
            ->get()
            ->pluck('recipe.id')
            ->unique();
        $recipeIds = $recipeIds->merge($stepRecipeIds);

        // 3. Receptury gdzie materiał jest zamiennikiem
        $substitutesRecipeIds = DB::table('recipe_step_materials')
            ->whereNotNull('zamienniki')
            ->where('zamienniki', '!=', '[]')
            ->where('zamienniki', 'LIKE', '%"material_id":' . $this->id . '%')
            ->join('recipe_steps', 'recipe_step_materials.recipe_step_id', '=', 'recipe_steps.id')
            ->pluck('recipe_steps.recipe_id');
        $recipeIds = $recipeIds->merge($substitutesRecipeIds);

        // Zwróć unikalne receptury
        return Recipe::whereIn('id', $recipeIds->unique())->get();
    }

    /**
     * Zwraca liczbę receptur używających tego materiału
     */
    public function getRecipeCount(): int
    {
        return $this->getAllRelatedRecipes()->count();
    }
}
