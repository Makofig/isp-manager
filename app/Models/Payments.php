<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'pagos';

    protected $fillable = [
        'id_cliente',
        'id_cuota',
        'num_cuotas',
        'costo',
        'abonado',
        'fecha_pago',
        'comentario',
        'image',
        'image2',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'datetime',
        ];
    }

    // Relación con el modelo de cliente uno a muchos
    public function clients()
    {
        return $this->belongsTo(Client::class, 'id_cliente')->withTrashed();
    }

    // Relación con el modelo de cuotas uno a muchos
    public function cuota()
    {
        return $this->belongsTo(Quota::class, 'id_cuota');
    }
}
