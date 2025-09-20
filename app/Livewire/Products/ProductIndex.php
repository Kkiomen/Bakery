<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ProductIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $categoryFilter = '';

    #[Url]
    public $activeFilter = '';

    #[Url]
    public $allergenFilter = '';

    #[Url]
    public $allergenMode = 'contains'; // contains or excludes

    #[Url]
    public $minWeight = '';

    #[Url]
    public $maxWeight = '';

    #[Url]
    public $minPrice = '';

    #[Url]
    public $maxPrice = '';

    #[Url]
    public $sortBy = 'nazwa';

    #[Url]
    public $sortDirection = 'asc';

    public $selectedProducts = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'activeFilter' => ['except' => ''],
        'allergenFilter' => ['except' => ''],
        'allergenMode' => ['except' => 'contains'],
        'minWeight' => ['except' => ''],
        'maxWeight' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
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

    public function updatedActiveFilter()
    {
        $this->resetPage();
    }

    public function updatedAllergenFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = $this->getProducts()->pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
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
        $this->reset([
            'search', 'categoryFilter', 'activeFilter', 'allergenFilter',
            'minWeight', 'maxWeight', 'minPrice', 'maxPrice'
        ]);
        $this->resetPage();
    }

    public function toggleProductStatus($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->update(['aktywny' => !$product->aktywny]);
            $this->dispatch('product-updated', 'Status produktu został zmieniony');
        }
    }

    public function bulkToggleStatus()
    {
        if (!empty($this->selectedProducts)) {
            Product::whereIn('id', $this->selectedProducts)
                ->update(['aktywny' => false]);

            $this->selectedProducts = [];
            $this->selectAll = false;
            $this->dispatch('product-updated', 'Status wybranych produktów został zmieniony');
        }
    }

    public function copyToClipboard($sku)
    {
        $this->dispatch('copy-to-clipboard', $sku);
    }

    public function getProducts()
    {
        $query = Product::with(['category', 'images'])
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->byCategory($this->categoryFilter);
            })
            ->when($this->activeFilter !== '', function ($query) {
                $query->where('aktywny', $this->activeFilter === '1');
            })
            ->when($this->allergenFilter && $this->allergenMode === 'contains', function ($query) {
                $query->withAllergens([$this->allergenFilter]);
            })
            ->when($this->allergenFilter && $this->allergenMode === 'excludes', function ($query) {
                $query->withoutAllergens([$this->allergenFilter]);
            })
            ->when($this->minWeight && $this->maxWeight, function ($query) {
                $query->weightRange($this->minWeight, $this->maxWeight);
            })
            ->when($this->minPrice && $this->maxPrice, function ($query) {
                $query->priceRange($this->minPrice, $this->maxPrice);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(20);
    }

    public function render()
    {
        $products = $this->getProducts();
        $categories = Category::where('aktywny', true)->orderBy('nazwa')->get();

        $allergens = [
            'gluten' => 'Gluten',
            'mleko' => 'Mleko',
            'jajka' => 'Jajka',
            'orzechy' => 'Orzechy',
            'soja' => 'Soja',
            'sezam' => 'Sezam'
        ];

        return view('livewire.products.product-index', [
            'products' => $products,
            'categories' => $categories,
            'allergens' => $allergens,
        ]);
    }
}
