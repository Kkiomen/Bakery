<?php

namespace App\Livewire\Test;

use Livewire\Component;

class SimpleTest extends Component
{
    public $testMessage = 'Początkowa wiadomość';
    public $showModal = false;

    public function openModal()
    {
        $this->testMessage = 'Modal został otwarty!';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.test.simple-test');
    }
}
