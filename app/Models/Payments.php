<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    // Nombre de la tabla
    protected $table = 'pagos';

    protected $fillable = [
        'id_cliente',
        'id_cuota',
        'num_cuotas',
        'costo',
        'abonado',
        'estado',
        'fecha_pago',
        'comentario',
        'image',
        'link_pago',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'costo' => 'decimal:2',
        'abonado' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    // Relación con el modelo de cliente uno a muchos
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'id_cliente');
    }

    // Relación con el modelo de cuotas uno a muchos
    public function cuota()
    {
        return $this->belongsTo(Quota::class, 'id_cuota');
    }
}
