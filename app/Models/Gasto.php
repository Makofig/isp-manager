<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'concepto', 'categoria', 'monto', 'fecha_gasto',
        'proveedor', 'comprobante', 'notas', 'user_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_gasto' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_gasto', [$desde, $hasta]);
    }

    public function scopeMesActual($query)
    {
        return $query->whereYear('fecha_gasto', now()->year)
                     ->whereMonth('fecha_gasto', now()->month);
    }
}
