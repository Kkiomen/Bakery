<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Models\Material;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RecipeForm extends Component
{
    public ?Recipe $recipe = null;
    public bool $isEditing = false;

    // Pola formularza - dane podstawowe
    public $kod = '';
    public $nazwa = '';
    public $opis = '';
    public $product_id = '';
    public $kategoria = '';
    public $poziom_trudnosci = 'średni';
    public $autor = '';
    public $wersja = '1.0';

    // Wydajność
    public $ilosc_porcji = 1;
    public $waga_jednostkowa_g = '';

    // Czasy
    public $czas_przygotowania_min = '';
    public $czas_wypiekania_min = '';
    public $czas_calkowity_min = '';

    // Parametry wypiekania
    public $temperatura_c = '';
    public $instrukcje_wypiekania = '';

    // Uwagi
    public $uwagi = '';
    public $wskazowki = '';

    // Status
    public $aktywny = true;
    public $testowany = false;

    // Składniki w procesach - nie ma już globalnych składników

    // Kroki - tablica z procesami i ich składnikami
    public $steps = [];
    public $showStepModal = false;
    public $editingStepIndex = null;
    public $stepType = '';
    public $stepName = '';
    public $stepDescription = '';
    public $stepTime = '';
    public $stepTemperature = '';
    public $stepHumidity = '';
    public $stepTools = '';
    public $stepTips = '';
    public $stepNotes = '';
    public $stepCriteria = '';
    public $stepErrors = '';
    public $stepRequired = true;

    // Składniki w procesie
    public $stepMaterials = [];
    public $showStepMaterialModal = false;
    public $selectedMaterialId = '';
    public $materialAmount = '';
    public $materialUnit = '';
    public $materialNotes = '';
    public $materialPreparation = '';
    public $materialTemperature = '';
    public $materialOptional = false;

    // Zamienniki składników
    public $showSubstituteModal = false;
    public $editingMaterialIndex = null;
    public $editingStepIndexForSubstitute = null;
    public $materialSubstitutes = [];
    public $selectedSubstituteMaterialId = '';
    public $substituteConversionFactor = 1.0;
    public $substituteNotes = '';

    protected $rules = [
        'kod' => 'required|string|max:50|unique:recipes,kod',
        'nazwa' => 'required|string|max:255',
        'opis' => 'nullable|string',
        'product_id' => 'nullable|exists:products,id',
        'kategoria' => 'required|string',
        'poziom_trudnosci' => 'required|in:łatwy,średni,trudny',
        'autor' => 'nullable|string|max:255',
        'wersja' => 'required|string|max:10',
        'ilosc_porcji' => 'required|integer|min:1',
        'waga_jednostkowa_g' => 'nullable|numeric|min:0',
        'czas_przygotowania_min' => 'nullable|integer|min:0',
        'czas_wypiekania_min' => 'nullable|integer|min:0',
        'czas_calkowity_min' => 'nullable|integer|min:0',
        'temperatura_c' => 'nullable|integer|min:0|max:300',
        'instrukcje_wypiekania' => 'nullable|string',
        'uwagi' => 'nullable|string',
        'wskazowki' => 'nullable|string',
        'aktywny' => 'boolean',
        'testowany' => 'boolean',
    ];

    public function mount(?Recipe $recipe = null)
    {
        if ($recipe && $recipe->exists) {
            $this->recipe = $recipe;
            $this->isEditing = true;
            $this->loadRecipeData();
        } else {
            // Nowa receptura - ustaw domyślne wartości
            $this->recipe = new Recipe();
            $this->isEditing = false;
            $this->steps = [];

            // Ustaw domyślne wartości
            $this->autor = Auth::check() ? Auth::user()->name : 'System';
            $this->kategoria = 'pieczywo';
            $this->poziom_trudnosci = 'średni';
            $this->wersja = '1.0';
            $this->aktywny = true;
            $this->testowany = false;
        }
    }

    private function loadRecipeData()
    {
        // Załaduj recepturę z relacjami
        $this->recipe->load(['steps.materials']);

        $this->kod = $this->recipe->kod;
        $this->nazwa = $this->recipe->nazwa;
        $this->opis = $this->recipe->opis ?? '';
        $this->product_id = $this->recipe->product_id;
        $this->kategoria = $this->recipe->kategoria;
        $this->poziom_trudnosci = $this->recipe->poziom_trudnosci;
        $this->autor = $this->recipe->autor ?? '';
        $this->wersja = $this->recipe->wersja;
        $this->ilosc_porcji = $this->recipe->ilosc_porcji;
        $this->waga_jednostkowa_g = $this->recipe->waga_jednostkowa_g;
        $this->czas_przygotowania_min = $this->recipe->czas_przygotowania_min;
        $this->czas_wypiekania_min = $this->recipe->czas_wypiekania_min;
        $this->czas_calkowity_min = $this->recipe->czas_calkowity_min;
        $this->temperatura_c = $this->recipe->temperatura_c;
        $this->instrukcje_wypiekania = $this->recipe->instrukcje_wypiekania ?? '';
        $this->uwagi = $this->recipe->uwagi ?? '';
        $this->wskazowki = $this->recipe->wskazowki ?? '';
        $this->aktywny = $this->recipe->aktywny;
        $this->testowany = $this->recipe->testowany;

        // Załaduj kroki z ich składnikami
        $this->steps = $this->recipe->steps->map(function ($step) {
            // Załaduj składniki dla tego kroku
            $materials = $step->materials->map(function ($material) {
                return [
                    'material_id' => $material->id,
                    'material_name' => $material->nazwa,
                    'material_unit' => $material->jednostka_podstawowa,
                    'amount' => $material->pivot->ilosc,
                    'unit' => $material->pivot->jednostka,
                    'notes' => $material->pivot->uwagi,
                    'preparation' => $material->pivot->sposob_przygotowania,
                    'temperature' => $material->pivot->temperatura_c,
                    'optional' => $material->pivot->opcjonalny,
                    'order' => $material->pivot->kolejnosc ?? 0,
                    'substitutes' => json_decode($material->pivot->substitutes ?? '[]', true),
                    'has_substitutes' => (bool) $material->pivot->has_substitutes,
                ];
            })->sortBy('order')->values()->toArray();

            return [
                'type' => $step->typ,
                'name' => $step->nazwa,
                'description' => $step->opis,
                'time' => $step->czas_min,
                'temperature' => $step->temperatura_c,
                'humidity' => $step->wilgotnosc_proc,
                'tools' => $step->narzedzia,
                'tips' => $step->wskazowki,
                'notes' => $step->uwagi,
                'criteria' => $step->kryteria_oceny,
                'errors' => $step->czeste_bledy,
                'required' => $step->obowiazkowy,
                'order' => $step->kolejnosc,
                'materials' => $materials, // Składniki w tym procesie
            ];
        })->sortBy('order')->values()->toArray();
    }

    // Zarządzanie składnikami w procesach
    public function openStepMaterialModal($stepIndex)
    {
        $this->editingStepIndex = $stepIndex;
        $this->resetStepMaterialForm();

        // Inicjalizuj tablicę składników jeśli nie istnieje
        if (!isset($this->steps[$stepIndex]['materials'])) {
            $this->steps[$stepIndex]['materials'] = [];
        }

        $this->showStepMaterialModal = true;
    }

    public function closeStepMaterialModal()
    {
        $this->showStepMaterialModal = false;
        $this->resetStepMaterialForm();
        $this->editingStepIndex = null;
    }

    private function resetStepMaterialForm()
    {
        $this->selectedMaterialId = '';
        $this->materialAmount = '';
        $this->materialUnit = '';
        $this->materialNotes = '';
        $this->materialPreparation = '';
        $this->materialTemperature = '';
        $this->materialOptional = false;
    }

    public function updatedSelectedMaterialId()
    {
        if ($this->selectedMaterialId) {
            $material = Material::find($this->selectedMaterialId);
            if ($material) {
                // Ustaw domyślną jednostkę na podstawie materiału
                $this->materialUnit = $this->getDefaultUnit($material);
            }
        }
    }

    private function getDefaultUnit(Material $material): string
    {
        // Inteligentne dobieranie jednostek
        return match($material->typ) {
            'mąka' => 'g',
            'cukier' => 'g',
            'drożdże' => 'g',
            'tłuszcze' => $material->jednostka_podstawowa === 'l' ? 'ml' : 'g',
            'nabiał' => 'ml',
            'jajka' => 'szt',
            'dodatki' => 'g',
            'przyprawy' => 'g',
            'owoce' => 'g',
            'orzechy' => 'g',
            default => $material->jednostka_podstawowa,
        };
    }

    public function addStepMaterial()
    {
        $this->validate([
            'selectedMaterialId' => 'required|exists:materials,id',
            'materialAmount' => 'required|numeric|min:0.001',
            'materialUnit' => 'required|string',
        ]);

        if ($this->editingStepIndex === null) {
            return;
        }

        $material = Material::find($this->selectedMaterialId);

        // Inicjalizuj tablicę składników jeśli nie istnieje
        if (!isset($this->steps[$this->editingStepIndex]['materials'])) {
            $this->steps[$this->editingStepIndex]['materials'] = [];
        }

        // Sprawdź czy składnik już nie istnieje w tym procesie
        $existingIndex = collect($this->steps[$this->editingStepIndex]['materials'])->search(function ($stepMaterial) {
            return $stepMaterial['material_id'] == $this->selectedMaterialId;
        });

        $materialData = [
            'material_id' => $this->selectedMaterialId,
            'material_name' => $material->nazwa,
            'material_unit' => $material->jednostka_podstawowa,
            'amount' => $this->materialAmount,
            'unit' => $this->materialUnit,
            'notes' => $this->materialNotes,
            'preparation' => $this->materialPreparation,
            'temperature' => $this->materialTemperature ?: null,
            'optional' => $this->materialOptional,
            'order' => count($this->steps[$this->editingStepIndex]['materials']),
        ];

        if ($existingIndex !== false) {
            // Aktualizuj istniejący składnik
            $this->steps[$this->editingStepIndex]['materials'][$existingIndex] = $materialData;
        } else {
            // Dodaj nowy składnik
            $this->steps[$this->editingStepIndex]['materials'][] = $materialData;
        }

        $this->closeStepMaterialModal();
    }

    public function removeStepMaterial($stepIndex, $materialIndex)
    {
        unset($this->steps[$stepIndex]['materials'][$materialIndex]);
        $this->steps[$stepIndex]['materials'] = array_values($this->steps[$stepIndex]['materials']);

        // Przenumeruj kolejność
        foreach ($this->steps[$stepIndex]['materials'] as $i => &$material) {
            $material['order'] = $i;
        }
    }

    // Zarządzanie zamiennikami składników
    public function openSubstituteModal($stepIndex, $materialIndex)
    {
        $this->editingStepIndexForSubstitute = $stepIndex;
        $this->editingMaterialIndex = $materialIndex;

        // Załaduj obecne zamienniki
        $material = $this->steps[$stepIndex]['materials'][$materialIndex];
        $this->materialSubstitutes = $material['substitutes'] ?? [];

        $this->resetSubstituteForm();
        $this->showSubstituteModal = true;
    }

    public function closeSubstituteModal()
    {
        $this->showSubstituteModal = false;
        $this->resetSubstituteForm();
        $this->editingStepIndexForSubstitute = null;
        $this->editingMaterialIndex = null;
    }

    private function resetSubstituteForm()
    {
        $this->selectedSubstituteMaterialId = '';
        $this->substituteConversionFactor = 1.0;
        $this->substituteNotes = '';
    }

    public function addSubstitute()
    {
        $this->validate([
            'selectedSubstituteMaterialId' => 'required|exists:materials,id',
            'substituteConversionFactor' => 'required|numeric|min:0.001',
        ]);

        if ($this->editingStepIndexForSubstitute === null || $this->editingMaterialIndex === null) {
            return;
        }

        $substitute = Material::find($this->selectedSubstituteMaterialId);

        // Sprawdź czy zamiennik już nie istnieje
        $existingIndex = collect($this->materialSubstitutes)->search(function ($sub) {
            return $sub['material_id'] == $this->selectedSubstituteMaterialId;
        });

        $substituteData = [
            'material_id' => $this->selectedSubstituteMaterialId,
            'material_name' => $substitute->nazwa,
            'wspolczynnik_przeliczenia' => $this->substituteConversionFactor,
            'uwagi' => $this->substituteNotes,
            'jednostka' => $substitute->jednostka_podstawowa,
        ];

        if ($existingIndex !== false) {
            $this->materialSubstitutes[$existingIndex] = $substituteData;
        } else {
            $this->materialSubstitutes[] = $substituteData;
        }

        // Zaktualizuj składnik w kroku
        $this->steps[$this->editingStepIndexForSubstitute]['materials'][$this->editingMaterialIndex]['substitutes'] = $this->materialSubstitutes;
        $this->steps[$this->editingStepIndexForSubstitute]['materials'][$this->editingMaterialIndex]['has_substitutes'] = count($this->materialSubstitutes) > 0;

        $this->resetSubstituteForm();
    }

    public function removeSubstitute($substituteIndex)
    {
        unset($this->materialSubstitutes[$substituteIndex]);
        $this->materialSubstitutes = array_values($this->materialSubstitutes);

        if ($this->editingStepIndexForSubstitute !== null && $this->editingMaterialIndex !== null) {
            $this->steps[$this->editingStepIndexForSubstitute]['materials'][$this->editingMaterialIndex]['substitutes'] = $this->materialSubstitutes;
            $this->steps[$this->editingStepIndexForSubstitute]['materials'][$this->editingMaterialIndex]['has_substitutes'] = count($this->materialSubstitutes) > 0;
        }
    }

    public function getAvailableSubstitutesForMaterial($materialId)
    {
        $material = Material::find($materialId);
        if (!$material) {
            return collect();
        }

        // Pobierz materiały tego samego typu, ale wykluczając obecny materiał
        return Material::active()
            ->where('typ', $material->typ)
            ->where('id', '!=', $materialId)
            ->orderBy('nazwa')
            ->get();
    }

    public function getCurrentMaterialIdForSubstitute()
    {
        if ($this->editingStepIndexForSubstitute !== null &&
            $this->editingMaterialIndex !== null &&
            isset($this->steps[$this->editingStepIndexForSubstitute]['materials'][$this->editingMaterialIndex])) {
            return $this->steps[$this->editingStepIndexForSubstitute]['materials'][$this->editingMaterialIndex]['material_id'];
        }
        return null;
    }

    // Zarządzanie krokami
    public function openStepModal()
    {
        $this->resetStepForm();
        $this->editingStepIndex = null;
        $this->showStepModal = true;
    }

    public function editStep($index)
    {
        $step = $this->steps[$index];
        $this->editingStepIndex = $index;

        $this->stepType = $step['type'];
        $this->stepName = $step['name'];
        $this->stepDescription = $step['description'];
        $this->stepTime = $step['time'];
        $this->stepTemperature = $step['temperature'];
        $this->stepHumidity = $step['humidity'];
        $this->stepTools = $step['tools'];
        $this->stepTips = $step['tips'];
        $this->stepNotes = $step['notes'];
        $this->stepCriteria = $step['criteria'];
        $this->stepErrors = $step['errors'];
        $this->stepRequired = $step['required'];

        $this->showStepModal = true;
    }

    public function closeStepModal()
    {
        $this->showStepModal = false;
        $this->resetStepForm();
        $this->editingStepIndex = null;
    }

    private function resetStepForm()
    {
        $this->stepType = '';
        $this->stepName = '';
        $this->stepDescription = '';
        $this->stepTime = '';
        $this->stepTemperature = '';
        $this->stepHumidity = '';
        $this->stepTools = '';
        $this->stepTips = '';
        $this->stepNotes = '';
        $this->stepCriteria = '';
        $this->stepErrors = '';
        $this->stepRequired = true;
    }

    public function addStep()
    {
        $this->validate([
            'stepType' => 'required|string',
            'stepName' => 'required|string|max:255',
            'stepDescription' => 'required|string',
        ]);

        $stepData = [
            'type' => $this->stepType,
            'name' => $this->stepName,
            'description' => $this->stepDescription,
            'time' => $this->stepTime ?: null,
            'temperature' => $this->stepTemperature ?: null,
            'humidity' => $this->stepHumidity ?: null,
            'tools' => $this->stepTools,
            'tips' => $this->stepTips,
            'notes' => $this->stepNotes,
            'criteria' => $this->stepCriteria,
            'errors' => $this->stepErrors,
            'required' => $this->stepRequired,
            'order' => $this->editingStepIndex !== null ? $this->steps[$this->editingStepIndex]['order'] : count($this->steps),
            'materials' => $this->editingStepIndex !== null ? ($this->steps[$this->editingStepIndex]['materials'] ?? []) : [], // Zachowaj istniejące składniki lub utwórz pustą tablicę
        ];

        if ($this->editingStepIndex !== null) {
            $this->steps[$this->editingStepIndex] = $stepData;
        } else {
            $this->steps[] = $stepData;
        }

        $this->closeStepModal();
    }

    public function removeStep($index)
    {
        unset($this->steps[$index]);
        $this->steps = array_values($this->steps);

        // Przenumeruj kolejność
        foreach ($this->steps as $i => &$step) {
            $step['order'] = $i;
        }
    }

    public function moveStepUp($index)
    {
        if ($index > 0) {
            $temp = $this->steps[$index];
            $this->steps[$index] = $this->steps[$index - 1];
            $this->steps[$index - 1] = $temp;

            // Aktualizuj kolejność
            $this->steps[$index]['order'] = $index;
            $this->steps[$index - 1]['order'] = $index - 1;
        }
    }

    public function moveStepDown($index)
    {
        if ($index < count($this->steps) - 1) {
            $temp = $this->steps[$index];
            $this->steps[$index] = $this->steps[$index + 1];
            $this->steps[$index + 1] = $temp;

            // Aktualizuj kolejność
            $this->steps[$index]['order'] = $index;
            $this->steps[$index + 1]['order'] = $index + 1;
        }
    }

    public function save()
    {
        // Dostosuj reguły walidacji dla edycji
        $rules = $this->rules;
        if ($this->isEditing) {
            $rules['kod'] = 'required|string|max:50|unique:recipes,kod,' . $this->recipe->id;
        }

        $this->validate($rules);

        $data = [
            'kod' => $this->kod,
            'nazwa' => $this->nazwa,
            'opis' => $this->opis ?: null,
            'product_id' => $this->product_id ?: null,
            'kategoria' => $this->kategoria,
            'poziom_trudnosci' => $this->poziom_trudnosci,
            'autor' => $this->autor ?: null,
            'wersja' => $this->wersja,
            'ilosc_porcji' => $this->ilosc_porcji,
            'waga_jednostkowa_g' => $this->waga_jednostkowa_g ?: null,
            'czas_przygotowania_min' => $this->czas_przygotowania_min ?: null,
            'czas_wypiekania_min' => $this->czas_wypiekania_min ?: null,
            'czas_calkowity_min' => $this->czas_calkowity_min ?: null,
            'temperatura_c' => $this->temperatura_c ?: null,
            'instrukcje_wypiekania' => $this->instrukcje_wypiekania ?: null,
            'uwagi' => $this->uwagi ?: null,
            'wskazowki' => $this->wskazowki ?: null,
            'aktywny' => $this->aktywny,
            'testowany' => $this->testowany,
        ];

        if ($this->isEditing) {
            $this->recipe->update($data);
            $recipe = $this->recipe;
        } else {
            $recipe = Recipe::create($data);
        }

        // Usuń stare składniki globalne (już nie używane)
        $recipe->materials()->detach();

        // Zapisz kroki z ich składnikami
        $recipe->steps()->delete(); // Usuń stare kroki
        foreach ($this->steps as $index => $step) {
            $recipeStep = $recipe->steps()->create([
                'kolejnosc' => $index,
                'typ' => $step['type'],
                'nazwa' => $step['name'],
                'opis' => $step['description'],
                'czas_min' => $step['time'],
                'temperatura_c' => $step['temperature'],
                'wilgotnosc_proc' => $step['humidity'],
                'narzedzia' => $step['tools'],
                'wskazowki' => $step['tips'],
                'uwagi' => $step['notes'],
                'kryteria_oceny' => $step['criteria'],
                'czeste_bledy' => $step['errors'],
                'obowiazkowy' => $step['required'],
                'automatyczny' => false,
            ]);

            // Zapisz składniki dla tego kroku
            if (isset($step['materials']) && is_array($step['materials'])) {
                foreach ($step['materials'] as $materialIndex => $material) {
                    $recipeStep->materials()->attach($material['material_id'], [
                        'ilosc' => $material['amount'],
                        'jednostka' => $material['unit'],
                        'uwagi' => $material['notes'],
                        'kolejnosc' => $materialIndex,
                        'opcjonalny' => $material['optional'],
                        'sposob_przygotowania' => $material['preparation'],
                        'temperatura_c' => $material['temperature'],
                        'substitutes' => json_encode($material['substitutes'] ?? []),
                        'has_substitutes' => isset($material['has_substitutes']) ? $material['has_substitutes'] : false,
                    ]);
                }
            }
        }

        $this->dispatch('recipe-saved', $this->isEditing ? 'Receptura została zaktualizowana' : 'Receptura została dodana');
        return redirect()->route('recipes.index');
    }

    public function cancel()
    {
        return redirect()->route('recipes.index');
    }

    // Podsumowanie wszystkich składników z receptury
    public function getAllMaterials()
    {
        $allMaterials = [];

        foreach ($this->steps as $stepIndex => $step) {
            if (isset($step['materials']) && is_array($step['materials'])) {
                foreach ($step['materials'] as $material) {
                    $materialId = $material['material_id'];

                    if (!isset($allMaterials[$materialId])) {
                        $allMaterials[$materialId] = [
                            'material_id' => $materialId,
                            'material_name' => $material['material_name'],
                            'material_unit' => $material['material_unit'],
                            'total_amount' => 0,
                            'unit' => $material['unit'],
                            'usages' => [],
                            'has_substitutes' => $material['has_substitutes'] ?? false,
                            'substitutes' => $material['substitutes'] ?? []
                        ];
                    }

                    // Dodaj użycie w tym procesie
                    $allMaterials[$materialId]['usages'][] = [
                        'step_index' => $stepIndex,
                        'step_name' => $step['name'],
                        'amount' => $material['amount'],
                        'unit' => $material['unit'],
                        'preparation' => $material['preparation'],
                        'temperature' => $material['temperature'],
                        'optional' => $material['optional']
                    ];

                    // Sumuj ilość (tylko jeśli ta sama jednostka)
                    if ($allMaterials[$materialId]['unit'] === $material['unit']) {
                        $allMaterials[$materialId]['total_amount'] += $material['amount'];
                    }

                    // Aktualizuj zamienniki (bierz z ostatniego użycia)
                    if (isset($material['has_substitutes']) && $material['has_substitutes']) {
                        $allMaterials[$materialId]['has_substitutes'] = true;
                        $allMaterials[$materialId]['substitutes'] = $material['substitutes'];
                    }
                }
            }
        }

        return collect($allMaterials)->values()->sortBy('material_name');
    }

    // Obliczanie kosztów receptury
    public function getCostAnalysis()
    {
        $allMaterials = $this->getAllMaterials();
        $totalCost = 0;
        $materialCosts = [];
        $substituteSavings = [];

        foreach ($allMaterials as $material) {
            $materialModel = Material::find($material['material_id']);
            if (!$materialModel || !$materialModel->cena_zakupu_gr) {
                continue;
            }

            // Oblicz koszt podstawowy
            $baseAmount = $this->convertToBaseUnit(
                $material['total_amount'],
                $material['unit'],
                $materialModel->jednostka_podstawowa
            );

            $materialCost = ($materialModel->cena_zakupu_gr / 100) * $baseAmount;
            $totalCost += $materialCost;

            $materialCosts[] = [
                'material_id' => $material['material_id'],
                'material_name' => $material['material_name'],
                'amount' => $material['total_amount'],
                'unit' => $material['unit'],
                'base_amount' => $baseAmount,
                'base_unit' => $materialModel->jednostka_podstawowa,
                'price_per_unit' => $materialModel->cena_zakupu_gr / 100,
                'total_cost' => $materialCost,
                'has_substitutes' => $material['has_substitutes'],
                'substitutes' => $material['substitutes']
            ];

            // Oblicz potencjalne oszczędności z zamienników
            if ($material['has_substitutes'] && count($material['substitutes']) > 0) {
                foreach ($material['substitutes'] as $substitute) {
                    $substituteModel = Material::find($substitute['material_id']);
                    if (!$substituteModel || !$substituteModel->cena_zakupu_gr) {
                        continue;
                    }

                    $substituteAmount = $baseAmount * $substitute['wspolczynnik_przeliczenia'];
                    $substituteCost = ($substituteModel->cena_zakupu_gr / 100) * $substituteAmount;
                    $savings = $materialCost - $substituteCost;

                    if ($savings != 0) {
                        $substituteSavings[] = [
                            'original_material' => $material['material_name'],
                            'substitute_material' => $substitute['material_name'],
                            'original_cost' => $materialCost,
                            'substitute_cost' => $substituteCost,
                            'savings' => $savings,
                            'savings_percent' => $materialCost > 0 ? ($savings / $materialCost) * 100 : 0,
                            'conversion_factor' => $substitute['wspolczynnik_przeliczenia'],
                            'notes' => $substitute['uwagi']
                        ];
                    }
                }
            }
        }

        // Sortuj oszczędności od największych
        usort($substituteSavings, function($a, $b) {
            return $b['savings'] <=> $a['savings'];
        });

        return [
            'total_cost' => $totalCost,
            'cost_per_portion' => $this->ilosc_porcji > 0 ? $totalCost / $this->ilosc_porcji : 0,
            'cost_per_100g' => $this->waga_jednostkowa_g > 0 ? ($totalCost / ($this->ilosc_porcji * $this->waga_jednostkowa_g)) * 100 : 0,
            'material_costs' => $materialCosts,
            'substitute_savings' => $substituteSavings,
            'total_portions' => $this->ilosc_porcji,
            'portion_weight' => $this->waga_jednostkowa_g,
            'total_weight' => $this->ilosc_porcji * $this->waga_jednostkowa_g
        ];
    }

    private function convertToBaseUnit(float $amount, string $fromUnit, string $toUnit): float
    {
        // Konwersje jednostek
        $conversions = [
            'g' => ['kg' => 0.001],
            'kg' => ['g' => 1000],
            'ml' => ['l' => 0.001],
            'l' => ['ml' => 1000],
            'szt' => ['szt' => 1],
        ];

        if ($fromUnit === $toUnit) {
            return $amount;
        }

        if (isset($conversions[$fromUnit][$toUnit])) {
            return $amount * $conversions[$fromUnit][$toUnit];
        }

        return $amount; // Jeśli nie ma konwersji, zwróć oryginalną wartość
    }

    public function render()
    {
        $categories = Recipe::getAvailableCategories();
        $difficulties = Recipe::getAvailableDifficulties();
        $products = Product::active()->get();
        $materials = Material::active()->orderBy('typ')->orderBy('nazwa')->get();
        $stepTypes = \App\Models\RecipeStep::getAvailableTypes();

        // Przygotuj dane dla widoku
        try {
            $allMaterials = $this->getAllMaterials();
            $costAnalysis = $this->getCostAnalysis();
        } catch (\Exception $e) {
            $allMaterials = collect();
            $costAnalysis = [
                'total_cost' => 0,
                'cost_per_portion' => 0,
                'cost_per_100g' => 0,
                'total_weight' => 0,
                'material_costs' => [],
                'substitute_savings' => []
            ];
        }

        return view('livewire.recipes.recipe-form', [
            'categories' => $categories,
            'difficulties' => $difficulties,
            'products' => $products,
            'materials' => $materials,
            'stepTypes' => $stepTypes,
            'allMaterials' => $allMaterials,
            'costAnalysis' => $costAnalysis,
        ]);
    }
}
