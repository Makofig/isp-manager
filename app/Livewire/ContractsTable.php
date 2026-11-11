<?php

namespace App\Livewire;

use App\Models\Contracts;
use Livewire\Component;
use Livewire\WithPagination;

class ContractsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    public function updatedSearch($value)
    {
        $this->search = strtolower(preg_replace('/\s+/', ' ', trim($value)));
        $this->resetPage();
    }

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function render()
    {
        $contracts = Contracts::when($this->search, function ($query) {
            $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . $this->search . '%']);
        })->paginate(10);

        return view('livewire.contracts-table', [
            'contracts' => $contracts,
        ]);
    }
}
