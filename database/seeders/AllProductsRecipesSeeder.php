<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Product;
use App\Models\Material;

class AllProductsRecipesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pobierz wszystkie produkty
        $products = Product::all();

        echo "Tworzenie przepisów dla wszystkich produktów...\n";

        foreach ($products as $product) {
            // Sprawdź czy produkt już ma przepis
            if ($product->recipes()->count() > 0) {
                echo "Produkt '{$product->nazwa}' już ma przepis - pomijam\n";
                continue;
            }

            // Stwórz przepis na podstawie nazwy produktu
            $this->createRecipeForProduct($product);
        }

        echo "\nUkończono tworzenie przepisów!\n";
        $this->showSummary();
    }

    private function createRecipeForProduct(Product $product)
    {
        $recipeName = "Przepis na " . $product->nazwa;
        $recipeCode = 'REC-' . strtoupper(substr(str_replace([' ', 'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż'], ['', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z'], $product->nazwa), 0, 8)) . '-' . str_pad($product->id, 3, '0', STR_PAD_LEFT);

        // Określ parametry na podstawie typu produktu
        $recipeData = $this->getRecipeDataByProductType($product);

        $recipe = Recipe::create([
            'kod' => $recipeCode,
            'nazwa' => $recipeName,
            'opis' => $recipeData['opis'],
            'product_id' => $product->id,
            'ilosc_porcji' => $recipeData['ilosc_porcji'],
            'waga_jednostkowa_g' => $recipeData['waga_jednostkowa_g'],
            'czas_przygotowania_min' => $recipeData['czas_przygotowania_min'],
            'czas_wypiekania_min' => $recipeData['czas_wypiekania_min'],
            'czas_calkowity_min' => $recipeData['czas_calkowity_min'],
            'temperatura_c' => $recipeData['temperatura_c'],
            'instrukcje_wypiekania' => $recipeData['instrukcje_wypiekania'],
            'poziom_trudnosci' => $recipeData['poziom_trudnosci'],
            'kategoria' => $recipeData['kategoria'],
            'uwagi' => 'Przepis wygenerowany automatycznie',
            'wskazowki' => $recipeData['wskazowki'],
            'aktywny' => true,
            'testowany' => false,
            'autor' => 'System',
            'wersja' => '1.0',
        ]);

        // Dodaj kroki do przepisu
        $this->addStepsToRecipe($recipe, $recipeData);

        echo "Utworzono przepis '{$recipeName}' dla produktu '{$product->nazwa}'\n";
    }

    private function getRecipeDataByProductType(Product $product): array
    {
        $productName = strtolower($product->nazwa);

        // Chleby
        if (str_contains($productName, 'chleb')) {
            return [
                'opis' => 'Tradycyjny przepis na chleb',
                'ilosc_porcji' => 2,
                'waga_jednostkowa_g' => 800.0,
                'czas_przygotowania_min' => 45,
                'czas_wypiekania_min' => 50,
                'czas_calkowity_min' => 480,
                'temperatura_c' => 200,
                'instrukcje_wypiekania' => 'Piec z parą przez pierwsze 15 minut',
                'poziom_trudnosci' => 'średni',
                'kategoria' => 'chleby',
                'wskazowki' => 'Ważne jest odpowiednie wyrastanie ciasta'
            ];
        }

        // Bułki
        if (str_contains($productName, 'bułka')) {
            return [
                'opis' => 'Przepis na świeże bułki',
                'ilosc_porcji' => 20,
                'waga_jednostkowa_g' => 60.0,
                'czas_przygotowania_min' => 30,
                'czas_wypiekania_min' => 15,
                'czas_calkowity_min' => 180,
                'temperatura_c' => 220,
                'instrukcje_wypiekania' => 'Piec z parą przez pierwsze 5 minut',
                'poziom_trudnosci' => 'łatwy',
                'kategoria' => 'bułki',
                'wskazowki' => 'Smarować jajkiem przed pieczeniem'
            ];
        }

        // Serniki
        if (str_contains($productName, 'sernik')) {
            return [
                'opis' => 'Klasyczny sernik na zimno lub pieczony',
                'ilosc_porcji' => 12,
                'waga_jednostkowa_g' => 150.0,
                'czas_przygotowania_min' => 60,
                'czas_wypiekania_min' => 45,
                'czas_calkowity_min' => 300,
                'temperatura_c' => 160,
                'instrukcje_wypiekania' => 'Piec w kąpieli wodnej',
                'poziom_trudnosci' => 'średni',
                'kategoria' => 'ciasta',
                'wskazowki' => 'Twaróg powinien być dobrze odcedzony'
            ];
        }

        // Ciastka
        if (str_contains($productName, 'ciastka') || str_contains($productName, 'ciasteczka')) {
            return [
                'opis' => 'Przepis na domowe ciastka',
                'ilosc_porcji' => 30,
                'waga_jednostkowa_g' => 25.0,
                'czas_przygotowania_min' => 20,
                'czas_wypiekania_min' => 12,
                'czas_calkowity_min' => 60,
                'temperatura_c' => 180,
                'instrukcje_wypiekania' => 'Piec do złotego koloru',
                'poziom_trudnosci' => 'łatwy',
                'kategoria' => 'ciasteczka',
                'wskazowki' => 'Nie przepiekać - mają być miękkie'
            ];
        }

        // Domyślne wartości dla innych produktów
        return [
            'opis' => 'Przepis na ' . $product->nazwa,
            'ilosc_porcji' => 10,
            'waga_jednostkowa_g' => 100.0,
            'czas_przygotowania_min' => 30,
            'czas_wypiekania_min' => 25,
            'czas_calkowity_min' => 120,
            'temperatura_c' => 180,
            'instrukcje_wypiekania' => 'Piec zgodnie z przepisem',
            'poziom_trudnosci' => 'średni',
            'kategoria' => 'inne',
            'wskazowki' => 'Sprawdzać gotowość wykałaczką'
        ];
    }

    private function addStepsToRecipe(Recipe $recipe, array $recipeData)
    {
        // Podstawowe kroki dla wszystkich przepisów
        $steps = [
            [
                'kolejnosc' => 1,
                'typ' => 'przygotowanie',
                'nazwa' => 'Przygotowanie składników',
                'opis' => 'Przygotuj i odważ wszystkie składniki zgodnie z przepisem.',
                'czas_min' => 10,
                'narzedzia' => 'waga, miski, łyżki',
                'wskazowki' => 'Wszystkie składniki powinny mieć temperaturę pokojową',
                'kryteria_oceny' => 'Składniki odważone i przygotowane',
                'obowiazkowy' => true,
                'automatyczny' => false,
            ],
            [
                'kolejnosc' => 2,
                'typ' => 'mieszanie',
                'nazwa' => 'Mieszanie składników',
                'opis' => 'Połącz składniki zgodnie z kolejnością w przepisie.',
                'czas_min' => 15,
                'narzedzia' => 'mikser, łyżka drewniana',
                'wskazowki' => 'Mieszaj dokładnie ale nie za długo',
                'kryteria_oceny' => 'Jednolita masa bez grudek',
                'obowiazkowy' => true,
                'automatyczny' => false,
            ]
        ];

        // Dodaj specyficzne kroki w zależności od typu produktu
        if ($recipeData['kategoria'] === 'chleby' || $recipeData['kategoria'] === 'bułki') {
            $steps = array_merge($steps, [
                [
                    'kolejnosc' => 3,
                    'typ' => 'wyrabianie',
                    'nazwa' => 'Wyrabianie ciasta',
                    'opis' => 'Wyrabiaj ciasto na stolnicy przez 8-10 minut.',
                    'czas_min' => 10,
                    'narzedzia' => 'stolnica, skrobaczka',
                    'wskazowki' => 'Ciasto powinno być gładkie i elastyczne',
                    'kryteria_oceny' => 'Ciasto nie klei się do rąk',
                    'obowiazkowy' => true,
                    'automatyczny' => false,
                ],
                [
                    'kolejnosc' => 4,
                    'typ' => 'wyrastanie',
                    'nazwa' => 'Pierwszy wzrost',
                    'opis' => 'Odstaw ciasto w ciepłe miejsce do wyrośnięcia.',
                    'czas_min' => 60,
                    'temperatura_c' => 28,
                    'wilgotnosc_proc' => 75,
                    'wskazowki' => 'Ciasto powinno podwoić objętość',
                    'kryteria_oceny' => 'Ciasto zwiększyło objętość',
                    'obowiazkowy' => true,
                    'automatyczny' => false,
                ],
                [
                    'kolejnosc' => 5,
                    'typ' => 'formowanie',
                    'nazwa' => 'Formowanie',
                    'opis' => 'Uformuj ciasto zgodnie z przeznaczeniem.',
                    'czas_min' => 15,
                    'narzedzia' => 'stolnica, nóż',
                    'wskazowki' => 'Formuj delikatnie',
                    'kryteria_oceny' => 'Równo uformowane',
                    'obowiazkowy' => true,
                    'automatyczny' => false,
                ]
            ]);
        }

        // Dodaj kroki pieczenia i studzenia
        $steps = array_merge($steps, [
            [
                'kolejnosc' => count($steps) + 1,
                'typ' => 'wypiekanie',
                'nazwa' => 'Pieczenie',
                'opis' => 'Piecz w rozgrzanym piekarniku.',
                'czas_min' => $recipeData['czas_wypiekania_min'],
                'temperatura_c' => $recipeData['temperatura_c'],
                'wskazowki' => $recipeData['instrukcje_wypiekania'],
                'kryteria_oceny' => 'Złoty kolor, puste brzmienie',
                'obowiazkowy' => true,
                'automatyczny' => false,
            ],
            [
                'kolejnosc' => count($steps) + 1,
                'typ' => 'chłodzenie',
                'nazwa' => 'Studzenie',
                'opis' => 'Wystudź na kratce przed pakowaniem.',
                'czas_min' => 30,
                'temperatura_c' => 25,
                'wskazowki' => 'Nie pakować gorących produktów',
                'kryteria_oceny' => 'Całkowicie ostudzone',
                'obowiazkowy' => true,
                'automatyczny' => false,
            ]
        ]);

        // Stwórz kroki w bazie danych
        foreach ($steps as $stepData) {
            $recipe->addStep($stepData);
        }

        // Dodaj podstawowe materiały
        $this->addBasicMaterialsToRecipe($recipe);
    }

    private function addBasicMaterialsToRecipe(Recipe $recipe)
    {
        // Pobierz podstawowe materiały
        $maka = Material::where('kod', 'MAK-001')->first();
        $cukier = Material::where('kod', 'CUK-001')->first();
        $sol = Material::where('kod', 'DOD-001')->first();

        if (!$recipe->steps->count()) {
            return;
        }

        $firstStep = $recipe->steps->first();

        // Dodaj podstawowe składniki do pierwszego kroku
        if ($maka && $firstStep) {
            $firstStep->addMaterial($maka, 0.5, 'kg', ['kolejnosc' => 0]);
        }

        if ($cukier && $firstStep) {
            $firstStep->addMaterial($cukier, 0.05, 'kg', ['kolejnosc' => 1]);
        }

        if ($sol && $firstStep) {
            $firstStep->addMaterial($sol, 0.01, 'kg', ['kolejnosc' => 2]);
        }
    }

    private function showSummary()
    {
        echo "\n=== PODSUMOWANIE PRZEPISÓW ===\n";

        $allProducts = Product::with('recipes')->get();

        foreach ($allProducts as $product) {
            $recipeCount = $product->recipes->count();
            $status = $recipeCount > 0 ? '✅' : '❌';
            echo "{$status} {$product->nazwa} - {$recipeCount} przepis(ów)\n";
        }

        $totalProducts = $allProducts->count();
        $productsWithRecipes = $allProducts->filter(fn($p) => $p->recipes->count() > 0)->count();

        echo "\nRazem: {$productsWithRecipes}/{$totalProducts} produktów ma przepisy\n";
    }
}
