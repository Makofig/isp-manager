<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Quota;
use App\Jobs\GenerateQuotaPayments;
use Carbon\Carbon;

class QuotaCreate extends Component
{
    protected $paginationTheme = 'tailwind';

    public $months_year; 
    public $loading = false; 
    public $progress = 0; 
    public $quotaId; 

    protected $listeners = ['execute-save' => 'save'];

    protected $rules = [
        'months_year' => 'required|date_format:F Y',
    ];

    public function mount()
    {
        $this->months_year = now()->format('F Y');
    }

    public function confirmCreate()
    {
        $this->dispatch('show-confirm');
    }

    public function save()
    {
        if ($this->loading) {
            return; // evita doble ejecución
        }

        $this->validate();

        [$monthName, $year] = explode(' ', $this->months_year);
        $month = Carbon::createFromFormat('F', $monthName)->month;

        $existingQuota = Quota::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->first();

        if ($existingQuota) {
            $this->addError('months_year', 'This month\'s fee already exists.');
            return;
        }

        $quota = Quota::create([
            'numero' => $month,
        ]);

        $this->quotaId = $quota->id;

        $this->resetErrorBag(); // 🔥 limpia errores viejos

        // Activamos loading UI
        $this->loading = true;

        // Cacheamos progreso inicial 
        #cache()->put("quota_progress_{$quota->id}", 0, now()->addMinutes(30));

        // Despachamos Job
        GenerateQuotaPayments::dispatch($quota->id);

        // Empezamos polling
        $this->dispatch('startPolling');
    }

    public function checkProgress()
    {
        $this->progress = cache()->get("quota_progress_{$this->quotaId}", 0);

        if ($this->progress >= 100) {
            $this->loading = false;
            session()->flash('success', 'Fee issued successfully.');
        }
    }

    public function render()
    {
        return view('livewire.quota-create')
            ->layout('layouts.app');
    }
}
