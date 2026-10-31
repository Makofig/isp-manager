<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quota extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'cuotas';

    protected $fillable = [
        'numero',
        'year',
    ];

    // Relación con el modelo de pagos uno a muchos
    public function pagos()
    {
        return $this->hasMany(Payments::class, 'id_cuota');
    }
}
