<?php

namespace App\Livewire\Contractors;

use App\Models\Contractor;
use Livewire\Component;

class CreateContractor extends Component
{
    public $nazwa = '';
    public $nip = '';
    public $regon = '';
    public $adres = '';
    public $kod_pocztowy = '';
    public $miasto = '';
    public $telefon = '';
    public $email = '';
    public $osoba_kontaktowa = '';
    public $telefon_kontaktowy = '';
    public $typ = 'klient';
    public $aktywny = true;
    public $uwagi = '';
    public $latitude = null;
    public $longitude = null;

    protected function rules()
    {
        return [
            'nazwa' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'regon' => 'nullable|string|max:20',
            'adres' => 'required|string|max:255',
            'kod_pocztowy' => 'required|string|max:10',
            'miasto' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'osoba_kontaktowa' => 'nullable|string|max:255',
            'telefon_kontaktowy' => 'nullable|string|max:20',
            'typ' => 'required|in:klient,dostawca,obydwa',
            'aktywny' => 'boolean',
            'uwagi' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    protected function messages()
    {
        return [
            'nazwa.required' => 'Nazwa kontrahenta jest wymagana.',
            'adres.required' => 'Adres jest wymagany.',
            'kod_pocztowy.required' => 'Kod pocztowy jest wymagany.',
            'miasto.required' => 'Miasto jest wymagane.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'typ.required' => 'Typ kontrahenta jest wymagany.',
        ];
    }

    public function save()
    {
        $this->validate();

        Contractor::create([
            'nazwa' => $this->nazwa,
            'nip' => $this->nip,
            'regon' => $this->regon,
            'adres' => $this->adres,
            'kod_pocztowy' => $this->kod_pocztowy,
            'miasto' => $this->miasto,
            'telefon' => $this->telefon,
            'email' => $this->email,
            'osoba_kontaktowa' => $this->osoba_kontaktowa,
            'telefon_kontaktowy' => $this->telefon_kontaktowy,
            'typ' => $this->typ,
            'aktywny' => $this->aktywny,
            'uwagi' => $this->uwagi,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        session()->flash('success', 'Kontrahent został utworzony pomyślnie.');

        $this->dispatch('contractor-created');
        $this->reset();
    }

    public function searchLocation()
    {
        // TODO: Implementacja wyszukiwania współrzędnych na podstawie adresu
        // Można użyć API Google Maps lub OpenStreetMap
    }

    public function render()
    {
        return view('livewire.contractors.create-contractor');
    }
}
