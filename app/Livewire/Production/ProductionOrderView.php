<?php

namespace App\Livewire\Production;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use Livewire\Component;

class ProductionOrderView extends Component
{
    public ProductionOrder $order;
    public $showUpdateModal = false;
    public $selectedItem = null;
    public $producedQuantity = 0;

    public function mount(ProductionOrder $order)
    {
        $this->order = $order->load(['user', 'items.product']);
    }

    public function startProduction()
    {
        if ($this->order->canBeStarted()) {
            $this->order->startProduction();
            $this->order->refresh();
            session()->flash('success', 'Produkcja została rozpoczęta.');
        }
    }

    public function completeProduction()
    {
        if ($this->order->canBeCompleted()) {
            $this->order->completeProduction();
            $this->order->refresh();
            session()->flash('success', 'Zlecenie zostało zakończone.');
        }
    }

    public function cancelOrder()
    {
        if ($this->order->canBeCancelled()) {
            $this->order->cancelOrder();
            $this->order->refresh();
            session()->flash('success', 'Zlecenie zostało anulowane.');
        }
    }

    public function startItemProduction($itemId)
    {
        $item = ProductionOrderItem::findOrFail($itemId);

        if ($item->canBeStarted()) {
            $item->startProduction();
            $this->order->refresh();
            session()->flash('success', 'Rozpoczęto produkcję pozycji.');
        }
    }

    public function completeItem($itemId)
    {
        $item = ProductionOrderItem::findOrFail($itemId);
        $item->completeItem();
        $this->order->refresh();
        session()->flash('success', 'Pozycja została ukończona.');
    }

    public function openUpdateModal($itemId)
    {
        $this->selectedItem = ProductionOrderItem::findOrFail($itemId);
        $this->producedQuantity = $this->selectedItem->ilosc_wyprodukowana;
        $this->showUpdateModal = true;
    }

    public function updateProducedQuantity()
    {
        $this->validate([
            'producedQuantity' => 'required|integer|min:0|max:' . $this->selectedItem->ilosc,
        ], [
            'producedQuantity.required' => 'Podaj ilość wyprodukowaną.',
            'producedQuantity.max' => 'Ilość nie może być większa niż zamówiona.',
        ]);

        $this->selectedItem->updateProducedQuantity($this->producedQuantity);
        $this->order->refresh();

        $this->showUpdateModal = false;
        $this->selectedItem = null;
        $this->producedQuantity = 0;

        session()->flash('success', 'Ilość wyprodukowana została zaktualizowana.');
    }

    public function duplicateOrder()
    {
        $newOrder = $this->order->duplicate([
            'data_produkcji' => $this->order->data_produkcji->copy()->addDay(),
            'nazwa' => $this->order->nazwa . ' (kopia)',
        ]);

        session()->flash('success', 'Zlecenie zostało zduplikowane.');

        return redirect()->route('production.orders.show', $newOrder);
    }

    public function render()
    {
        return view('livewire.production.production-order-view');
    }
}
