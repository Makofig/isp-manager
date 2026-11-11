<?php

namespace App\Livewire;

use App\Models\Access_Point;
use Livewire\Component;
use Livewire\WithPagination;

class AccessTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    /*
    protected $listeners = ['filterBySearch'];

    function filterBySearch($filterSearch){
        $this->search = strtolower(preg_replace('/\s+/', ' ', trim($filterSearch)));
    }
    */
    public function updatedSearch($value)
    {
        $this->search = strtolower(preg_replace('/\s+/', ' ', trim($value)));
        $this->resetPage();
    }

    // Esto hace que search estén en el query string
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function render()
    {
        $points = Access_Point::when($this->search, function ($query) {
            $query->whereRaw('LOWER(ssid) LIKE ?', ['%' . $this->search . '%']);
        })->paginate(10);

        return view('livewire.access-table', [
            'points' => $points,
        ]);
    }
}
