<?php

namespace App\Http\Controllers;

use App\Models\Quota;
use App\Services\QuotaGenerationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class quotaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Quota::class);

        // traer todas las cuotas y ordenar
        // sortBy ordena en memoria
        // $quotas = Quota::all()->sortBy('created_at');
        $quotas = Quota::orderBy('created_at', 'desc')->paginate(10);

        return view('quota.index', compact('quotas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Renderizar la vista de creación de cuotas
        return view('quota.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * Validates input, then delegates to QuotaGenerationService.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Quota::class);

        $validated = $request->validate([
            'months_year' => 'required|date_format:F Y',
        ]);

        // Parse month and year from input
        [$monthName, $year] = explode(' ', $validated['months_year']);
        $month = Carbon::createFromFormat('F', $monthName)->month;

        // Delegate to service (handles quota creation + payment generation)
        $result = app(QuotaGenerationService::class)->handle((int) $year, $month);

        if ($result['created']) {
            return redirect()->route('quota')->with('success', $result['message']);
        }

        return redirect()->back()->withErrors(['quota' => $result['message']]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
