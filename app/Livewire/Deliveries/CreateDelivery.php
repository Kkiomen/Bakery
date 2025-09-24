<?php

namespace App\Livewire\Deliveries;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\ProductionOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CreateDelivery extends Component
{
    protected $listeners = ['editDelivery'];
    public $productionOrderId;
    public $selectedProductionOrder;

    // Dane dostawy
    public $data_dostawy;
    public $godzina_planowana;
    public $priorytet = 'normalny';
    public $klient_nazwa = '';
    public $klient_adres = '';
    public $klient_telefon = '';
    public $klient_email = '';
    public $osoba_kontaktowa = '';
    public $telefon_kontaktowy = '';
    public $kod_pocztowy = '';
    public $miasto = '';
    public $uwagi_dostawy = '';
    public $driver_id = '';
    public $kolejnosc_dostawy = 0;

    // Pozycje dostawy
    public $selectedItems = [];
    public $customItems = [];

    // Pomocnicze
    public $availableProductionOrders = [];
    public $drivers = [];
    public $showLocationSearch = false;
    public $locationSearchResults = [];

    // Editing
    public $isEditing = false;
    public $editingDeliveryId = null;

    protected function rules()
    {
        return [
            'productionOrderId' => 'required|exists:production_orders,id',
            'data_dostawy' => 'required|date|after_or_equal:today',
            'godzina_planowana' => 'nullable|date_format:H:i',
            'priorytet' => 'required|in:niski,normalny,wysoki,pilny',
            'klient_nazwa' => 'required|string|max:255',
            'klient_adres' => 'required|string|max:500',
            'klient_telefon' => 'nullable|string|max:20',
            'klient_email' => 'nullable|email|max:255',
            'osoba_kontaktowa' => 'nullable|string|max:255',
            'telefon_kontaktowy' => 'nullable|string|max:20',
            'kod_pocztowy' => 'nullable|string|max:10',
            'miasto' => 'nullable|string|max:100',
            'uwagi_dostawy' => 'nullable|string',
            'driver_id' => 'nullable|exists:users,id',
            'kolejnosc_dostawy' => 'integer|min:0',
            'selectedItems' => 'required|array|min:1',
            'selectedItems.*' => 'required|array',
            'selectedItems.*.item_id' => 'required|exists:production_order_items,id',
            'selectedItems.*.ilosc' => 'required|integer|min:1',
            'customItems' => 'array',
            'customItems.*.product_id' => 'required_with:customItems.*|exists:products,id',
            'customItems.*.nazwa_produktu' => 'required_with:customItems.*|string|max:255',
            'customItems.*.ilosc' => 'required_with:customItems.*|integer|min:1',
            'customItems.*.jednostka' => 'required_with:customItems.*|string|max:20',
            'customItems.*.waga_kg' => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'productionOrderId.required' => 'Wybór zlecenia produkcyjnego jest wymagany.',
        'data_dostawy.required' => 'Data dostawy jest wymagana.',
        'data_dostawy.after_or_equal' => 'Data dostawy nie może być wcześniejsza niż dzisiaj.',
        'klient_nazwa.required' => 'Nazwa klienta jest wymagana.',
        'klient_adres.required' => 'Adres klienta jest wymagany.',
        'selectedItems.required' => 'Wybór pozycji do dostawy jest wymagany.',
        'selectedItems.min' => 'Musisz wybrać przynajmniej jedną pozycję do dostawy.',
    ];

    public function mount()
    {
        $this->data_dostawy = now()->format('Y-m-d');
        $this->loadAvailableProductionOrders();
        $this->loadDrivers();
    }

    public function editDelivery($deliveryId)
    {
        $delivery = Delivery::with(['productionOrder', 'items.product'])->findOrFail($deliveryId);

        $this->isEditing = true;
        $this->editingDeliveryId = $deliveryId;

        // Fill form with delivery data
        $this->productionOrderId = $delivery->production_order_id;
        $this->selectedProductionOrder = $delivery->productionOrder;
        $this->data_dostawy = $delivery->data_dostawy->format('Y-m-d');
        $this->godzina_planowana = $delivery->godzina_planowana;
        $this->priorytet = $delivery->priorytet;
        $this->klient_nazwa = $delivery->klient_nazwa;
        $this->klient_adres = $delivery->klient_adres;
        $this->klient_telefon = $delivery->klient_telefon;
        $this->klient_email = $delivery->klient_email;
        $this->osoba_kontaktowa = $delivery->osoba_kontaktowa;
        $this->telefon_kontaktowy = $delivery->telefon_kontaktowy;
        $this->kod_pocztowy = $delivery->kod_pocztowy;
        $this->miasto = $delivery->miasto;
        $this->uwagi_dostawy = $delivery->uwagi_dostawy;
        $this->driver_id = $delivery->driver_id;
        $this->kolejnosc_dostawy = $delivery->kolejnosc_dostawy;

        // Load delivery items
        $this->selectedItems = [];
        foreach ($delivery->items as $item) {
            $this->selectedItems[] = [
                'id' => $item->product_id,
                'product_id' => $item->product_id,
                'nazwa' => $item->product->nazwa,
                'ilosc_dostepna' => $item->ilosc,
                'ilosc_wybrana' => $item->ilosc,
                'selected' => true
            ];
        }
    }

    public function render()
    {
        return view('livewire.deliveries.create-delivery');
    }

    public function loadAvailableProductionOrders()
    {
        $this->availableProductionOrders = ProductionOrder::byStatus('zakonczone')
            ->whereDoesntHave('deliveries')
            ->orWhereHas('deliveries', function($query) {
                $query->whereIn('status', ['anulowana']);
            })
            ->with('items.product')
            ->orderBy('data_produkcji', 'desc')
            ->get();

    }

    public function loadDrivers()
    {
        // Dla uproszczenia, pobieramy wszystkich użytkowników jako potencjalnych kierowców
        $this->drivers = User::orderBy('name')->get();
    }

    public function updatedProductionOrderId($value)
    {
        if ($value) {
            $this->selectedProductionOrder = ProductionOrder::with(['items.product', 'contractor', 'b2bOrder.client'])
                ->findOrFail($value);

            // Inteligentnie wypełnij dane na podstawie źródła zlecenia
            $this->prefillDeliveryData();

            // Resetuj wybrane pozycje
            $this->selectedItems = [];

            // Ustaw kolejność dostawy
            $this->kolejnosc_dostawy = Delivery::forDate($this->data_dostawy)->max('kolejnosc_dostawy') + 1;
        }
    }

    public function updatedDataDostawy($value)
    {
        if ($value) {
            $this->kolejnosc_dostawy = Delivery::forDate($value)->max('kolejnosc_dostawy') + 1;
        }
    }

    public function toggleItemSelection($itemId)
    {
        $item = $this->selectedProductionOrder->items->find($itemId);

        if (isset($this->selectedItems[$itemId])) {
            unset($this->selectedItems[$itemId]);
        } else {
            $this->selectedItems[$itemId] = [
                'item_id' => $itemId,
                'ilosc' => $item->ilosc_wyprodukowana,
                'max_ilosc' => $item->ilosc_wyprodukowana,
                'nazwa_produktu' => $item->product->nazwa,
                'jednostka' => $item->jednostka,
            ];
        }
    }

    public function selectAllItems()
    {
        foreach ($this->selectedProductionOrder->items as $item) {
            if ($item->ilosc_wyprodukowana > 0) {
                $this->selectedItems[$item->id] = [
                    'item_id' => $item->id,
                    'ilosc' => $item->ilosc_wyprodukowana,
                    'max_ilosc' => $item->ilosc_wyprodukowana,
                    'nazwa_produktu' => $item->product->nazwa,
                    'jednostka' => $item->jednostka,
                ];
            }
        }
    }

    public function deselectAllItems()
    {
        $this->selectedItems = [];
    }

    private function prefillDeliveryData()
    {
        if (!$this->selectedProductionOrder) {
            return;
        }

        $order = $this->selectedProductionOrder;

        // Jeśli zlecenie pochodzi z B2B, wypełnij dane klienta B2B
        if ($order->b2bOrder && $order->b2bOrder->client) {
            $b2bOrder = $order->b2bOrder;
            $client = $b2bOrder->client;

            // Data dostawy z zamówienia B2B lub domyślna
            if ($b2bOrder->delivery_date) {
                $this->data_dostawy = Carbon::parse($b2bOrder->delivery_date)->format('Y-m-d');
            }

            // Godziny dostawy z zamówienia B2B
            if ($b2bOrder->delivery_time_from) {
                $this->godzina_planowana = Carbon::parse($b2bOrder->delivery_time_from)->format('H:i');
            }

            // Adres dostawy z zamówienia B2B
            if ($b2bOrder->delivery_address) {
                $this->klient_adres = $b2bOrder->delivery_address;
                $this->kod_pocztowy = $b2bOrder->delivery_postal_code ?? '';
                $this->miasto = $b2bOrder->delivery_city ?? '';
            } else {
                // Fallback na adres główny klienta
                $this->klient_adres = $client->address ?? '';
                $this->kod_pocztowy = $client->postal_code ?? '';
                $this->miasto = $client->city ?? '';
            }

            // Dane kontaktowe klienta B2B
            $this->klient_nazwa = $client->company_name ?? '';
            $this->klient_telefon = $client->phone ?? '';
            $this->klient_email = $client->email ?? '';
            $this->osoba_kontaktowa = $client->contact_person ?? '';
            $this->telefon_kontaktowy = $client->contact_phone ?? $client->phone ?? '';

            // Uwagi z zamówienia B2B
            if ($b2bOrder->delivery_notes) {
                $this->uwagi_dostawy = $b2bOrder->delivery_notes;
            }
        }
        // Jeśli zlecenie ma przypisanego kontrahenta, wypełnij jego dane
        elseif ($order->contractor) {
            $contractor = $order->contractor;

            // Adres kontrahenta
            $this->klient_adres = $contractor->adres ?? '';
            $this->kod_pocztowy = $contractor->kod_pocztowy ?? '';
            $this->miasto = $contractor->miasto ?? '';

            // Dane kontaktowe kontrahenta
            $this->klient_nazwa = $contractor->nazwa ?? '';
            $this->klient_telefon = $contractor->telefon ?? '';
            $this->klient_email = $contractor->email ?? '';
            $this->osoba_kontaktowa = $contractor->osoba_kontaktowa ?? '';
            $this->telefon_kontaktowy = $contractor->telefon_kontaktowy ?? $contractor->telefon ?? '';

            // Data z zlecenia produkcyjnego
            if ($order->data_produkcji) {
                $this->data_dostawy = Carbon::parse($order->data_produkcji)->format('Y-m-d');
            }
        }
        // Standardowe zlecenie - użyj danych ze zlecenia
        else {
            // Data z zlecenia produkcyjnego
            if ($order->data_produkcji) {
                $this->data_dostawy = Carbon::parse($order->data_produkcji)->format('Y-m-d');
            }

            // Jeśli jest klient w polu tekstowym
            if ($order->klient) {
                $this->klient_nazwa = $order->klient;
                $this->uwagi_dostawy = 'Standardowe zlecenie dla: ' . $order->klient;
            }
        }
    }

    public function addCustomItem()
    {
        $this->customItems[] = [
            'product_id' => '',
            'nazwa_produktu' => '',
            'ilosc' => 1,
            'jednostka' => 'szt',
            'waga_kg' => null,
        ];
    }

    public function removeCustomItem($index)
    {
        unset($this->customItems[$index]);
        $this->customItems = array_values($this->customItems);
    }

    public function searchLocation()
    {
        if (strlen($this->klient_adres) > 3) {
            // TODO: Implementacja wyszukiwania adresów z API (np. Nominatim)
            $this->showLocationSearch = true;
            $this->locationSearchResults = [
                // Przykładowe wyniki - w rzeczywistości z API
                [
                    'display_name' => $this->klient_adres . ', Warszawa',
                    'lat' => 52.2297,
                    'lon' => 21.0122,
                ],
            ];
        }
    }

    public function selectLocation($lat, $lon, $address)
    {
        $this->klient_adres = $address;
        $this->showLocationSearch = false;
        $this->locationSearchResults = [];

        // TODO: Wyodrębnij kod pocztowy i miasto z adresu
        if (preg_match('/(\d{2}-\d{3})/', $address, $matches)) {
            $this->kod_pocztowy = $matches[1];
        }

        if (preg_match('/([^,]+)$/', $address, $matches)) {
            $this->miasto = trim($matches[1]);
        }
    }

    public function createDelivery()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed - collect all error messages
            $errorMessages = [];
            foreach ($e->validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }

            $this->dispatch('delivery-creation-failed', [
                'message' => 'Formularz zawiera błędy: ' . implode(', ', $errorMessages)
            ]);
            return;
        }

        try {
            if ($this->isEditing) {
                // Aktualizuj istniejącą dostawę
                $delivery = Delivery::findOrFail($this->editingDeliveryId);
                $delivery->update([
                    'production_order_id' => $this->productionOrderId,
                    'driver_id' => $this->driver_id ?: null,
                    'status' => $this->driver_id ? 'przypisana' : 'oczekujaca',
                    'priorytet' => $this->priorytet,
                    'data_dostawy' => $this->data_dostawy,
                    'godzina_planowana' => $this->godzina_planowana ?
                        $this->data_dostawy . ' ' . $this->godzina_planowana : null,
                    'klient_nazwa' => $this->klient_nazwa,
                    'klient_adres' => $this->klient_adres,
                    'klient_telefon' => $this->klient_telefon,
                    'klient_email' => $this->klient_email,
                    'osoba_kontaktowa' => $this->osoba_kontaktowa,
                    'telefon_kontaktowy' => $this->telefon_kontaktowy,
                    'kod_pocztowy' => $this->kod_pocztowy,
                    'miasto' => $this->miasto,
                    'uwagi_dostawy' => $this->uwagi_dostawy,
                    'kolejnosc_dostawy' => $this->kolejnosc_dostawy,
                ]);

                // Usuń stare pozycje dostawy
                $delivery->items()->delete();
            } else {
                // Utwórz nową dostawę
                $delivery = Delivery::create([
                'production_order_id' => $this->productionOrderId,
                'driver_id' => $this->driver_id ?: null,
                'status' => $this->driver_id ? 'przypisana' : 'oczekujaca',
                'priorytet' => $this->priorytet,
                'data_dostawy' => $this->data_dostawy,
                'godzina_planowana' => $this->godzina_planowana ?
                    $this->data_dostawy . ' ' . $this->godzina_planowana : null,
                'klient_nazwa' => $this->klient_nazwa,
                'klient_adres' => $this->klient_adres,
                'klient_telefon' => $this->klient_telefon,
                'klient_email' => $this->klient_email,
                'osoba_kontaktowa' => $this->osoba_kontaktowa,
                'telefon_kontaktowy' => $this->telefon_kontaktowy,
                'kod_pocztowy' => $this->kod_pocztowy,
                'miasto' => $this->miasto,
                'uwagi_dostawy' => $this->uwagi_dostawy,
                'kolejnosc_dostawy' => $this->kolejnosc_dostawy,
            ]);

            // Utwórz pozycje dostawy z wybranych pozycji zlecenia
            foreach ($this->selectedItems as $selectedItem) {
                $productionOrderItem = $this->selectedProductionOrder->items->find($selectedItem['item_id']);

                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $productionOrderItem->product_id,
                    'production_order_item_id' => $productionOrderItem->id,
                    'nazwa_produktu' => $productionOrderItem->product->nazwa,
                    'ilosc' => $selectedItem['ilosc'],
                    'jednostka' => $productionOrderItem->jednostka,
                    'waga_kg' => $productionOrderItem->product->waga_g ?
                        ($productionOrderItem->product->waga_g * $selectedItem['ilosc']) / 1000 : null,
                    'status' => 'przygotowany',
                ]);
            }

            // Utwórz dodatkowe pozycje
            foreach ($this->customItems as $customItem) {
                if (!empty($customItem['nazwa_produktu'])) {
                    DeliveryItem::create([
                        'delivery_id' => $delivery->id,
                        'product_id' => $customItem['product_id'] ?: null,
                        'nazwa_produktu' => $customItem['nazwa_produktu'],
                        'ilosc' => $customItem['ilosc'],
                        'jednostka' => $customItem['jednostka'],
                        'waga_kg' => $customItem['waga_kg'],
                        'status' => 'przygotowany',
                    ]);
                }
            }

            if ($this->isEditing) {
                $this->dispatch('delivery-created', [
                    'message' => 'Dostawa została zaktualizowana pomyślnie. Numer dostawy: ' . $delivery->numer_dostawy,
                    'deliveryId' => $delivery->id
                ]);
            } else {
                $this->dispatch('delivery-created', [
                    'message' => 'Dostawa została utworzona pomyślnie. Numer dostawy: ' . ($delivery->numer_dostawy ?? 'Wygenerowany automatycznie'),
                    'deliveryId' => $delivery->id
                ]);
            }

            // Reset form only after successful creation/update
            $this->reset();
            $this->mount();

        } catch (\Exception $e) {
            $this->dispatch('delivery-creation-failed', [
                'message' => 'Wystąpił błąd podczas tworzenia dostawy: ' . $e->getMessage()
            ]);
        }
    }

    public function cancel()
    {
        $this->dispatch('delivery-creation-cancelled');
    }

    public function loadFromProductionOrder($orderId)
    {
        $this->productionOrderId = $orderId;
        $this->updatedProductionOrderId($orderId);
    }
}
