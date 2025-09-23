<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Product;

class AssignRecipesToProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Znajdź produkty i przepisy
        $bulkaKajzerka = Product::where('nazwa', 'LIKE', '%Bułka kajzerka%')->first();
        $chlebZytni = Product::where('nazwa', 'LIKE', '%Chleb żytni%')->first();
        $sernikKlasyczny = Product::where('nazwa', 'LIKE', '%Sernik%')->first();

        // Przypisz przepisy do produktów
        $recipeBulki = Recipe::where('kod', 'REC-BULKA-001')->first();
        if ($recipeBulki && $bulkaKajzerka) {
            $recipeBulki->update(['product_id' => $bulkaKajzerka->id]);
            echo "Przypisano przepis 'Bułki pszenne klasyczne' do produktu '{$bulkaKajzerka->nazwa}'\n";
        }

        $recipeChleb = Recipe::where('kod', 'REC-CHLEB-001')->first();
        if ($recipeChleb && $chlebZytni) {
            $recipeChleb->update(['product_id' => $chlebZytni->id]);
            echo "Przypisano przepis 'Chleb żytni na żurek' do produktu '{$chlebZytni->nazwa}'\n";
        }

        $recipeRogal = Recipe::where('kod', 'REC-ROGAL-001')->first();
        if ($recipeRogal && $sernikKlasyczny) {
            $recipeRogal->update(['product_id' => $sernikKlasyczny->id]);
            echo "Przypisano przepis 'Ciasto drożdżowe na rogale' do produktu '{$sernikKlasyczny->nazwa}'\n";
        }

        // Wyświetl wszystkie produkty i przepisy
        echo "\n=== WSZYSTKIE PRODUKTY ===\n";
        foreach (Product::all() as $product) {
            echo "{$product->id}: {$product->nazwa}\n";
        }

        echo "\n=== WSZYSTKIE PRZEPISY ===\n";
        foreach (Recipe::with('product')->get() as $recipe) {
            $productName = $recipe->product ? $recipe->product->nazwa : 'BRAK PRODUKTU';
            echo "{$recipe->id}: {$recipe->nazwa} -> {$productName}\n";
        }
    }
}

