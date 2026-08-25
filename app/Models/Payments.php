<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Payments extends Model
{
    use Auditable;

    // Nombre de la tabla
    protected $table = 'pagos';

    protected $fillable = [
        'id_cliente',
        'id_cuota',
        'num_cuotas',
        'costo',
        'abonado',
        'pago_parcial',
        'estado',
        'fecha_pago',
        'fecha_vencimiento',
        'comentario',
        'image',
        'image2'
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'abonado' => 'decimal:2',
        'pago_parcial' => 'decimal:2',
        'fecha_pago' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    // Relación con el modelo de cliente
    public function clients()
    {
        return $this->belongsTo(Client::class, 'id_cliente');
    }

    // Alias para compatibilidad con vistas (usado en blade)
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'id_cliente');
    }

    // Relación con el modelo de cuotas
    public function cuota()
    {
        return $this->belongsTo(Quota::class, 'id_cuota');
    }

    // Verificar si está pagado completamente
    public function getIsPaidAttribute(): bool
    {
        return $this->estado === 1 || $this->abonado >= $this->costo;
    }

    // Verificar si tiene pago parcial
    public function getHasPartialPaymentAttribute(): bool
    {
        return $this->pago_parcial > 0 && $this->abonado < $this->costo;
    }

    // Calcular deuda restante
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->costo - $this->abonado);
    }
}
