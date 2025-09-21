<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Models\Material;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RecipeCreate extends Component
{
    // Pola formularza - dane podstawowe
    public $kod = '';
    public $nazwa = '';
    public $opis = '';
    public $product_id = '';
    public $kategoria = 'pieczywo';
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

    protected $messages = [
        'kod.required' => 'Kod receptury jest wymagany.',
        'kod.unique' => 'Ten kod już istnieje.',
        'nazwa.required' => 'Nazwa receptury jest wymagana.',
        'kategoria.required' => 'Kategoria jest wymagana.',
        'poziom_trudnosci.required' => 'Poziom trudności jest wymagany.',
        'poziom_trudnosci.in' => 'Nieprawidłowy poziom trudności.',
        'wersja.required' => 'Wersja jest wymagana.',
        'ilosc_porcji.required' => 'Ilość porcji jest wymagana.',
        'ilosc_porcji.min' => 'Ilość porcji musi być większa od 0.',
        'waga_jednostkowa_g.min' => 'Waga nie może być ujemna.',
        'czas_przygotowania_min.min' => 'Czas przygotowania nie może być ujemny.',
        'czas_wypiekania_min.min' => 'Czas wypiekania nie może być ujemny.',
        'czas_calkowity_min.min' => 'Czas całkowity nie może być ujemny.',
        'temperatura_c.min' => 'Temperatura nie może być ujemna.',
        'temperatura_c.max' => 'Temperatura nie może być wyższa niż 300°C.',
    ];

    public function mount()
    {
        // Inicjalizacja dla nowej receptury
        $this->steps = [];
        $this->autor = Auth::check() ? Auth::user()->name : 'System';
        $this->kategoria = 'pieczywo';
        $this->poziom_trudnosci = 'średni';
        $this->wersja = '1.0';
        $this->aktywny = true;
        $this->testowany = false;
        $this->ilosc_porcji = 1;
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
            'materials' => $this->editingStepIndex !== null ? ($this->steps[$this->editingStepIndex]['materials'] ?? []) : [],
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
        $this->validate();

        try {
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

            $recipe = Recipe::create($data);

            // Zapisz kroki z ich składnikami
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
                            'substitutes' => json_encode([]),
                            'has_substitutes' => false,
                        ]);
                    }
                }
            }

            session()->flash('success', 'Receptura została pomyślnie dodana!');

            return redirect()->route('recipes.edit', $recipe);

        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił błąd podczas zapisywania receptury: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('recipes.index');
    }

    public function render()
    {
        $categories = Recipe::getAvailableCategories();
        $difficulties = Recipe::getAvailableDifficulties();
        $products = Product::active()->get();
        $materials = Material::active()->orderBy('typ')->orderBy('nazwa')->get();
        $stepTypes = \App\Models\RecipeStep::getAvailableTypes();

        return view('livewire.recipes.recipe-create', [
            'categories' => $categories,
            'difficulties' => $difficulties,
            'products' => $products,
            'materials' => $materials,
            'stepTypes' => $stepTypes,
        ]);
    }
}
