<?php

namespace App\Livewire;

use Livewire\Component;

class TestInput extends Component
{
    public $message = 'Hola';

    public function updatedMessage($value)
    {
        logger("Nuevo valor: $value");
    }

    public function render()
    {
        return view('livewire.test-input');
    }
}
