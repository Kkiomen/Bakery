<?php

namespace App\Livewire\Materials;

use App\Models\Material;
use Livewire\Component;

class MaterialForm extends Component
{
    public ?Material $material = null;
    public bool $isEditing = false;

    // Pola formularza
    public $kod = '';
    public $nazwa = '';
    public $opis = '';
    public $typ = '';
    public $jednostka_podstawowa = 'kg';
    public $waga_opakowania = '';
    public $dostawca = '';
    public $stan_aktualny = '';
    public $stan_minimalny = '';
    public $stan_optymalny = '';
    public $cena_zakupu_gr = '';
    public $stawka_vat = '5';
    public $dni_waznosci = '';
    public $data_ostatniej_dostawy = '';
    public $uwagi = '';
    public $aktywny = true;

    // Pola pomocnicze do wyświetlania
    public $cena_zakupu_zl = '';

    protected $rules = [
        'kod' => 'required|string|max:50|unique:materials,kod',
        'nazwa' => 'required|string|max:255',
        'opis' => 'nullable|string',
        'typ' => 'required|string|max:100',
        'jednostka_podstawowa' => 'required|string|max:10',
        'waga_opakowania' => 'nullable|numeric|min:0',
        'dostawca' => 'nullable|string|max:255',
        'stan_aktualny' => 'required|numeric|min:0',
        'stan_minimalny' => 'required|numeric|min:0',
        'stan_optymalny' => 'required|numeric|min:0',
        'cena_zakupu_gr' => 'nullable|integer|min:0',
        'stawka_vat' => 'required|in:0,5,8,23',
        'dni_waznosci' => 'nullable|integer|min:1',
        'data_ostatniej_dostawy' => 'nullable|date',
        'uwagi' => 'nullable|string',
        'aktywny' => 'boolean',
    ];

    protected $messages = [
        'kod.required' => 'Kod surowca jest wymagany.',
        'kod.unique' => 'Ten kod już istnieje.',
        'nazwa.required' => 'Nazwa surowca jest wymagana.',
        'typ.required' => 'Typ surowca jest wymagany.',
        'jednostka_podstawowa.required' => 'Jednostka podstawowa jest wymagana.',
        'stan_aktualny.required' => 'Stan aktualny jest wymagany.',
        'stan_aktualny.min' => 'Stan aktualny nie może być ujemny.',
        'stan_minimalny.required' => 'Stan minimalny jest wymagany.',
        'stan_minimalny.min' => 'Stan minimalny nie może być ujemny.',
        'stan_optymalny.required' => 'Stan optymalny jest wymagany.',
        'stan_optymalny.min' => 'Stan optymalny nie może być ujemny.',
        'cena_zakupu_gr.min' => 'Cena nie może być ujemna.',
        'stawka_vat.required' => 'Stawka VAT jest wymagana.',
        'stawka_vat.in' => 'Nieprawidłowa stawka VAT.',
        'dni_waznosci.min' => 'Dni ważności muszą być większe od 0.',
        'data_ostatniej_dostawy.date' => 'Nieprawidłowy format daty.',
    ];

    public function mount(?Material $material = null)
    {
        if ($material && $material->exists) {
            $this->material = $material;
            $this->isEditing = true;

            // Wypełnij pola danymi z materiału
            $this->kod = $material->kod;
            $this->nazwa = $material->nazwa;
            $this->opis = $material->opis ?? '';
            $this->typ = $material->typ;
            $this->jednostka_podstawowa = $material->jednostka_podstawowa;
            $this->waga_opakowania = $material->waga_opakowania;
            $this->dostawca = $material->dostawca ?? '';
            $this->stan_aktualny = $material->stan_aktualny;
            $this->stan_minimalny = $material->stan_minimalny;
            $this->stan_optymalny = $material->stan_optymalny;
            $this->cena_zakupu_gr = $material->cena_zakupu_gr;
            $this->stawka_vat = $material->stawka_vat;
            $this->dni_waznosci = $material->dni_waznosci;
            $this->data_ostatniej_dostawy = $material->data_ostatniej_dostawy?->format('Y-m-d');
            $this->uwagi = $material->uwagi ?? '';
            $this->aktywny = $material->aktywny;

            // Ustaw cenę w złotych do wyświetlania
            $this->cena_zakupu_zl = $material->cena_zakupu_gr ? number_format($material->cena_zakupu_gr / 100, 2, '.', '') : '';
        } else {
            // Nowy materiał - ustaw domyślne wartości
            $this->material = new Material();
            $this->isEditing = false;
        }
    }

    public function updatedCenaZakupuZl()
    {
        if ($this->cena_zakupu_zl !== '') {
            $this->cena_zakupu_gr = (int) round(floatval($this->cena_zakupu_zl) * 100);
        } else {
            $this->cena_zakupu_gr = null;
        }
    }

    public function updatedCenaZakupuGr()
    {
        if ($this->cena_zakupu_gr !== '') {
            $this->cena_zakupu_zl = number_format($this->cena_zakupu_gr / 100, 2, '.', '');
        }
    }

    public function save()
    {
        // Dostosuj reguły walidacji dla edycji
        $rules = $this->rules;
        if ($this->isEditing) {
            $rules['kod'] = 'required|string|max:50|unique:materials,kod,' . $this->material->id;
        }

        $this->validate($rules);

        $data = [
            'kod' => $this->kod,
            'nazwa' => $this->nazwa,
            'opis' => $this->opis ?: null,
            'typ' => $this->typ,
            'jednostka_podstawowa' => $this->jednostka_podstawowa,
            'waga_opakowania' => $this->waga_opakowania ?: null,
            'dostawca' => $this->dostawca ?: null,
            'stan_aktualny' => $this->stan_aktualny,
            'stan_minimalny' => $this->stan_minimalny,
            'stan_optymalny' => $this->stan_optymalny,
            'cena_zakupu_gr' => $this->cena_zakupu_gr ?: null,
            'stawka_vat' => $this->stawka_vat,
            'dni_waznosci' => $this->dni_waznosci ?: null,
            'data_ostatniej_dostawy' => $this->data_ostatniej_dostawy ?: null,
            'uwagi' => $this->uwagi ?: null,
            'aktywny' => $this->aktywny,
        ];

        if ($this->isEditing) {
            $this->material->update($data);
            $this->dispatch('material-saved', 'Surowiec został zaktualizowany');
        } else {
            Material::create($data);
            $this->dispatch('material-saved', 'Surowiec został dodany');
        }

        return redirect()->route('materials.index');
    }

    public function cancel()
    {
        return redirect()->route('materials.index');
    }

    public function render()
    {
        $types = Material::getAvailableTypes();
        $units = Material::getAvailableUnits();

        return view('livewire.materials.material-form', [
            'types' => $types,
            'units' => $units,
        ]);
    }
}
