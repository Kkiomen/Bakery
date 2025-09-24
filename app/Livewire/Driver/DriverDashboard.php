<?php

namespace App\Livewire\Driver;

use App\Models\Delivery;
use App\Models\DeliveryPhoto;
use App\Models\DeliverySignature;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriverDashboard extends Component
{
    use WithFileUploads;

    public $selectedDate;
    public $statusFilter = '';
    public $currentDelivery = null;

    // Filtrowanie
    public $showCompleted = false;

    // Modals
    public $showDeliveryDetails = false;
    public $showPhotoUpload = false;
    public $showSignatureModal = false;

    // Upload zdjęć
    public $photos = [];
    public $photoDescription = '';
    public $photoType = 'produkty';

    // Podpis
    public $signatureData = '';
    public $signerName = '';
    public $signerPosition = '';
    public $signatureNotes = '';

    public function mount()
    {
        // Pobierz datę z sesji lub ustaw dzisiejszą jako domyślną
        $this->selectedDate = session('driver_selected_date', now()->format('Y-m-d'));
    }

    public function render()
    {
        $deliveries = $this->getMyDeliveries();
        $stats = $this->getDeliveryStats();

        return view('livewire.driver.driver-dashboard', [
            'deliveries' => $deliveries,
            'stats' => $stats,
        ]);
    }

    public function getMyDeliveries()
    {
        return Delivery::with(['productionOrder', 'items.product'])
            ->byDriver(Auth::id())
            ->when($this->selectedDate, fn($q) => $q->forDate($this->selectedDate))
            ->when($this->statusFilter, fn($q) => $q->byStatus($this->statusFilter))
            ->when(!$this->showCompleted, fn($q) => $q->whereNotIn('status', ['dostarczona', 'anulowana']))
            ->orderedBySequence()
            ->get();
    }

    public function getDeliveryStats()
    {
        $baseQuery = Delivery::byDriver(Auth::id())
            ->when($this->selectedDate, fn($q) => $q->forDate($this->selectedDate));

        return [
            'total' => $baseQuery->count(),
            'przypisane' => $baseQuery->clone()->byStatus('przypisana')->count(),
            'w_drodze' => $baseQuery->clone()->byStatus('w_drodze')->count(),
            'dostarczone' => $baseQuery->clone()->byStatus('dostarczona')->count(),
            'problemy' => $baseQuery->clone()->byStatus('problem')->count(),
        ];
    }

    public function viewDelivery($deliveryId)
    {
        $this->currentDelivery = Delivery::with(['productionOrder', 'items.product', 'photos', 'signature'])
            ->findOrFail($deliveryId);
        $this->showDeliveryDetails = true;
    }

    public function updatedSelectedDate($value)
    {
        // Zapisz wybraną datę w sesji
        session(['driver_selected_date' => $value]);
    }

    public function showToday()
    {
        $today = now()->format('Y-m-d');
        $this->selectedDate = $today;
        session(['driver_selected_date' => $today]);
    }

    public function startDelivery($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->canBeStarted() && $delivery->driver_id === Auth::id()) {
            $delivery->startDelivery();
            $this->dispatch('delivery-started', ['message' => 'Dostawa została rozpoczęta.']);
        }
    }

    public function completeDelivery($deliveryId)
    {
        $delivery = Delivery::with('signature')->findOrFail($deliveryId);

        // Sprawdź czy dostawa ma wymagane elementy
        if (!$delivery->signature()->exists()) {
            $this->dispatch('completion-failed', [
                'message' => 'Nie można zakończyć dostawy bez podpisu odbiorcy.'
            ]);
            return;
        }

        if ($delivery->canBeCompleted() && $delivery->driver_id === Auth::id()) {
            $delivery->completeDelivery();
            $this->dispatch('delivery-completed', ['message' => 'Dostawa została zakończona.']);
        }
    }

    public function reportProblem($deliveryId, $description)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->driver_id === Auth::id()) {
            $delivery->reportProblem($description);
            $this->dispatch('problem-reported', ['message' => 'Problem został zgłoszony.']);
        }
    }

    public function openPhotoUpload($deliveryId)
    {
        $this->currentDelivery = Delivery::findOrFail($deliveryId);
        $this->showPhotoUpload = true;
        $this->photos = [];
        $this->photoDescription = '';
        $this->photoType = 'produkty';
    }

    public function uploadPhotos()
    {
        $this->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'image|max:5120', // 5MB max
            'photoDescription' => 'nullable|string|max:500',
            'photoType' => 'required|in:produkty,dowod_dostawy,problem,lokalizacja,inne',
        ]);

        try {
            foreach ($this->photos as $index => $photo) {
                $filename = 'delivery_' . $this->currentDelivery->id . '_' . time() . '_' . $index . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('deliveries/photos', $filename, 'public');

                DeliveryPhoto::create([
                    'delivery_id' => $this->currentDelivery->id,
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'opis' => $this->photoDescription,
                    'typ_zdjecia' => $this->photoType,
                    'kolejnosc' => $index,
                    'data_wykonania' => now(),
                ]);
            }

            $this->showPhotoUpload = false;
            $this->photos = [];
            $this->dispatch('photos-uploaded', ['message' => 'Zdjęcia zostały przesłane.']);

        } catch (\Exception $e) {
            $this->dispatch('photo-upload-failed', [
                'message' => 'Błąd podczas przesyłania zdjęć: ' . $e->getMessage()
            ]);
        }
    }

    public function openSignatureModal($deliveryId)
    {
        $this->currentDelivery = Delivery::findOrFail($deliveryId);
        $this->showSignatureModal = true;

        // Check if signature already exists and pre-fill form
        if ($this->currentDelivery->signature()->exists()) {
            $signature = $this->currentDelivery->signature()->first();
            $this->signatureData = $signature->signature_image; // Full base64 with prefix
            $this->signerName = $signature->signer_name;
            $this->signerPosition = $signature->signer_position ?? '';
            $this->signatureNotes = $signature->uwagi ?? '';
        } else {
            // Clear form for new signature
            $this->signatureData = '';
            $this->signerName = '';
            $this->signerPosition = '';
            $this->signatureNotes = '';
        }

        // Emit event to initialize signature pad
        $this->dispatch('showSignatureModal');
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
            // Usuń prefix "data:image/png;base64," jeśli istnieje
            $signatureData = preg_replace('/^data:image\/png;base64,/', '', $this->signatureData);

            // Check if signature already exists for this delivery
            $existingSignature = $this->currentDelivery->signature()->first();

            if ($existingSignature) {
                // Update existing signature
                $existingSignature->update([
                    'signature_data' => $signatureData,
                    'signer_name' => $this->signerName,
                    'signer_position' => $this->signerPosition,
                    'signature_date' => now(), // Update timestamp
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'uwagi' => $this->signatureNotes,
                ]);

                $message = 'Podpis został zaktualizowany pomyślnie.';
            } else {
                // Create new signature
                DeliverySignature::create([
                    'delivery_id' => $this->currentDelivery->id,
                    'signature_data' => $signatureData,
                    'signer_name' => $this->signerName,
                    'signer_position' => $this->signerPosition,
                    'signature_date' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'uwagi' => $this->signatureNotes,
                ]);

                $message = 'Podpis został zapisany pomyślnie.';
            }

            $this->showSignatureModal = false;
            $this->dispatch('signature-saved', ['message' => $message]);

        } catch (\Exception $e) {
            $this->dispatch('signature-save-failed', [
                'message' => 'Błąd podczas zapisywania podpisu: ' . $e->getMessage()
            ]);
        }
    }

    public function getDirectionsUrl($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        return $delivery->google_maps_url;
    }

    public function updateItemStatus($deliveryId, $itemId, $status, $quantity = null)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->driver_id !== Auth::id()) {
            return;
        }

        $item = $delivery->items()->findOrFail($itemId);

        switch ($status) {
            case 'dostarczony':
                $item->markAsDelivered($quantity);
                break;
            case 'brakuje':
                $item->markAsMissing();
                break;
            case 'uszkodzony':
                $item->markAsDamaged();
                break;
        }

        $this->dispatch('item-status-updated', ['message' => 'Status pozycji został zaktualizowany.']);
    }

    public function refreshDeliveries()
    {
        // Odświeża listę dostaw
        $this->dispatch('deliveries-refreshed');
    }

    public function closeModal()
    {
        $this->showDeliveryDetails = false;
        $this->showPhotoUpload = false;
        $this->showSignatureModal = false;
        $this->currentDelivery = null;
    }


    public function toggleCompleted()
    {
        $this->showCompleted = !$this->showCompleted;
    }

    // JavaScript bridge methods
    public function updateSignatureData($signatureData)
    {
        $this->signatureData = $signatureData;
    }

    public function getLocationAndUpdateSignature($lat, $lng)
    {
        if ($this->currentDelivery && $this->showSignatureModal) {
            // Aktualizuj lokalizację w podpisie jeśli jest dostępna
            $signature = DeliverySignature::where('delivery_id', $this->currentDelivery->id)->latest()->first();
            if ($signature) {
                $signature->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
            }
        }
    }
}
