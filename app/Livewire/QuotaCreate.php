<?php

namespace App\Livewire;

use App\Services\QuotaGenerationService;
use Carbon\Carbon;
use Livewire\Component;

class QuotaCreate extends Component
{
    protected $paginationTheme = 'tailwind';

    public $months_year;
    public $loading = false;
    public $progress = 0;
    public $quotaId;
    public $status = 'processing';

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

        // Delegate to centralized service
        $result = app(QuotaGenerationService::class)->handle((int) $year, $month);

        if (! $result['created']) {
            $this->addError('months_year', $result['message']);

            return;
        }

        $this->quotaId = $result['quota']->id;

        $this->resetErrorBag();

        // Activate loading UI for progress polling
        $this->loading = true;

        // Start polling for job progress
        $this->dispatch('startPolling');
    }

    public function checkProgress()
    {
        $this->progress = cache()->get("quota_progress_{$this->quotaId}", 0);

        // if ($this->progress >= 100) {
        //     $this->loading = false;
        //     session()->flash('success', 'Fee issued successfully.');
        // }
        if ($this->progress >= 100 && $this->status !== 'success') {
            $this->status = 'success';
            // $this->loading = false;
            $this->dispatch('quota-finished');
        }
    }

    public function render()
    {
        return view('livewire.quota-create')
            ->layout('layouts.app');
    }
}
