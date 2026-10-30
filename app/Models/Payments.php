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
        'fecha_pago',
        'comentario',
        'image',
        'image2'
    ]; 

    // Relación con el modelo de cliente uno a muchos 
    public function clients()
    {
        return $this->belongsTo(Client::class, 'id_cliente');
    }
    // Relación con el modelo de cuotas uno a muchos
    public function cuota()
    {
        return $this->belongsTo(Quota::class, 'id_cuota');
    }
}
