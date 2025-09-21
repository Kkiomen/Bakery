<?php

namespace App\Livewire\Baker;

use App\Models\ProductionOrderItem;
use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public $productCard;
    public $showDetails = false;
    public $showSubstitutes = false;

    public function mount($productCard)
    {
        $this->productCard = $productCard;
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    public function toggleSubstitutes()
    {
        $this->showSubstitutes = !$this->showSubstitutes;
    }

    public function updateItemStep($itemId, $step)
    {
        $item = ProductionOrderItem::findOrFail($itemId);
        $item->moveToStep($step);

        $this->dispatch('item-updated');
        session()->flash('success', 'Status został zaktualizowany.');
    }

    public function nextStep($itemId)
    {
        $item = ProductionOrderItem::findOrFail($itemId);
        $item->moveToNextStep();

        $this->dispatch('item-updated');
        session()->flash('success', 'Przeszedł do kolejnego kroku.');
    }

    public function showRecipe($step)
    {
        $this->dispatch('show-recipe', [
            'productId' => $this->productCard['product']->id,
            'step' => $step
        ]);
    }

    public function render()
    {
        return view('livewire.baker.product-card');
    }
}
