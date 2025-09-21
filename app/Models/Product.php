<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'ean',
        'nazwa',
        'opis',
        'kategoria_id',
        'waga_g',
        'jednostka_sprzedazy',
        'zawartosc_opakowania',
        'alergeny',
        'wartosci_odzywcze',
        'stawka_vat',
        'cena_netto_gr',
        'aktywny',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'alergeny' => 'array',
        'wartosci_odzywcze' => 'array',
        'aktywny' => 'boolean',
        'waga_g' => 'integer',
        'zawartosc_opakowania' => 'integer',
        'cena_netto_gr' => 'integer',
    ];

    // Relacje
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategoria_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    // Zamienniki - relacja wiele do wielu z pivot
    public function substitutes(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_substitutes',
            'product_id',
            'substitute_product_id'
        )->withPivot(['priorytet', 'uwagi'])
          ->orderByPivot('priorytet');
    }

    public function substituteFor(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_substitutes',
            'substitute_product_id',
            'product_id'
        )->withPivot(['priorytet', 'uwagi'])
          ->orderByPivot('priorytet');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    // Akcesory i mutatory
    protected function wagaKg(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->waga_g / 1000, 2, ',', '') . ' kg',
        );
    }

    protected function cenaNetto(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->cena_netto_gr / 100, 2, ',', ''),
        );
    }

    protected function cenaNettoZl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cena_netto_gr / 100,
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('aktywny', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('kategoria_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nazwa', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('ean', 'like', "%{$search}%");
        });
    }

    public function scopeWithAllergens($query, array $allergens)
    {
        return $query->whereJsonContains('alergeny', $allergens);
    }

    public function scopeWithoutAllergens($query, array $allergens)
    {
        foreach ($allergens as $allergen) {
            $query->whereJsonDoesntContain('alergeny', $allergen);
        }
        return $query;
    }

    public function scopeWeightRange($query, $minWeight, $maxWeight)
    {
        return $query->whereBetween('waga_g', [$minWeight, $maxWeight]);
    }

    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('cena_netto_gr', [$minPrice * 100, $maxPrice * 100]);
    }

    // Metody pomocnicze
    public function hasAllergen(string $allergen): bool
    {
        return in_array($allergen, $this->alergeny ?? []);
    }

    public function addSubstitute(Product $substitute, int $priority = 0, ?string $notes = null): void
    {
        // Sprawdź czy nie jest to ten sam produkt
        if ($this->id === $substitute->id) {
            throw new \InvalidArgumentException('Produkt nie może być zamiennikiem samego siebie');
        }

        // Dodaj relację w obu kierunkach (symetryczność)
        $this->substitutes()->syncWithoutDetaching([
            $substitute->id => ['priorytet' => $priority, 'uwagi' => $notes]
        ]);

        $substitute->substitutes()->syncWithoutDetaching([
            $this->id => ['priorytet' => $priority, 'uwagi' => $notes]
        ]);
    }

    public function removeSubstitute(Product $substitute): void
    {
        // Usuń relację w obu kierunkach
        $this->substitutes()->detach($substitute->id);
        $substitute->substitutes()->detach($this->id);
    }
}
