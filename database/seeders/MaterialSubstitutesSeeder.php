<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class MaterialSubstitutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Znajdź materiały
        $makaPszenna = Material::where('kod', 'MAK-001')->first();
        $makaZytnia = Material::where('kod', 'MAK-002')->first();
        $cukierBialy = Material::where('kod', 'CUK-001')->first();
        $maslo = Material::where('kod', 'TLU-001')->first();
        $mleko = Material::where('kod', 'NAB-001')->first();

        // Znajdź przepis na bułki
        $recipeBulki = Recipe::where('kod', 'REC-BULKA-001')->first();

        if ($recipeBulki && $makaPszenna && $makaZytnia) {
            // Dodaj zamienniki do mąki pszennej w przepisie na bułki
            $this->addSubstituteToRecipeMaterial($recipeBulki, $makaPszenna, [
                [
                    'material_id' => $makaZytnia->id,
                    'wspolczynnik_przeliczenia' => 0.8, // 800g mąki żytniej zamiast 1kg pszennej
                    'uwagi' => 'Bułki będą ciemniejsze i bardziej zwarte'
                ]
            ]);
        }

        // Znajdź przepis na chleb żytni
        $recipeChleb = Recipe::where('kod', 'REC-CHLEB-001')->first();

        if ($recipeChleb && $makaZytnia && $makaPszenna) {
            // Dodaj zamienniki do mąki żytniej w przepisie na chleb
            $this->addSubstituteToRecipeMaterial($recipeChleb, $makaZytnia, [
                [
                    'material_id' => $makaPszenna->id,
                    'wspolczynnik_przeliczenia' => 1.2, // 1.2kg mąki pszennej zamiast 1kg żytniej
                    'uwagi' => 'Chleb będzie jaśniejszy, dodaj więcej żurku dla smaku'
                ]
            ]);
        }

        // Znajdź przepis na rogale
        $recipeRogale = Recipe::where('kod', 'REC-ROGAL-001')->first();

        if ($recipeRogale && $cukierBialy) {
            // Znajdź miód jako zamiennik cukru
            $miod = Material::where('nazwa', 'LIKE', '%miód%')->first();
            if (!$miod) {
                // Sprawdź czy kod DOD-002 już istnieje
                $existingMaterial = Material::where('kod', 'DOD-002')->first();
                if (!$existingMaterial) {
                    // Stwórz materiał miód jeśli nie istnieje
                    $miod = Material::create([
                        'kod' => 'DOD-002',
                        'nazwa' => 'Miód naturalny',
                        'opis' => 'Miód wielokwiatowy do wypieków',
                        'typ' => 'dodatki',
                        'jednostka_podstawowa' => 'kg',
                        'stan_aktualny' => 5.0,
                        'stan_minimalny' => 1.0,
                        'stan_optymalny' => 10.0,
                        'cena_zakupu_gr' => 2500, // 25 zł za kg
                        'aktywny' => true,
                    ]);
                } else {
                    $miod = $existingMaterial;
                }
            }

            // Dodaj miód jako zamiennik cukru
            $this->addSubstituteToRecipeMaterial($recipeRogale, $cukierBialy, [
                [
                    'material_id' => $miod->id,
                    'wspolczynnik_przeliczenia' => 0.7, // 70g miodu zamiast 100g cukru
                    'uwagi' => 'Zmniejsz ilość płynów o 20ml, ciasto będzie bardziej wilgotne'
                ]
            ]);
        }

        echo "Dodano zamienniki składników do przepisów!\n";

        // Wyświetl podsumowanie
        $this->showSubstituteSummary();
    }

    private function addSubstituteToRecipeMaterial(Recipe $recipe, Material $originalMaterial, array $substitutes)
    {
        // Znajdź materiał w krokach przepisu (przez recipe_step_materials)
        $stepMaterials = DB::table('recipe_step_materials')
            ->join('recipe_steps', 'recipe_step_materials.recipe_step_id', '=', 'recipe_steps.id')
            ->where('recipe_steps.recipe_id', $recipe->id)
            ->where('recipe_step_materials.material_id', $originalMaterial->id)
            ->select('recipe_step_materials.*')
            ->get();

        if ($stepMaterials->count() > 0) {
            foreach ($stepMaterials as $stepMaterial) {
                DB::table('recipe_step_materials')
                    ->where('id', $stepMaterial->id)
                    ->update([
                        'substitutes' => json_encode($substitutes),
                        'has_substitutes' => true,
                        'updated_at' => now()
                    ]);
            }

            echo "Dodano zamienniki dla {$originalMaterial->nazwa} w przepisie {$recipe->nazwa} ({$stepMaterials->count()} wystąpień)\n";
        } else {
            echo "Nie znaleziono materiału {$originalMaterial->nazwa} w przepisie {$recipe->nazwa}\n";
        }
    }

    private function showSubstituteSummary()
    {
        echo "\n=== PODSUMOWANIE ZAMIENNIKÓW ===\n";

        $recipesWithSubstitutes = DB::table('recipe_step_materials')
            ->join('recipe_steps', 'recipe_step_materials.recipe_step_id', '=', 'recipe_steps.id')
            ->join('recipes', 'recipe_steps.recipe_id', '=', 'recipes.id')
            ->join('materials', 'recipe_step_materials.material_id', '=', 'materials.id')
            ->where('recipe_step_materials.has_substitutes', true)
            ->select('recipes.nazwa as recipe_name', 'materials.nazwa as material_name', 'recipe_step_materials.substitutes')
            ->get();

        foreach ($recipesWithSubstitutes as $item) {
            echo "Przepis: {$item->recipe_name}\n";
            echo "  Składnik: {$item->material_name}\n";

            $substitutes = json_decode($item->substitutes, true);
            if (is_array($substitutes)) {
                foreach ($substitutes as $substitute) {
                    $substituteMaterial = Material::find($substitute['material_id']);
                    if ($substituteMaterial) {
                        echo "    → {$substituteMaterial->nazwa} (x{$substitute['wspolczynnik_przeliczenia']})\n";
                        if (isset($substitute['uwagi'])) {
                            echo "      {$substitute['uwagi']}\n";
                        }
                    }
                }
            }
            echo "\n";
        }
    }
}
