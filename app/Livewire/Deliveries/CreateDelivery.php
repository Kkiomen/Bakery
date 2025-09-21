<?php

namespace App\Livewire\Deliveries;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\ProductionOrder;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CreateDelivery extends Component
{
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

        // Debug - usuń po naprawie
        \Log::info('Available production orders loaded: ' . $this->availableProductionOrders->count());
    }

    public function loadDrivers()
    {
        // Dla uproszczenia, pobieramy wszystkich użytkowników jako potencjalnych kierowców
        $this->drivers = User::orderBy('name')->get();
    }

    public function updatedProductionOrderId($value)
    {
        if ($value) {
            $this->selectedProductionOrder = ProductionOrder::with('items.product')
                ->findOrFail($value);

            // Automatycznie wypełnij dane klienta jeśli są dostępne
            if ($this->selectedProductionOrder->klient) {
                $this->klient_nazwa = $this->selectedProductionOrder->klient;
            }

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
        $this->validate();

        try {
            // Utwórz dostawę
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

            $this->dispatch('delivery-created', [
                'message' => 'Dostawa została utworzona pomyślnie.',
                'deliveryId' => $delivery->id
            ]);

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
