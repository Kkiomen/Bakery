<?php

namespace App\Livewire\Production;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;

class ProductionOrderForm extends Component
{
    public ?ProductionOrder $order = null;
    public bool $isEdit = false;

    // Dane zlecenia
    public $nazwa = '';
    public $opis = '';
    public $data_produkcji = '';
    public $user_id = '';
    public $priorytet = 'normalny';
    public $typ_zlecenia = 'wewnetrzne';
    public $klient = '';
    public $uwagi = '';

    // Pozycje zlecenia
    public $items = [];
    public $newItem = [
        'product_id' => '',
        'ilosc' => '',
        'jednostka' => 'szt',
        'uwagi' => '',
    ];

    // Pomocnicze
    public $products = [];
    public $users = [];
    public $showProductSearch = false;
    public $productSearch = '';

    protected function rules()
    {
        return [
            'nazwa' => 'required|string|max:255',
            'opis' => 'nullable|string',
            'data_produkcji' => 'required|date|after_or_equal:today',
            'user_id' => 'required|exists:users,id',
            'priorytet' => 'required|in:niski,normalny,wysoki,pilny',
            'typ_zlecenia' => 'required|in:wewnetrzne,sklep,b2b,hotel,inne',
            'klient' => 'nullable|string|max:255',
            'uwagi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.ilosc' => 'required|integer|min:1',
            'items.*.jednostka' => 'required|string|max:10',
            'items.*.uwagi' => 'nullable|string',
        ];
    }

    protected $messages = [
        'nazwa.required' => 'Nazwa zlecenia jest wymagana.',
        'data_produkcji.required' => 'Data produkcji jest wymagana.',
        'data_produkcji.after_or_equal' => 'Data produkcji nie może być z przeszłości.',
        'user_id.required' => 'Wybierz użytkownika odpowiedzialnego.',
        'items.required' => 'Dodaj przynajmniej jedną pozycję do zlecenia.',
        'items.min' => 'Dodaj przynajmniej jedną pozycję do zlecenia.',
        'items.*.product_id.required' => 'Wybierz produkt.',
        'items.*.ilosc.required' => 'Podaj ilość.',
        'items.*.ilosc.min' => 'Ilość musi być większa od 0.',
    ];

    public function mount(?ProductionOrder $order = null)
    {
        $this->users = User::orderBy('name')->get();
        $this->products = Product::active()->orderBy('nazwa')->get();

        if ($order && $order->exists) {
            $this->order = $order;
            $this->isEdit = true;
            $this->fill([
                'nazwa' => $order->nazwa,
                'opis' => $order->opis,
                'data_produkcji' => $order->data_produkcji->toDateString(),
                'user_id' => $order->user_id,
                'priorytet' => $order->priorytet,
                'typ_zlecenia' => $order->typ_zlecenia,
                'klient' => $order->klient,
                'uwagi' => $order->uwagi,
            ]);

            $this->items = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'ilosc' => $item->ilosc,
                    'jednostka' => $item->jednostka,
                    'uwagi' => $item->uwagi ?? '',
                ];
            })->toArray();
        } else {
            $this->user_id = auth()->check() ? auth()->user()->id : 1;
            $this->data_produkcji = now()->addDay()->toDateString();
        }
    }

    public function updatedProductSearch()
    {
        if (strlen($this->productSearch) >= 2) {
            $this->products = Product::active()
                ->search($this->productSearch)
                ->orderBy('nazwa')
                ->limit(20)
                ->get();
        } else {
            $this->products = Product::active()->orderBy('nazwa')->limit(20)->get();
        }
    }

    public function addItem()
    {
        $this->validate([
            'newItem.product_id' => 'required|exists:products,id',
            'newItem.ilosc' => 'required|integer|min:1',
            'newItem.jednostka' => 'required|string|max:10',
        ], [
            'newItem.product_id.required' => 'Wybierz produkt.',
            'newItem.ilosc.required' => 'Podaj ilość.',
            'newItem.ilosc.min' => 'Ilość musi być większa od 0.',
        ]);

        // Sprawdź czy produkt już nie jest na liście
        $exists = collect($this->items)->contains('product_id', $this->newItem['product_id']);

        if ($exists) {
            $this->addError('newItem.product_id', 'Ten produkt jest już dodany do zlecenia.');
            return;
        }

        $this->items[] = [
            'id' => null,
            'product_id' => $this->newItem['product_id'],
            'ilosc' => $this->newItem['ilosc'],
            'jednostka' => $this->newItem['jednostka'],
            'uwagi' => $this->newItem['uwagi'],
        ];

        $this->newItem = [
            'product_id' => '',
            'ilosc' => '',
            'jednostka' => 'szt',
            'uwagi' => '',
        ];

        $this->showProductSearch = false;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function duplicateFromOrder($orderId)
    {
        $sourceOrder = ProductionOrder::with('items')->findOrFail($orderId);

        $this->fill([
            'nazwa' => $sourceOrder->nazwa . ' (kopia)',
            'opis' => $sourceOrder->opis,
            'data_produkcji' => now()->addDay()->format('Y-m-d'),
            'user_id' => $sourceOrder->user_id,
            'priorytet' => $sourceOrder->priorytet,
            'typ_zlecenia' => $sourceOrder->typ_zlecenia,
            'klient' => $sourceOrder->klient,
            'uwagi' => $sourceOrder->uwagi,
        ]);

        $this->items = $sourceOrder->items->map(function ($item) {
            return [
                'id' => null,
                'product_id' => $item->product_id,
                'ilosc' => $item->ilosc,
                'jednostka' => $item->jednostka,
                'uwagi' => $item->uwagi ?? '',
            ];
        })->toArray();

        session()->flash('info', 'Zlecenie zostało skopiowane. Możesz teraz je zmodyfikować.');
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                $this->order->update([
                    'nazwa' => $this->nazwa,
                    'opis' => $this->opis,
                    'data_produkcji' => $this->data_produkcji,
                    'user_id' => $this->user_id,
                    'priorytet' => $this->priorytet,
                    'typ_zlecenia' => $this->typ_zlecenia,
                    'klient' => $this->klient,
                    'uwagi' => $this->uwagi,
                ]);

                // Aktualizuj pozycje
                $this->updateItems();
            } else {
                $this->order = ProductionOrder::create([
                    'nazwa' => $this->nazwa,
                    'opis' => $this->opis,
                    'data_produkcji' => $this->data_produkcji,
                    'user_id' => $this->user_id,
                    'priorytet' => $this->priorytet,
                    'typ_zlecenia' => $this->typ_zlecenia,
                    'klient' => $this->klient,
                    'uwagi' => $this->uwagi,
                ]);

                // Dodaj pozycje
                $this->createItems();
            }

            session()->flash('success', $this->isEdit ? 'Zlecenie zostało zaktualizowane.' : 'Zlecenie zostało utworzone.');

            return redirect()->route('production.orders.show', $this->order);
        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił błąd podczas zapisywania zlecenia.');
        }
    }

    private function updateItems()
    {
        $existingItemIds = collect($this->items)->pluck('id')->filter()->toArray();

        // Usuń pozycje które zostały usunięte z formularza
        if (!empty($existingItemIds)) {
            $this->order->items()->whereNotIn('id', $existingItemIds)->delete();
        } else {
            $this->order->items()->delete();
        }

        foreach ($this->items as $itemData) {
            if ($itemData['id']) {
                // Aktualizuj istniejącą pozycję
                ProductionOrderItem::where('id', $itemData['id'])->update([
                    'product_id' => $itemData['product_id'],
                    'ilosc' => $itemData['ilosc'],
                    'jednostka' => $itemData['jednostka'],
                    'uwagi' => $itemData['uwagi'],
                ]);
            } else {
                // Dodaj nową pozycję
                $this->order->items()->create([
                    'product_id' => $itemData['product_id'],
                    'ilosc' => $itemData['ilosc'],
                    'jednostka' => $itemData['jednostka'],
                    'uwagi' => $itemData['uwagi'],
                ]);
            }
        }
    }

    private function createItems()
    {
        foreach ($this->items as $itemData) {
            $this->order->items()->create([
                'product_id' => $itemData['product_id'],
                'ilosc' => $itemData['ilosc'],
                'jednostka' => $itemData['jednostka'],
                'uwagi' => $itemData['uwagi'],
            ]);
        }
    }

    public function getProductName($productId)
    {
        $product = collect($this->products)->firstWhere('id', $productId);
        return $product ? $product->nazwa : 'Nieznany produkt';
    }

    public function render()
    {
        return view('livewire.production.production-order-form', [
            'statusOptions' => [
                'niski' => 'Niski',
                'normalny' => 'Normalny',
                'wysoki' => 'Wysoki',
                'pilny' => 'Pilny',
            ],
            'typeOptions' => [
                'wewnetrzne' => 'Wewnętrzne',
                'sklep' => 'Sklep',
                'b2b' => 'B2B',
                'hotel' => 'Hotel',
                'inne' => 'Inne',
            ],
        ]);
    }
}
