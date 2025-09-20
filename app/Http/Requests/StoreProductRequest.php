<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'ean' => ['nullable', 'string', 'min:8', 'max:14', 'unique:products,ean'],
            'nazwa' => ['required', 'string', 'max:255'],
            'opis' => ['nullable', 'string'],
            'kategoria_id' => ['required', 'exists:categories,id'],
            'waga_g' => ['required', 'integer', 'min:1'],
            'jednostka_sprzedazy' => ['required', Rule::in(['szt', 'opak', 'kg'])],
            'zawartosc_opakowania' => ['nullable', 'integer', 'min:1'],
            'alergeny' => ['nullable', 'array'],
            'alergeny.*' => [Rule::in(['gluten', 'mleko', 'jajka', 'orzechy', 'soja', 'sezam'])],
            'wartosci_odzywcze' => ['nullable', 'array'],
            'wartosci_odzywcze.kcal' => ['nullable', 'numeric', 'min:0'],
            'wartosci_odzywcze.bialko_g' => ['nullable', 'numeric', 'min:0'],
            'wartosci_odzywcze.tluszcz_g' => ['nullable', 'numeric', 'min:0'],
            'wartosci_odzywcze.wegle_g' => ['nullable', 'numeric', 'min:0'],
            'stawka_vat' => ['required', Rule::in(['0', '5', '8', '23'])],
            'cena_netto_gr' => ['required', 'integer', 'min:1'],
            'aktywny' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'SKU jest wymagane.',
            'sku.unique' => 'SKU musi być unikalne.',
            'ean.min' => 'EAN musi mieć co najmniej 8 znaków.',
            'ean.max' => 'EAN może mieć maksymalnie 14 znaków.',
            'ean.unique' => 'EAN musi być unikalne.',
            'nazwa.required' => 'Nazwa produktu jest wymagana.',
            'nazwa.max' => 'Nazwa może mieć maksymalnie 255 znaków.',
            'kategoria_id.required' => 'Kategoria jest wymagana.',
            'kategoria_id.exists' => 'Wybrana kategoria nie istnieje.',
            'waga_g.required' => 'Waga jest wymagana.',
            'waga_g.min' => 'Waga musi być większa od 0.',
            'jednostka_sprzedazy.required' => 'Jednostka sprzedaży jest wymagana.',
            'jednostka_sprzedazy.in' => 'Nieprawidłowa jednostka sprzedaży.',
            'zawartosc_opakowania.min' => 'Zawartość opakowania musi być większa od 0.',
            'alergeny.*.in' => 'Nieprawidłowy alergen.',
            'wartosci_odzywcze.kcal.min' => 'Wartość kaloryczna nie może być ujemna.',
            'wartosci_odzywcze.bialko_g.min' => 'Zawartość białka nie może być ujemna.',
            'wartosci_odzywcze.tluszcz_g.min' => 'Zawartość tłuszczu nie może być ujemna.',
            'wartosci_odzywcze.wegle_g.min' => 'Zawartość węglowodanów nie może być ujemna.',
            'stawka_vat.required' => 'Stawka VAT jest wymagana.',
            'stawka_vat.in' => 'Nieprawidłowa stawka VAT.',
            'cena_netto_gr.required' => 'Cena netto jest wymagana.',
            'cena_netto_gr.min' => 'Cena musi być większa od 0.',
            'meta_title.max' => 'Tytuł SEO może mieć maksymalnie 255 znaków.',
            'meta_description.max' => 'Opis SEO może mieć maksymalnie 500 znaków.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'sku' => 'SKU',
            'ean' => 'EAN',
            'nazwa' => 'nazwa',
            'opis' => 'opis',
            'kategoria_id' => 'kategoria',
            'waga_g' => 'waga',
            'jednostka_sprzedazy' => 'jednostka sprzedaży',
            'zawartosc_opakowania' => 'zawartość opakowania',
            'alergeny' => 'alergeny',
            'wartosci_odzywcze' => 'wartości odżywcze',
            'stawka_vat' => 'stawka VAT',
            'cena_netto_gr' => 'cena netto',
            'aktywny' => 'aktywny',
            'meta_title' => 'tytuł SEO',
            'meta_description' => 'opis SEO',
        ];
    }
}
