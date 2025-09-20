<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;

class ProductCreate extends Component
{
    // Podstawowe dane
    public $sku = '';
    public $ean = '';
    public $nazwa = '';
    public $opis = '';
    public $kategoria_id = '';

    // Waga i jednostki
    public $waga_g = '';
    public $jednostka_sprzedazy = 'szt';
    public $zawartosc_opakowania = '';

    // Alergeny
    public $alergeny = [];
    public $availableAllergens = [
        'gluten' => 'Gluten',
        'mleko' => 'Mleko',
        'jajka' => 'Jajka',
        'orzechy' => 'Orzechy',
        'soja' => 'Soja',
        'sezam' => 'Sezam'
    ];

    // Wartości odżywcze
    public $kcal = '';
    public $bialko_g = '';
    public $tluszcz_g = '';
    public $wegle_g = '';

    // Ceny i VAT
    public $stawka_vat = '23';
    public $cena_netto_gr = '';

    // Status i SEO
    public $aktywny = true;
    public $meta_title = '';
    public $meta_description = '';

    // Wyliczane pola
    public $wagaKgDisplay = '';
    public $cenaNettoDisplay = '';

    protected $rules = [
        'sku' => 'required|string|max:255|unique:products,sku',
        'ean' => 'nullable|string|min:8|max:14',
        'nazwa' => 'required|string|max:255',
        'opis' => 'nullable|string',
        'kategoria_id' => 'required|exists:categories,id',
        'waga_g' => 'required|integer|min:1',
        'jednostka_sprzedazy' => 'required|in:szt,opak,kg',
        'zawartosc_opakowania' => 'nullable|integer|min:1',
        'alergeny' => 'nullable|array',
        'alergeny.*' => 'in:gluten,mleko,jajka,orzechy,soja,sezam',
        'kcal' => 'nullable|numeric|min:0',
        'bialko_g' => 'nullable|numeric|min:0',
        'tluszcz_g' => 'nullable|numeric|min:0',
        'wegle_g' => 'nullable|numeric|min:0',
        'stawka_vat' => 'required|in:0,5,8,23',
        'cena_netto_gr' => 'required|integer|min:1',
        'aktywny' => 'boolean',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'sku.required' => 'SKU jest wymagane.',
        'sku.unique' => 'Ten SKU już istnieje.',
        'nazwa.required' => 'Nazwa produktu jest wymagana.',
        'kategoria_id.required' => 'Kategoria jest wymagana.',
        'kategoria_id.exists' => 'Wybrana kategoria nie istnieje.',
        'waga_g.required' => 'Waga produktu jest wymagana.',
        'waga_g.min' => 'Waga musi być większa od 0.',
        'jednostka_sprzedazy.required' => 'Jednostka sprzedaży jest wymagana.',
        'stawka_vat.required' => 'Stawka VAT jest wymagana.',
        'cena_netto_gr.required' => 'Cena netto jest wymagana.',
        'cena_netto_gr.min' => 'Cena musi być większa od 0.',
    ];

    public function mount()
    {
        // Inicjalizacja dla nowego produktu
        $this->alergeny = [];
        $this->aktywny = true;
    }

    public function updatedWagaG()
    {
        $this->updateCalculatedFields();
    }

    public function updatedCenaNettoGr()
    {
        $this->updateCalculatedFields();
    }

    private function updateCalculatedFields()
    {
        if ($this->waga_g) {
            $this->wagaKgDisplay = number_format($this->waga_g / 1000, 2, ',', '') . ' kg';
        }

        if ($this->cena_netto_gr) {
            $this->cenaNettoDisplay = number_format($this->cena_netto_gr / 100, 2, ',', '') . ' zł';
        }
    }

    public function toggleAllergen($allergen)
    {
        if (in_array($allergen, $this->alergeny)) {
            $this->alergeny = array_values(array_diff($this->alergeny, [$allergen]));
        } else {
            $this->alergeny[] = $allergen;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'sku' => $this->sku,
                'ean' => $this->ean ?: null,
                'nazwa' => $this->nazwa,
                'opis' => $this->opis ?: null,
                'kategoria_id' => $this->kategoria_id,
                'waga_g' => $this->waga_g,
                'jednostka_sprzedazy' => $this->jednostka_sprzedazy,
                'zawartosc_opakowania' => $this->zawartosc_opakowania ?: null,
                'alergeny' => $this->alergeny,
                'wartosci_odzywcze' => [
                    'kcal' => $this->kcal ?: null,
                    'bialko_g' => $this->bialko_g ?: null,
                    'tluszcz_g' => $this->tluszcz_g ?: null,
                    'wegle_g' => $this->wegle_g ?: null,
                ],
                'stawka_vat' => $this->stawka_vat,
                'cena_netto_gr' => $this->cena_netto_gr,
                'aktywny' => $this->aktywny,
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
            ];

            $product = Product::create($data);

            session()->flash('success', 'Produkt został pomyślnie dodany!');
            
            return redirect()->route('products.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Wystąpił błąd podczas zapisywania produktu: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('products.index');
    }

    public function render()
    {
        $categories = Category::where('aktywny', true)->orderBy('nazwa')->get();

        return view('livewire.products.product-create', [
            'categories' => $categories,
        ]);
    }
}
