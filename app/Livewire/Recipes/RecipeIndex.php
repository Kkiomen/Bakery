<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class RecipeIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $categoryFilter = '';

    #[Url]
    public $difficultyFilter = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $testedFilter = '';

    #[Url]
    public $sortBy = 'nazwa';

    #[Url]
    public $sortDirection = 'asc';

    public $selectedRecipes = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'difficultyFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'testedFilter' => ['except' => ''],
        'sortBy' => ['except' => 'nazwa'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedDifficultyFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTestedFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRecipes = $this->getRecipes()->pluck('id')->toArray();
        } else {
            $this->selectedRecipes = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'difficultyFilter', 'statusFilter', 'testedFilter']);
        $this->resetPage();
    }

    public function toggleRecipeStatus($recipeId)
    {
        $recipe = Recipe::find($recipeId);
        if ($recipe) {
            $recipe->update(['aktywny' => !$recipe->aktywny]);
            $this->dispatch('recipe-updated', 'Status receptury został zmieniony');
        }
    }

    public function toggleRecipeTested($recipeId)
    {
        $recipe = Recipe::find($recipeId);
        if ($recipe) {
            $recipe->update(['testowany' => !$recipe->testowany]);
            $this->dispatch('recipe-updated', 'Status testowania został zmieniony');
        }
    }

    public function duplicateRecipe($recipeId)
    {
        $recipe = Recipe::with(['steps', 'materials'])->find($recipeId);
        if ($recipe) {
            $newRecipe = $recipe->replicate();
            $newRecipe->kod = $recipe->kod . '-KOPIA';
            $newRecipe->nazwa = $recipe->nazwa . ' (kopia)';
            $newRecipe->testowany = false;
            $newRecipe->save();

            // Kopiuj kroki
            foreach ($recipe->steps as $step) {
                $newStep = $step->replicate();
                $newStep->recipe_id = $newRecipe->id;
                $newStep->save();
            }

            // Kopiuj składniki
            foreach ($recipe->materials as $material) {
                $newRecipe->materials()->attach($material->id, [
                    'ilosc' => $material->pivot->ilosc,
                    'jednostka' => $material->pivot->jednostka,
                    'uwagi' => $material->pivot->uwagi,
                    'kolejnosc' => $material->pivot->kolejnosc,
                    'opcjonalny' => $material->pivot->opcjonalny,
                    'sposob_przygotowania' => $material->pivot->sposob_przygotowania,
                    'temperatura_c' => $material->pivot->temperatura_c,
                ]);
            }

            $this->dispatch('recipe-updated', 'Receptura została skopiowana');
        }
    }

    public function getRecipes()
    {
        $query = Recipe::query()
            ->with(['product', 'steps', 'materials'])
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->byCategory($this->categoryFilter);
            })
            ->when($this->difficultyFilter, function ($query) {
                $query->byDifficulty($this->difficultyFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('aktywny', $this->statusFilter === '1');
            })
            ->when($this->testedFilter !== '', function ($query) {
                $query->where('testowany', $this->testedFilter === '1');
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(20);
    }

    public function getStats()
    {
        return [
            'total' => Recipe::active()->count(),
            'tested' => Recipe::active()->tested()->count(),
            'categories' => Recipe::active()->distinct('kategoria')->count('kategoria'),
            'avg_time' => Recipe::active()->avg('czas_calkowity_min') ?? 0,
        ];
    }

    public function render()
    {
        $recipes = $this->getRecipes();
        $stats = $this->getStats();
        $categories = Recipe::getAvailableCategories();
        $difficulties = Recipe::getAvailableDifficulties();

        return view('livewire.recipes.recipe-index', [
            'recipes' => $recipes,
            'stats' => $stats,
            'categories' => $categories,
            'difficulties' => $difficulties,
        ]);
    }
}
