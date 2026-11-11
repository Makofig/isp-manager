<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Contracts;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsTable extends Component
{
    use WithPagination;

    public $id_plan = null;
    public $contracts;
    public $search = '';
    public $onlyBanned = false;

    protected $paginationTheme = 'tailwind';

    // Esto hace que search e id_plan estén en el query string
    protected $queryString = [
        'search' => ['except' => '', 'debounce' => 300],
        'id_plan' => ['except' => null],
    ];

    public function mount($onlyBanned = false)
    {
        $this->onlyBanned = $onlyBanned;
        $this->contracts = Contracts::all();
    }

    public function updatedSearch($value)
    {
        $this->search = strtolower(preg_replace('/\s+/', ' ', trim($value)));
        $this->resetPage();
    }

    public function updatedIdPlan()
    {
        // Reinicia la paginación al cambiar
        $this->resetPage();
    }

    public function toggleBan($id)
    {
        $client = Client::findOrFail($id);

        Gate::authorize('ban', $client);

        $client->update([
            'is_banned' => ! $client->is_banned,
        ]);

        session()->flash('message', $client->is_banned ? 'Client banned.' : 'Client unbanned.');
    }
    /*
    public function updatingIdPlan()
    {
        $this->resetPage();
    }
    */

    public function render()
    {
        $clients = Client::withCount([
            'pagos as debtors_count' => function ($query) {
                $query->where('estado', '0');
            },
            'pagos as paid_count' => function ($query) {
                $query->where('estado', '1');
            },
        ])
            ->when($this->id_plan, function ($query) {
                $query->where('id_plan', (int) $this->id_plan);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $this->search . '%'])
                        ->orWhereRaw('LOWER(apellido) LIKE ?', ['%' . $this->search . '%'])
                        ->orWhereRaw('ip LIKE ?', ['%' . $this->search . '%']);
                });
            })
            ->when($this->onlyBanned, function ($query) {
                $query->where('is_banned', 1); // o el campo que uses
            })
            ->paginate(10);

        return view('livewire.clients-table', [
            'clients' => $clients,
        ]);
    }
}
