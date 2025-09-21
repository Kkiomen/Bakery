<?php

namespace App\Livewire\Driver;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DeliveryPhoto;
use App\Models\DeliverySignature;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class DeliveryDetails extends Component
{
    use WithFileUploads;

    public Delivery $delivery;
    public $showPhotoModal = false;
    public $showSignatureModal = false;
    public $showItemDetails = false;
    public $selectedItem = null;

    // Photo upload
    public $newPhotos = [];
    public $photoDescription = '';
    public $photoType = 'produkty';

    // Signature
    public $signatureData = '';
    public $signerName = '';
    public $signerPosition = '';
    public $signatureNotes = '';

    // Item status update
    public $itemQuantityDelivered = [];

    protected $listeners = [
        'signature-pad-updated' => 'updateSignatureData',
        'location-updated' => 'updateLocation',
    ];

    public function mount(Delivery $delivery)
    {
        $this->delivery = $delivery->load(['productionOrder', 'items.product', 'photos', 'signature']);

        // Initialize quantity delivered for each item
        foreach ($this->delivery->items as $item) {
            $this->itemQuantityDelivered[$item->id] = $item->ilosc_dostarczona;
        }
    }

    public function render()
    {
        return view('livewire.driver.delivery-details');
    }

    public function startDelivery()
    {
        if ($this->delivery->canBeStarted() && $this->delivery->driver_id === Auth::id()) {
            $this->delivery->startDelivery();
            $this->delivery->refresh();

            $this->dispatch('delivery-started', [
                'message' => 'Dostawa została rozpoczęta. Powodzenia!'
            ]);
        }
    }

    public function completeDelivery()
    {
        // Sprawdź wymagania do zakończenia dostawy
        if (!$this->delivery->signature()->exists()) {
            $this->dispatch('completion-failed', [
                'message' => 'Nie można zakończyć dostawy bez podpisu odbiorcy.'
            ]);
            return;
        }

        if (!$this->delivery->photos()->productPhotos()->exists()) {
            $this->dispatch('completion-failed', [
                'message' => 'Dodaj przynajmniej jedno zdjęcie dostarczonych produktów.'
            ]);
            return;
        }

        if ($this->delivery->canBeCompleted() && $this->delivery->driver_id === Auth::id()) {
            $this->delivery->completeDelivery();
            $this->delivery->refresh();

            $this->dispatch('delivery-completed', [
                'message' => 'Dostawa została zakończona pomyślnie!'
            ]);
        }
    }

    public function reportProblem($description)
    {
        if ($this->delivery->driver_id === Auth::id()) {
            $this->delivery->reportProblem($description);
            $this->delivery->refresh();

            $this->dispatch('problem-reported', [
                'message' => 'Problem został zgłoszony.'
            ]);
        }
    }

    public function updateItemQuantity($itemId)
    {
        $item = $this->delivery->items()->findOrFail($itemId);
        $newQuantity = $this->itemQuantityDelivered[$itemId];

        if ($newQuantity > $item->ilosc) {
            $this->itemQuantityDelivered[$itemId] = $item->ilosc;
            return;
        }

        $item->update([
            'ilosc_dostarczona' => $newQuantity,
            'status' => $newQuantity >= $item->ilosc ? 'dostarczony' :
                       ($newQuantity > 0 ? 'brakuje' : 'oczekujacy')
        ]);

        $this->dispatch('item-updated', [
            'message' => 'Ilość dostarczona została zaktualizowana.'
        ]);
    }

    public function markItemAsMissing($itemId, $reason = null)
    {
        $item = $this->delivery->items()->findOrFail($itemId);
        $item->markAsMissing($reason);
        $this->itemQuantityDelivered[$itemId] = 0;

        $this->dispatch('item-marked-missing', [
            'message' => 'Pozycja została oznaczona jako brakująca.'
        ]);
    }

    public function markItemAsDamaged($itemId, $description = null)
    {
        $item = $this->delivery->items()->findOrFail($itemId);
        $item->markAsDamaged($description);
        $this->itemQuantityDelivered[$itemId] = 0;

        $this->dispatch('item-marked-damaged', [
            'message' => 'Pozycja została oznaczona jako uszkodzona.'
        ]);
    }

    public function openPhotoModal($type = 'produkty')
    {
        $this->photoType = $type;
        $this->showPhotoModal = true;
        $this->newPhotos = [];
        $this->photoDescription = '';
    }

    public function uploadPhotos()
    {
        $this->validate([
            'newPhotos' => 'required|array|max:10',
            'newPhotos.*' => 'image|max:5120', // 5MB max
            'photoDescription' => 'nullable|string|max:500',
            'photoType' => 'required|in:produkty,dowod_dostawy,problem,lokalizacja,inne',
        ], [
            'newPhotos.required' => 'Wybierz przynajmniej jedno zdjęcie.',
            'newPhotos.*.image' => 'Wszystkie pliki muszą być zdjęciami.',
            'newPhotos.*.max' => 'Maksymalny rozmiar zdjęcia to 5MB.',
        ]);

        try {
            foreach ($this->newPhotos as $index => $photo) {
                $filename = 'delivery_' . $this->delivery->id . '_' . time() . '_' . $index . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('deliveries/photos', $filename, 'public');

                DeliveryPhoto::create([
                    'delivery_id' => $this->delivery->id,
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'opis' => $this->photoDescription,
                    'typ_zdjecia' => $this->photoType,
                    'kolejnosc' => DeliveryPhoto::where('delivery_id', $this->delivery->id)->max('kolejnosc') + 1,
                    'data_wykonania' => now(),
                ]);
            }

            $this->delivery->refresh();
            $this->showPhotoModal = false;
            $this->newPhotos = [];

            $this->dispatch('photos-uploaded', [
                'message' => 'Zdjęcia zostały przesłane pomyślnie.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('photo-upload-failed', [
                'message' => 'Błąd podczas przesyłania zdjęć: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePhoto($photoId)
    {
        $photo = DeliveryPhoto::where('delivery_id', $this->delivery->id)
                             ->findOrFail($photoId);

        $photo->delete();
        $this->delivery->refresh();

        $this->dispatch('photo-deleted', [
            'message' => 'Zdjęcie zostało usunięte.'
        ]);
    }

    public function openSignatureModal()
    {
        if ($this->delivery->signature()->exists()) {
            $this->dispatch('signature-already-exists', [
                'message' => 'Podpis już istnieje dla tej dostawy.'
            ]);
            return;
        }

        $this->showSignatureModal = true;
        $this->signatureData = '';
        $this->signerName = '';
        $this->signerPosition = '';
        $this->signatureNotes = '';
    }

    public function saveSignature()
    {
        $this->validate([
            'signatureData' => 'required|string',
            'signerName' => 'required|string|max:255',
            'signerPosition' => 'nullable|string|max:255',
            'signatureNotes' => 'nullable|string|max:500',
        ], [
            'signatureData.required' => 'Podpis jest wymagany.',
            'signerName.required' => 'Imię i nazwisko osoby podpisującej jest wymagane.',
        ]);

        try {
            // Remove base64 prefix if present
            $signatureData = preg_replace('/^data:image\/png;base64,/', '', $this->signatureData);

            DeliverySignature::create([
                'delivery_id' => $this->delivery->id,
                'signature_data' => $signatureData,
                'signer_name' => $this->signerName,
                'signer_position' => $this->signerPosition,
                'signature_date' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'uwagi' => $this->signatureNotes,
            ]);

            $this->delivery->refresh();
            $this->showSignatureModal = false;

            $this->dispatch('signature-saved', [
                'message' => 'Podpis został zapisany pomyślnie.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('signature-save-failed', [
                'message' => 'Błąd podczas zapisywania podpisu: ' . $e->getMessage()
            ]);
        }
    }

    public function updateSignatureData($signatureData)
    {
        $this->signatureData = $signatureData;
    }

    public function updateLocation($lat, $lng)
    {
        // Update delivery location if needed
        if ($this->delivery->latitude === null || $this->delivery->longitude === null) {
            $this->delivery->update([
                'latitude' => $lat,
                'longitude' => $lng,
            ]);
        }
    }

    public function getDirectionsUrl()
    {
        return $this->delivery->google_maps_url;
    }

    public function callCustomer()
    {
        $phone = $this->delivery->telefon_kontaktowy ?: $this->delivery->klient_telefon;
        if ($phone) {
            $this->dispatch('call-customer', ['phone' => $phone]);
        }
    }

    public function viewItemDetails($itemId)
    {
        $this->selectedItem = $this->delivery->items()->with('product')->findOrFail($itemId);
        $this->showItemDetails = true;
    }

    public function closeModal()
    {
        $this->showPhotoModal = false;
        $this->showSignatureModal = false;
        $this->showItemDetails = false;
        $this->selectedItem = null;
    }

    public function refreshDelivery()
    {
        $this->delivery->refresh();

        // Update quantity delivered array
        foreach ($this->delivery->items as $item) {
            $this->itemQuantityDelivered[$item->id] = $item->ilosc_dostarczona;
        }

        $this->dispatch('delivery-refreshed');
    }

    public function getDeliveryProgress()
    {
        return $this->delivery->getDeliveryProgress();
    }

    public function getTotalWeight()
    {
        return $this->delivery->items->sum('waga_kg');
    }

    public function getEstimatedDuration()
    {
        // Estimate based on number of items and distance
        $baseTime = 15; // 15 minutes base time
        $itemTime = $this->delivery->items->count() * 2; // 2 minutes per item
        $distanceTime = ($this->delivery->dystans_km ?? 10) * 2; // 2 minutes per km

        return $baseTime + $itemTime + $distanceTime;
    }
}
