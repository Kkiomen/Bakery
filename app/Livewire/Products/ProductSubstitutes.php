<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSubstitutes extends Component
{
    use WithPagination;

    public Product $product;
    public $showModal = false;
    public $search = '';
    public $selectedSubstitutes = [];
    public $substitutePriorities = [];
    public $substituteNotes = [];

    protected $rules = [
        'substitutePriorities.*' => 'integer|min:0',
        'substituteNotes.*' => 'nullable|string|max:500',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->loadCurrentSubstitutes();
    }

    public function loadCurrentSubstitutes()
    {
        $substitutes = $this->product->substitutes;

        foreach ($substitutes as $substitute) {
            $this->selectedSubstitutes[] = $substitute->id;
            $this->substitutePriorities[$substitute->id] = $substitute->pivot->priorytet;
            $this->substituteNotes[$substitute->id] = $substitute->pivot->uwagi;
        }
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->search = '';
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->search = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleSubstitute($productId)
    {
        if (in_array($productId, $this->selectedSubstitutes)) {
            // Usuń z listy
            $this->selectedSubstitutes = array_values(
                array_diff($this->selectedSubstitutes, [$productId])
            );
            unset($this->substitutePriorities[$productId]);
            unset($this->substituteNotes[$productId]);
        } else {
            // Dodaj do listy
            $this->selectedSubstitutes[] = $productId;
            $this->substitutePriorities[$productId] = 0;
            $this->substituteNotes[$productId] = '';
        }
    }

    public function removeSubstitute($productId)
    {
        $this->selectedSubstitutes = array_values(
            array_diff($this->selectedSubstitutes, [$productId])
        );
        unset($this->substitutePriorities[$productId]);
        unset($this->substituteNotes[$productId]);
    }

    public function updatePriority($productId, $priority)
    {
        $this->substitutePriorities[$productId] = (int) $priority;
    }

    public function updateNotes($productId, $notes)
    {
        $this->substituteNotes[$productId] = $notes;
    }

    public function saveSubstitutes()
    {
        $this->validate();

        try {
            // Usuń wszystkie istniejące zamienniki
            $currentSubstitutes = $this->product->substitutes->pluck('id')->toArray();
            foreach ($currentSubstitutes as $substituteId) {
                $substitute = Product::find($substituteId);
                if ($substitute) {
                    $this->product->removeSubstitute($substitute);
                }
            }

            // Dodaj nowe zamienniki
            foreach ($this->selectedSubstitutes as $substituteId) {
                $substitute = Product::find($substituteId);
                if ($substitute) {
                    $priority = $this->substitutePriorities[$substituteId] ?? 0;
                    $notes = $this->substituteNotes[$substituteId] ?? null;

                    $this->product->addSubstitute($substitute, $priority, $notes);
                }
            }

            $this->dispatch('substitutes-updated', 'Zamienniki zostały zaktualizowane');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('substitutes-error', 'Wystąpił błąd podczas zapisywania zamienników: ' . $e->getMessage());
        }
    }

    public function getAvailableProductsProperty()
    {
        return Product::where('id', '!=', $this->product->id)
            ->where('aktywny', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nazwa', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->with('category')
            ->orderBy('nazwa')
            ->paginate(10);
    }

    public function getCurrentSubstitutesProperty()
    {
        return $this->product->substitutes()
            ->with('category')
            ->orderByPivot('priorytet')
            ->get();
    }

    public function render()
    {
        return view('livewire.products.product-substitutes', [
            'availableProducts' => $this->availableProducts,
            'currentSubstitutes' => $this->currentSubstitutes,
        ]);
    }
}
