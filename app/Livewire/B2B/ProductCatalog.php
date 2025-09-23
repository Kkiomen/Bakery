<?php

namespace App\Livewire\B2B;

use App\Models\Product;
use App\Models\Category;
use App\Models\B2BPricing;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $sortBy = 'nazwa';
    public $sortDirection = 'asc';
    public $cart = [];
    public $showCart = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        // Załaduj koszyk z sesji
        $this->cart = session()->get('b2b_cart', []);
    }

    public function addToCart($productId, $quantity = 1)
    {
        if (!Auth::guard('b2b')->check()) {
            return;
        }

        $product = Product::findOrFail($productId);
        $client = Auth::guard('b2b')->user();

        // Pobierz cenę dla klienta
        $pricing = $client->getPriceForProduct($product, $quantity);

        if (!$pricing) {
            session()->flash('error', 'Brak dostępnych cen dla tego produktu.');
            return;
        }

        // Debug: loguj informacje o cenniku
        Log::info('B2B Pricing for product', [
            'product_id' => $productId,
            'product_name' => $product->nazwa,
            'quantity' => $quantity,
            'client_tier' => $client->pricing_tier,
            'price_net' => $pricing->price_net,
            'discount_percent' => $pricing->discount_percent,
            'pricing_id' => $pricing->id
        ]);

        $cartKey = $productId;

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $productId,
                'product_name' => $product->nazwa,
                'quantity' => $quantity,
                'unit_price' => $pricing->price_net,
                'unit_price_gross' => $pricing->price_gross,
                'tax_rate' => $pricing->tax_rate,
                'discount_percent' => $pricing->discount_percent ?? 0,
            ];
        }

        // Przelicz ceny po dodaniu
        $this->recalculateCartItem($cartKey);

        // Zapisz koszyk w sesji
        session()->put('b2b_cart', $this->cart);

        // Wyślij event do frontend dla animacji
        $this->dispatch('product-added-to-cart', [
            'productName' => $product->nazwa,
            'quantity' => $quantity,
            'cartCount' => $this->getCartItemsCount()
        ]);

        session()->flash('success', 'Produkt został dodany do koszyka.');
    }

    public function updateCartQuantity($cartKey, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($cartKey);
            return;
        }

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] = $quantity;
            $this->recalculateCartItem($cartKey);
            session()->put('b2b_cart', $this->cart);
        }
    }

    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
        session()->put('b2b_cart', $this->cart);
        session()->flash('success', 'Produkt został usunięty z koszyka.');
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('b2b_cart');
        session()->flash('success', 'Koszyk został wyczyszczony.');
    }

    public function toggleCart()
    {
        $this->showCart = !$this->showCart;
    }

    private function recalculateCartItem($cartKey)
    {
        if (!isset($this->cart[$cartKey])) return;

        $item = &$this->cart[$cartKey];
        $quantity = $item['quantity'];

        // Sprawdź czy jest lepsza cena dla większej ilości
        $product = Product::find($item['product_id']);
        $client = Auth::guard('b2b')->user();
        $pricing = $client->getPriceForProduct($product, $quantity);

        if ($pricing) {
            $item['unit_price'] = $pricing->price_net;
            $item['unit_price_gross'] = $pricing->price_gross;
            $item['discount_percent'] = $pricing->discount_percent ?? 0;
        }

        $item['line_total'] = $quantity * $item['unit_price'];
        $item['line_total_gross'] = $quantity * $item['unit_price_gross'];
        $item['tax_amount'] = $item['line_total_gross'] - $item['line_total'];
    }

    public function getProducts()
    {
        $query = Product::with(['category', 'images']);

        // Wyszukiwanie
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nazwa', 'like', '%' . $this->search . '%')
                  ->orWhere('opis', 'like', '%' . $this->search . '%');
            });
        }

        // Filtrowanie po kategorii
        if ($this->categoryFilter) {
            $query->where('kategoria_id', $this->categoryFilter);
        }

        // Sortowanie
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    public function getCategories()
    {
        return Category::orderBy('nazwa')->get();
    }

    public function getCartTotal()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['line_total'] ?? 0;
        }
        return $total;
    }

    public function getCartTotalGross()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['line_total_gross'] ?? 0;
        }
        return $total;
    }

    public function getCartItemsCount()
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.b2-b.product-catalog', [
            'products' => $this->getProducts(),
            'categories' => $this->getCategories(),
        ]);
    }
}
