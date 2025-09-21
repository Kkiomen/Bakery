<?php

namespace App\Livewire\Deliveries;

use App\Models\Delivery;
use App\Models\User;
use Livewire\Component;

class SimpleDeliveryManagement extends Component
{
    public $showCreateForm = false;
    public $testMessage = 'Kliknij przycisk';

    public function openModal()
    {
        \Log::info('SimpleDeliveryManagement: openModal called');
        $this->showCreateForm = true;
        $this->testMessage = 'Modal został otwarty!';
        \Log::info('SimpleDeliveryManagement: showCreateForm set to true');
    }

    public function closeModal()
    {
        \Log::info('SimpleDeliveryManagement: closeModal called');
        $this->showCreateForm = false;
        $this->testMessage = 'Modal został zamknięty!';
    }

    public function render()
    {
        // Proste pobranie danych bez skomplikowanych metod
        $deliveries = Delivery::with(['driver', 'productionOrder'])
                             ->orderBy('created_at', 'desc')
                             ->limit(10)
                             ->get();

        $drivers = User::all();

        $stats = [
            'total' => Delivery::count(),
            'oczekujace' => Delivery::where('status', 'oczekujaca')->count(),
            'dostarczone' => Delivery::where('status', 'dostarczona')->count(),
        ];

        return view('livewire.deliveries.simple-delivery-management', [
            'deliveries' => $deliveries,
            'drivers' => $drivers,
            'stats' => $stats,
        ]);
    }
}
