<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class AdditionalMaterialSubstitutesSeeder extends Seeder
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
        $sol = Material::where('kod', 'DOD-001')->first();

        // Stwórz dodatkowe materiały jeśli nie istnieją
        $makaGrahamka = $this->createMaterialIfNotExists('MAK-003', 'Mąka graham', 'Mąka pełnoziarnista graham', 'mąka');
        $cukierTrzcinowy = $this->createMaterialIfNotExists('CUK-002', 'Cukier trzcinowy', 'Cukier trzcinowy nierafinowany', 'cukier');
        $solMorska = $this->createMaterialIfNotExists('DOD-003', 'Sól morska', 'Sól morska drobna', 'dodatki');

        // Dodaj zamienniki do nowych przepisów
        $this->addSubstitutesToNewRecipes($makaPszenna, $makaZytnia, $makaGrahamka, $cukierBialy, $cukierTrzcinowy, $sol, $solMorska);

        echo "Dodano zamienniki składników do nowych przepisów!\n";
        $this->showSubstituteSummary();
    }

    private function createMaterialIfNotExists(string $kod, string $nazwa, string $opis, string $typ): Material
    {
        $material = Material::where('kod', $kod)->first();

        if (!$material) {
            $material = Material::create([
                'kod' => $kod,
                'nazwa' => $nazwa,
                'opis' => $opis,
                'typ' => $typ,
                'jednostka_podstawowa' => 'kg',
                'stan_aktualny' => 10.0,
                'stan_minimalny' => 2.0,
                'stan_optymalny' => 20.0,
                'cena_zakupu_gr' => 500, // 5 zł za kg
                'aktywny' => true,
            ]);
            echo "Utworzono materiał: {$nazwa}\n";
        }

        return $material;
    }

    private function addSubstitutesToNewRecipes($makaPszenna, $makaZytnia, $makaGrahamka, $cukierBialy, $cukierTrzcinowy, $sol, $solMorska)
    {
        // Przepis na Chleb pszenny
        $chlebPszenny = Recipe::where('nazwa', 'Przepis na Chleb pszenny')->first();
        if ($chlebPszenny && $makaPszenna && $makaZytnia) {
            $this->addSubstituteToRecipeMaterial($chlebPszenny, $makaPszenna, [
                [
                    'material_id' => $makaZytnia->id,
                    'wspolczynnik_przeliczenia' => 0.9,
                    'uwagi' => 'Chleb będzie ciemniejszy i bardziej sycący'
                ],
                [
                    'material_id' => $makaGrahamka->id,
                    'wspolczynnik_przeliczenia' => 1.0,
                    'uwagi' => 'Chleb pełnoziarnisty, bardziej zdrowy'
                ]
            ]);
        }

        // Przepis na Bułka grahamka
        $bulkaGrahamka = Recipe::where('nazwa', 'Przepis na Bułka grahamka')->first();
        if ($bulkaGrahamka && $makaPszenna && $makaGrahamka) {
            $this->addSubstituteToRecipeMaterial($bulkaGrahamka, $makaPszenna, [
                [
                    'material_id' => $makaGrahamka->id,
                    'wspolczynnik_przeliczenia' => 1.0,
                    'uwagi' => 'Oryginalny składnik dla bułek graham'
                ]
            ]);
        }

        // Przepis na Ciastka owsiane
        $ciastkaOwsiane = Recipe::where('nazwa', 'Przepis na Ciastka owsiane')->first();
        if ($ciastkaOwsiane && $cukierBialy && $cukierTrzcinowy) {
            $this->addSubstituteToRecipeMaterial($ciastkaOwsiane, $cukierBialy, [
                [
                    'material_id' => $cukierTrzcinowy->id,
                    'wspolczynnik_przeliczenia' => 0.8,
                    'uwagi' => 'Ciastka będą mieć karmelowy smak'
                ]
            ]);
        }

        // Przepis na Chleb bezglutenowy
        $chlebBezglutenowy = Recipe::where('nazwa', 'Przepis na Chleb bezglutenowy')->first();
        if ($chlebBezglutenowy && $makaPszenna) {
            // Stwórz mąkę bezglutenową
            $makaBezglutenowa = $this->createMaterialIfNotExists('MAK-004', 'Mąka bezglutenowa', 'Mieszanka mąk bezglutenowych', 'mąka');

            $this->addSubstituteToRecipeMaterial($chlebBezglutenowy, $makaPszenna, [
                [
                    'material_id' => $makaBezglutenowa->id,
                    'wspolczynnik_przeliczenia' => 1.1,
                    'uwagi' => 'Obowiązkowy zamiennik dla osób z celiakią'
                ]
            ]);
        }

        // Dodaj zamienniki soli we wszystkich nowych przepisach
        if ($sol && $solMorska) {
            $newRecipes = Recipe::whereIn('nazwa', [
                'Przepis na Chleb pszenny',
                'Przepis na Bułka grahamka',
                'Przepis na Ciastka owsiane',
                'Przepis na Chleb bezglutenowy'
            ])->get();

            foreach ($newRecipes as $recipe) {
                $this->addSubstituteToRecipeMaterial($recipe, $sol, [
                    [
                        'material_id' => $solMorska->id,
                        'wspolczynnik_przeliczenia' => 1.0,
                        'uwagi' => 'Sól morska ma lepszy smak'
                    ]
                ]);
            }
        }
    }

    private function addSubstituteToRecipeMaterial(Recipe $recipe, Material $originalMaterial, array $substitutes)
    {
        // Znajdź materiał w krokach przepisu
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

            echo "Dodano zamienniki dla {$originalMaterial->nazwa} w przepisie {$recipe->nazwa}\n";
        }
    }

    private function showSubstituteSummary()
    {
        echo "\n=== WSZYSTKIE ZAMIENNIKI SKŁADNIKÓW ===\n";

        $recipesWithSubstitutes = DB::table('recipe_step_materials')
            ->join('recipe_steps', 'recipe_step_materials.recipe_step_id', '=', 'recipe_steps.id')
            ->join('recipes', 'recipe_steps.recipe_id', '=', 'recipes.id')
            ->join('materials', 'recipe_step_materials.material_id', '=', 'materials.id')
            ->where('recipe_step_materials.has_substitutes', true)
            ->select('recipes.nazwa as recipe_name', 'materials.nazwa as material_name', 'recipe_step_materials.substitutes')
            ->distinct()
            ->get();

        $groupedByRecipe = $recipesWithSubstitutes->groupBy('recipe_name');

        foreach ($groupedByRecipe as $recipeName => $materials) {
            echo "📋 {$recipeName}:\n";

            $uniqueMaterials = $materials->unique('material_name');
            foreach ($uniqueMaterials as $item) {
                echo "  🥄 {$item->material_name}:\n";

                $substitutes = json_decode($item->substitutes, true);
                if (is_array($substitutes)) {
                    foreach ($substitutes as $substitute) {
                        $substituteMaterial = Material::find($substitute['material_id']);
                        if ($substituteMaterial) {
                            echo "    → {$substituteMaterial->nazwa} (x{$substitute['wspolczynnik_przeliczenia']})\n";
                            if (isset($substitute['uwagi'])) {
                                echo "      💡 {$substitute['uwagi']}\n";
                            }
                        }
                    }
                }
            }
            echo "\n";
        }
    }
}

