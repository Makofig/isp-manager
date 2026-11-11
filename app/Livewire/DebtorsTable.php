<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class DebtorsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $anio;
    public $mes;

    public function mount()
    {
        // Por defecto: año y mes actual
        $this->anio = now()->year;
        $this->mes = now()->month;
    }

    public function updatedAnio($value)
    {
        $this->anio = $value;
        $this->resetPage();
    }

    public function updatedMes($value)
    {
        $this->mes = $value;
        $this->resetPage();
    }

    public function updatedSearch($value)
    {
        $this->search = strtolower(preg_replace('/\s+/', ' ', trim($value)));
        $this->resetPage();
    }

    public function export()
    {
        return redirect()->route('clients.export', [
            'type' => 'debtors',
            'anio' => $this->anio,
            'mes' => $this->mes,
        ]);
    }

    public function render()
    {
        $debtors = Client::select('cliente.*', 'pagos.id as pago_id', 'pagos.estado as pago_estado')
            ->join('pagos', 'cliente.id', '=', 'pagos.id_cliente')
            ->where('pagos.estado', 0)
            ->when($this->anio, function ($query) {
                $query->whereYear('pagos.created_at', $this->anio);
            })
            ->when($this->mes, function ($query) {
                $query->whereMonth('pagos.created_at', $this->mes);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(cliente.nombre) LIKE ?', ['%' . strtolower($this->search) . '%'])
                        ->orWhereRaw('LOWER(cliente.apellido) LIKE ?', ['%' . strtolower($this->search) . '%']);
                });
            })
            ->paginate(10);

        return view('livewire.debtors-table', [
            'debtors' => $debtors,
        ]);
    }
}
