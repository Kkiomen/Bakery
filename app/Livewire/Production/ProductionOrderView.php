<?php

namespace App\Livewire\Production;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Models\Delivery;
use App\Models\Contractor;
use Carbon\Carbon;
use Livewire\Component;

class ProductionOrderView extends Component
{
    public ProductionOrder $order;
    public $showUpdateModal = false;
    public $selectedItem = null;
    public $producedQuantity = 0;

    // Właściwości dla zarządzania dostawami
    public $showDeliveryModal = false;
    public $deliveryDate = '';
    public $deliveryTimeFrom = '';
    public $deliveryTimeTo = '';
    public $contractorId = '';
    public $deliveryAddress = '';
    public $deliveryPostalCode = '';
    public $deliveryCity = '';
    public $deliveryNotes = '';
    public $clientName = '';
    public $clientPhone = '';
    public $clientEmail = '';
    public $contactPerson = '';
    public $contactPhone = '';
    public $selectedItems = [];
    public $completeOrderAfterDelivery = false;

    public function mount(ProductionOrder $order)
    {
        $this->order = $order->load(['user', 'items.product', 'deliveries.contractor', 'b2bOrder.client', 'contractor']);

        // Ustaw domyślną datę dostawy na następny dzień roboczy
        $this->deliveryDate = now()->addDay()->format('Y-m-d');
        $this->deliveryTimeFrom = '08:00';
        $this->deliveryTimeTo = '16:00';
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
            'data_produkcji' => Carbon::parse($this->order->data_produkcji)->addDay(),
            'nazwa' => $this->order->nazwa . ' (kopia)',
        ]);

        session()->flash('success', 'Zlecenie zostało zduplikowane.');

        return redirect()->route('production.orders.show', $newOrder);
    }

    // Metody zarządzania dostawami
    public function openDeliveryModal()
    {
        $this->showDeliveryModal = true;
        $this->resetDeliveryForm();

        // Inteligentnie wypełnij dane na podstawie źródła zlecenia
        $this->prefillDeliveryData();

        // Domyślnie zaznacz wszystkie gotowe pozycje
        $readyItems = $this->order->items()
            ->whereIn('status', ['zakonczona', 'w_produkcji'])
            ->where('ilosc_wyprodukowana', '>', 0)
            ->get();

        $this->selectedItems = $readyItems->pluck('id')->toArray();

        // Jeśli wszystkie pozycje zlecenia są gotowe i wybrane, sugeruj zakończenie zlecenia
        $allOrderItems = $this->order->items;
        $allItemsReady = $allOrderItems->every(function ($item) use ($readyItems) {
            return $readyItems->contains('id', $item->id) || $item->status === 'zakonczona';
        });

        if ($allItemsReady && $this->order->canBeCompleted()) {
            $this->completeOrderAfterDelivery = true;
        }
    }

    public function closeDeliveryModal()
    {
        $this->showDeliveryModal = false;
        $this->resetDeliveryForm();
    }

    private function resetDeliveryForm()
    {
        // Resetuj tylko podstawowe wartości - dane będą wypełnione przez prefillDeliveryData()
        $this->deliveryDate = now()->addDay()->format('Y-m-d');
        $this->deliveryTimeFrom = '08:00';
        $this->deliveryTimeTo = '16:00';
        $this->contractorId = '';
        $this->deliveryAddress = '';
        $this->deliveryPostalCode = '';
        $this->deliveryCity = '';
        $this->deliveryNotes = '';
        $this->clientName = '';
        $this->clientPhone = '';
        $this->clientEmail = '';
        $this->contactPerson = '';
        $this->contactPhone = '';
        $this->selectedItems = [];
        $this->completeOrderAfterDelivery = false;
    }

    public function createDelivery()
    {
        // Różne reguły walidacji w zależności od typu zlecenia
        $rules = [
            'deliveryDate' => 'required|date|after_or_equal:today',
            'deliveryTimeFrom' => 'required',
            'deliveryTimeTo' => 'required|after:deliveryTimeFrom',
            'deliveryAddress' => 'required|string|max:255',
            'deliveryPostalCode' => 'required|string|max:10',
            'deliveryCity' => 'required|string|max:100',
            'clientName' => 'required|string|max:255',
            'clientPhone' => 'nullable|string|max:20',
            'clientEmail' => 'nullable|email|max:255',
            'contactPerson' => 'nullable|string|max:255',
            'contactPhone' => 'nullable|string|max:20',
            'selectedItems' => 'required|array|min:1',
            'selectedItems.*' => 'exists:production_order_items,id',
        ];

        $messages = [
            'deliveryDate.required' => 'Data dostawy jest wymagana.',
            'deliveryDate.after_or_equal' => 'Data dostawy nie może być wcześniejsza niż dzisiaj.',
            'deliveryTimeFrom.required' => 'Godzina rozpoczęcia dostawy jest wymagana.',
            'deliveryTimeTo.required' => 'Godzina zakończenia dostawy jest wymagana.',
            'deliveryTimeTo.after' => 'Godzina zakończenia musi być późniejsza niż rozpoczęcia.',
            'deliveryAddress.required' => 'Adres dostawy jest wymagany.',
            'deliveryPostalCode.required' => 'Kod pocztowy jest wymagany.',
            'deliveryCity.required' => 'Miasto jest wymagane.',
            'clientName.required' => 'Nazwa klienta jest wymagana.',
            'clientEmail.email' => 'Podaj prawidłowy adres email.',
            'selectedItems.required' => 'Wybierz przynajmniej jedną pozycję do dostawy.',
            'selectedItems.min' => 'Wybierz przynajmniej jedną pozycję do dostawy.',
        ];

        // Dla zleceń nie-B2B wymagaj kontrahenta
        if (!($this->order->b2bOrder && $this->order->b2bOrder->client)) {
            $rules['contractorId'] = 'required|exists:contractors,id';
            $messages['contractorId.required'] = 'Wybierz kontrahenta.';
            $messages['contractorId.exists'] = 'Wybrany kontrahent nie istnieje.';
        }

        $this->validate($rules, $messages);

        // Utwórz dostawę
        $delivery = Delivery::create([
            'numer_dostawy' => $this->generateDeliveryNumber(),
            'production_order_id' => $this->order->id,
            'contractor_id' => $this->contractorId ?: null,
            'data_dostawy' => $this->deliveryDate,
            'godzina_od' => $this->deliveryTimeFrom,
            'godzina_do' => $this->deliveryTimeTo,
            'adres_dostawy' => $this->deliveryAddress,
            'kod_pocztowy' => $this->deliveryPostalCode,
            'miasto' => $this->deliveryCity,
            'klient_nazwa' => $this->clientName,
            'klient_telefon' => $this->clientPhone,
            'klient_email' => $this->clientEmail,
            'osoba_kontaktowa' => $this->contactPerson,
            'telefon_kontaktowy' => $this->contactPhone,
            'uwagi' => $this->deliveryNotes,
            'status' => 'oczekujaca',
            'typ_dostawy' => 'standardowa',
        ]);

        // Dodaj pozycje do dostawy
        foreach ($this->selectedItems as $itemId) {
            $item = ProductionOrderItem::find($itemId);
            if ($item && $item->ilosc_wyprodukowana > 0) {
                $delivery->items()->create([
                    'product_id' => $item->product_id,
                    'production_order_item_id' => $item->id,
                    'nazwa_produktu' => $item->product->nazwa,
                    'ilosc' => $item->ilosc_wyprodukowana,
                    'jednostka' => $item->jednostka,
                    'uwagi' => $item->uwagi,
                ]);
            }
        }

        $this->order->refresh();

        // Zakończ zlecenie jeśli użytkownik wybrał tę opcję lub automatycznie jeśli wszystkie pozycje są gotowe
        if ($this->completeOrderAfterDelivery && $this->order->canBeCompleted()) {
            $this->order->completeProduction();
            session()->flash('success', 'Dostawa została utworzona i zlecenie zostało zakończone.');
        } else {
            // Sprawdź czy można automatycznie zakończyć zlecenie
            $this->checkAndCompleteOrderIfReady();
            session()->flash('success', 'Dostawa została utworzona pomyślnie.');
        }

        $this->closeDeliveryModal();
    }

    private function generateDeliveryNumber(): string
    {
        $date = now()->format('Ymd');
        $lastDelivery = Delivery::whereDate('created_at', now())
                              ->orderBy('id', 'desc')
                              ->first();

        $sequence = $lastDelivery ? (int) substr($lastDelivery->numer_dostawy, -3) + 1 : 1;

        return 'DOS-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    private function prefillDeliveryData()
    {
        // Jeśli zlecenie pochodzi z B2B, wypełnij dane klienta B2B
        if ($this->order->b2bOrder && $this->order->b2bOrder->client) {
            $b2bOrder = $this->order->b2bOrder;
            $client = $b2bOrder->client;

            // Data dostawy z zamówienia B2B lub domyślna
            if ($b2bOrder->delivery_date) {
                $this->deliveryDate = $b2bOrder->delivery_date->format('Y-m-d');
            }

            // Godziny dostawy z zamówienia B2B
            if ($b2bOrder->delivery_time_from) {
                $this->deliveryTimeFrom = $b2bOrder->delivery_time_from->format('H:i');
            }
            if ($b2bOrder->delivery_time_to) {
                $this->deliveryTimeTo = $b2bOrder->delivery_time_to->format('H:i');
            }

            // Adres dostawy z zamówienia B2B
            if ($b2bOrder->delivery_address) {
                $this->deliveryAddress = $b2bOrder->delivery_address;
                $this->deliveryPostalCode = $b2bOrder->delivery_postal_code ?? '';
                $this->deliveryCity = $b2bOrder->delivery_city ?? '';
            } else {
                // Fallback na adres główny klienta
                $this->deliveryAddress = $client->address ?? '';
                $this->deliveryPostalCode = $client->postal_code ?? '';
                $this->deliveryCity = $client->city ?? '';
            }

            // Dane kontaktowe klienta B2B
            $this->clientName = $client->company_name ?? '';
            $this->clientPhone = $client->phone ?? '';
            $this->clientEmail = $client->email ?? '';
            $this->contactPerson = $client->contact_person ?? '';
            $this->contactPhone = $client->contact_phone ?? $client->phone ?? '';

            // Uwagi z zamówienia B2B
            if ($b2bOrder->delivery_notes) {
                $this->deliveryNotes = $b2bOrder->delivery_notes;
            }

            // Brak kontrahenta dla B2B - dostawa bezpośrednia do klienta
            $this->contractorId = '';

        }
        // Jeśli zlecenie ma przypisanego kontrahenta, wypełnij jego dane
        elseif ($this->order->contractor) {
            $contractor = $this->order->contractor;

            // Ustaw kontrahenta
            $this->contractorId = $contractor->id;

            // Adres kontrahenta
            $this->deliveryAddress = $contractor->adres ?? '';
            $this->deliveryPostalCode = $contractor->kod_pocztowy ?? '';
            $this->deliveryCity = $contractor->miasto ?? '';

            // Dane kontaktowe kontrahenta
            $this->clientName = $contractor->nazwa ?? '';
            $this->clientPhone = $contractor->telefon ?? '';
            $this->clientEmail = $contractor->email ?? '';
            $this->contactPerson = $contractor->osoba_kontaktowa ?? '';
            $this->contactPhone = $contractor->telefon_kontaktowy ?? $contractor->telefon ?? '';

            // Data z zlecenia produkcyjnego
            if ($this->order->data_produkcji) {
                $this->deliveryDate = Carbon::parse($this->order->data_produkcji)->format('Y-m-d');
            }
        }
        // Standardowe zlecenie - użyj danych ze zlecenia
        else {
            // Data z zlecenia produkcyjnego
            if ($this->order->data_produkcji) {
                $this->deliveryDate = Carbon::parse($this->order->data_produkcji)->format('Y-m-d');
            }

            // Jeśli jest klient w polu tekstowym
            if ($this->order->klient) {
                $this->clientName = $this->order->klient;
                $this->deliveryNotes = 'Standardowe zlecenie dla: ' . $this->order->klient;
            }
        }
    }

    private function checkAndCompleteOrderIfReady()
    {
        // Sprawdź czy wszystkie pozycje są zakończone lub w dostawie
        $allItemsCompleted = $this->order->items->every(function ($item) {
            // Pozycja jest "gotowa" jeśli:
            // 1. Jest zakończona (status = 'zakonczona')
            // 2. Lub ma wyprodukowaną ilość równą zamówionej
            // 3. Lub jest już w dostawie
            return $item->status === 'zakonczona'
                || $item->ilosc_wyprodukowana >= $item->ilosc
                || $this->isItemInDelivery($item);
        });

        // Jeśli wszystkie pozycje są gotowe i zlecenie może być zakończone
        if ($allItemsCompleted && $this->order->canBeCompleted()) {
            $this->order->completeProduction();
            session()->flash('info', 'Zlecenie zostało automatycznie zakończone - wszystkie pozycje są gotowe lub w dostawie.');
        }
    }

    private function isItemInDelivery($item): bool
    {
        // Sprawdź czy pozycja jest już w jakiejś dostawie
        return $this->order->deliveries()
            ->whereHas('items', function ($query) use ($item) {
                $query->where('production_order_item_id', $item->id);
            })
            ->exists();
    }

    public function render()
    {
        $contractors = Contractor::active()->orderBy('nazwa')->get();

        return view('livewire.production.production-order-view', [
            'contractors' => $contractors,
        ]);
    }
}
