<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nazwa',
        'opis',
        'aktywny',
    ];

    protected $casts = [
        'aktywny' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'kategoria_id');
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('aktywny', true);
    }
}
