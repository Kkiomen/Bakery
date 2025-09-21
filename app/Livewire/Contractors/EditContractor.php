<?php

namespace App\Livewire\Contractors;

use App\Models\Contractor;
use App\Models\ProductionOrder;
use Livewire\Component;
use Livewire\WithPagination;

class EditContractor extends Component
{
    use WithPagination;

    public Contractor $contractor;

    // Dane kontrahenta
    public $nazwa = '';
    public $nip = '';
    public $regon = '';
    public $adres = '';
    public $kod_pocztowy = '';
    public $miasto = '';
    public $telefon = '';
    public $email = '';
    public $osoba_kontaktowa = '';
    public $telefon_kontaktowy = '';
    public $typ = 'klient';
    public $aktywny = true;
    public $uwagi = '';
    public $latitude = null;
    public $longitude = null;

    // Filtry dla zleceń
    public $orderStatusFilter = '';
    public $orderDateFrom = '';
    public $orderDateTo = '';
    public $orderSearch = '';

    public function mount(Contractor $contractor)
    {
        $this->contractor = $contractor;
        $this->fill([
            'nazwa' => $contractor->nazwa,
            'nip' => $contractor->nip,
            'regon' => $contractor->regon,
            'adres' => $contractor->adres,
            'kod_pocztowy' => $contractor->kod_pocztowy,
            'miasto' => $contractor->miasto,
            'telefon' => $contractor->telefon,
            'email' => $contractor->email,
            'osoba_kontaktowa' => $contractor->osoba_kontaktowa,
            'telefon_kontaktowy' => $contractor->telefon_kontaktowy,
            'typ' => $contractor->typ,
            'aktywny' => $contractor->aktywny,
            'uwagi' => $contractor->uwagi,
            'latitude' => $contractor->latitude,
            'longitude' => $contractor->longitude,
        ]);
    }

    protected function rules()
    {
        return [
            'nazwa' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'regon' => 'nullable|string|max:20',
            'adres' => 'required|string|max:255',
            'kod_pocztowy' => 'required|string|max:10',
            'miasto' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'osoba_kontaktowa' => 'nullable|string|max:255',
            'telefon_kontaktowy' => 'nullable|string|max:20',
            'typ' => 'required|in:klient,dostawca,obydwa',
            'aktywny' => 'boolean',
            'uwagi' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->contractor->update([
            'nazwa' => $this->nazwa,
            'nip' => $this->nip,
            'regon' => $this->regon,
            'adres' => $this->adres,
            'kod_pocztowy' => $this->kod_pocztowy,
            'miasto' => $this->miasto,
            'telefon' => $this->telefon,
            'email' => $this->email,
            'osoba_kontaktowa' => $this->osoba_kontaktowa,
            'telefon_kontaktowy' => $this->telefon_kontaktowy,
            'typ' => $this->typ,
            'aktywny' => $this->aktywny,
            'uwagi' => $this->uwagi,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        session()->flash('success', 'Kontrahent został zaktualizowany pomyślnie.');
    }

    public function assignOrderToContractor($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);
        $order->update(['contractor_id' => $this->contractor->id]);

        session()->flash('success', 'Zlecenie zostało przypisane do kontrahenta.');
    }

    public function unassignOrderFromContractor($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);
        $order->update(['contractor_id' => null]);

        session()->flash('success', 'Zlecenie zostało odłączone od kontrahenta.');
    }

    public function getContractorOrders()
    {
        $query = $this->contractor->productionOrders()
            ->with(['items.product']);

        // Filtry
        if ($this->orderStatusFilter) {
            $query->byStatus($this->orderStatusFilter);
        }

        if ($this->orderDateFrom) {
            $query->whereDate('data_produkcji', '>=', $this->orderDateFrom);
        }

        if ($this->orderDateTo) {
            $query->whereDate('data_produkcji', '<=', $this->orderDateTo);
        }

        if ($this->orderSearch) {
            $query->where(function ($q) {
                $q->where('numer_zlecenia', 'like', '%' . $this->orderSearch . '%')
                  ->orWhere('klient', 'like', '%' . $this->orderSearch . '%')
                  ->orWhere('nazwa', 'like', '%' . $this->orderSearch . '%');
            });
        }

        // Sortowanie: niezrealizowane najpierw, potem po dacie
        return $query->orderByRaw("
            CASE
                WHEN status = 'zakonczone' THEN 1
                ELSE 0
            END ASC
        ")->orderBy('data_produkcji', 'desc')
          ->paginate(10, ['*'], 'contractorOrders');
    }

    public function getAvailableOrders()
    {
        // Zlecenia bez przypisanego kontrahenta
        return ProductionOrder::whereNull('contractor_id')
            ->with(['items.product'])
            ->orderBy('data_produkcji', 'desc')
            ->limit(20)
            ->get();
    }

    public function updatedOrderStatusFilter()
    {
        $this->resetPage('contractorOrders');
    }

    public function updatedOrderDateFrom()
    {
        $this->resetPage('contractorOrders');
    }

    public function updatedOrderDateTo()
    {
        $this->resetPage('contractorOrders');
    }

    public function updatedOrderSearch()
    {
        $this->resetPage('contractorOrders');
    }

    public function render()
    {
        return view('livewire.contractors.edit-contractor', [
            'contractorOrders' => $this->getContractorOrders(),
            'availableOrders' => $this->getAvailableOrders(),
        ]);
    }
}
