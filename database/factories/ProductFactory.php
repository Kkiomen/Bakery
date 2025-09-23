<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'ean' => $this->faker->ean13(),
            'nazwa' => $this->faker->words(3, true),
            'opis' => $this->faker->paragraph(),
            'kategoria_id' => Category::factory(),
            'waga_g' => $this->faker->numberBetween(100, 2000),
            'jednostka_sprzedazy' => $this->faker->randomElement(['szt', 'opak', 'kg']),
            'zawartosc_opakowania' => $this->faker->numberBetween(1, 20),
            'alergeny' => $this->faker->randomElements(['gluten', 'mleko', 'jajka', 'orzechy'], 2),
            'wartosci_odzywcze' => [
                'kcal' => $this->faker->numberBetween(200, 500),
                'bialko_g' => $this->faker->numberBetween(5, 20),
                'tluszcz_g' => $this->faker->numberBetween(2, 15),
                'wegle_g' => $this->faker->numberBetween(20, 60),
            ],
            'stawka_vat' => $this->faker->randomElement(['0', '5', '8', '23']),
            'cena_netto_gr' => $this->faker->numberBetween(500, 5000),
            'aktywny' => true,
            'meta_title' => $this->faker->sentence(4),
            'meta_description' => $this->faker->sentence(10),
        ];
    }
}
