<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'contacto', 'telefono', 'email', 'direccion',
        'mb_up', 'mb_down', 'precio_total', 'precio_por_mb',
        'tipo', 'notas', 'activo'
    ];

    protected $casts = [
        'precio_total' => 'decimal:2',
        'precio_por_mb' => 'decimal:4',
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getCostoEfectivoAttribute(): float
    {
        $totalMb = $this->mb_down + $this->mb_up;
        return $totalMb > 0 ? round($this->precio_total / $totalMb, 4) : 0;
    }

    public function getAnchoBandaTotalAttribute(): int
    {
        return $this->mb_up + $this->mb_down;
    }
}
