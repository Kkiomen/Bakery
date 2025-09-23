<?php

namespace App\Livewire\Deliveries;

use Livewire\Component;

class SimpleDeliveryTest extends Component
{
    public $showCreateForm = false;
    public $testMessage = 'Kliknij przycisk';

    public function showCreateForm()
    {
        $this->showCreateForm = true;
        $this->testMessage = 'Modal został otwarty!';
    }

    public function hideCreateForm()
    {
        $this->showCreateForm = false;
        $this->testMessage = 'Modal został zamknięty!';
    }

    public function render()
    {
        return view('livewire.deliveries.simple-delivery-test');
    }
}

