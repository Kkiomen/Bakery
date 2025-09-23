<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;
    public bool $isEditing = false;

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

    // Zdjęcia
    public $photos = [];
    public $existingImages = [];
    public $imagesToDelete = [];

    protected $rules = [
        'sku' => 'required|string|max:255',
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
        'photos.*' => 'nullable|image|max:5120', // max 5MB
    ];

    public function mount(?Product $product = null)
    {
        if ($product && $product->exists) {
            $this->product = $product;
            $this->isEditing = true;
            $this->fill($product->toArray());

            // Wypełnij wartości odżywcze
            if ($product->wartosci_odzywcze) {
                $this->kcal = $product->wartosci_odzywcze['kcal'] ?? '';
                $this->bialko_g = $product->wartosci_odzywcze['bialko_g'] ?? '';
                $this->tluszcz_g = $product->wartosci_odzywcze['tluszcz_g'] ?? '';
                $this->wegle_g = $product->wartosci_odzywcze['wegle_g'] ?? '';
            }

            $this->alergeny = $product->alergeny ?? [];
            $this->existingImages = $product->images;
            $this->updateCalculatedFields();
        } else {
            // Nowy produkt - ustaw domyślne wartości
            $this->product = new Product();
            $this->isEditing = false;
            $this->alergeny = [];
            $this->existingImages = collect();
        }
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

    public function removePhoto($index)
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function markImageForDeletion($imageId)
    {
        if (!in_array($imageId, $this->imagesToDelete)) {
            $this->imagesToDelete[] = $imageId;
        }
    }

    public function unmarkImageForDeletion($imageId)
    {
        $this->imagesToDelete = array_filter($this->imagesToDelete, function($id) use ($imageId) {
            return $id !== $imageId;
        });
    }

    public function setPrimaryImage($imageId)
    {
        // Usuń primary z wszystkich istniejących zdjęć
        ProductImage::where('product_id', $this->product->id)
            ->update(['is_primary' => false]);

        // Ustaw nowe jako primary
        ProductImage::where('id', $imageId)
            ->update(['is_primary' => true]);

        // Odśwież dane
        $this->existingImages = $this->product->fresh()->images;

        session()->flash('success', 'Zdjęcie główne zostało zmienione.');
    }

    public function save()
    {

        // Przygotuj dane do walidacji
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

        // Walidacja
        $this->validate();

        try {
            if ($this->isEditing) {
                $this->product->update($data);
                $message = 'Produkt został zaktualizowany';
            } else {
                $this->product = Product::create($data);
                $message = 'Produkt został utworzony';
            }

            // Obsługa zdjęć
            $this->handleImages();

            $this->dispatch('product-saved', [
                'message' => $message,
                'product' => $this->product->id
            ]);

            if (!$this->isEditing) {
                $this->reset();
            }

        } catch (\Exception $e) {
            $this->dispatch('product-error', 'Wystąpił błąd podczas zapisywania produktu: ' . $e->getMessage());
        }
    }

    private function handleImages()
    {

        // Usuń oznaczone zdjęcia
        if (!empty($this->imagesToDelete)) {
            $imagesToDelete = ProductImage::whereIn('id', $this->imagesToDelete)->get();

            foreach ($imagesToDelete as $image) {
                // Usuń plik z dysku
                Storage::disk('public')->delete('products/' . $image->filename);
                // Usuń z bazy danych
                $image->delete();
            }
        }

        // Dodaj nowe zdjęcia
        if (!empty($this->photos) && $this->product && $this->product->id) {
            $sortOrder = ProductImage::where('product_id', $this->product->id)->max('sort_order') ?? 0;
            $isFirstImage = ProductImage::where('product_id', $this->product->id)->count() === 0;


            foreach ($this->photos as $index => $photo) {
                try {
                    $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    $storedPath = $photo->storeAs('products', $filename, 'public');

                    if ($storedPath) {
                        $imageData = [
                            'product_id' => $this->product->id,
                            'filename' => $filename,
                            'original_name' => $photo->getClientOriginalName(),
                            'mime_type' => $photo->getMimeType(),
                            'size' => $photo->getSize(),
                            'alt_text' => $this->nazwa,
                            'sort_order' => $sortOrder + $index + 1,
                            'is_primary' => $isFirstImage && $index === 0,
                        ];

                        ProductImage::create($imageData);
                    }
                } catch (\Exception $e) {
                    Log::error('Błąd podczas zapisywania zdjęcia produktu: ' . $e->getMessage());
                }
            }
        }

        // Odśwież dane
        if ($this->isEditing) {
            $this->existingImages = $this->product->fresh()->images;
        }

        // Wyczyść tymczasowe dane
        $this->photos = [];
        $this->imagesToDelete = [];
    }

    public function cancel()
    {
        $this->dispatch('product-cancelled');
    }

    public function render()
    {
        $categories = Category::where('aktywny', true)->orderBy('nazwa')->get();

        return view('livewire.products.product-form', [
            'categories' => $categories,
        ]);
    }
}
