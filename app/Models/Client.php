<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    // borrado lógico
    use SoftDeletes;
    
    // Nombre de la tabla 
    protected $table = 'cliente'; 

    // Clave primaria solo si no es id 
    // protected $primaryKey = 'id_cliente';

    // si no se usa timestamps (created_at / updated_at)
    // public $timestamps = false;

    protected $fillable = [
        'id_plan', 
        'id_point', 
        'nombre',
        'apellido',
        'direccion',
        'telefono',
        'ip',
        'imagen',
        'is_banned'
    ]; 

    // Relación con el modelo de contratos uno a uno. 
    public function contract()
    {
        return $this->belongsTo(Contracts::class, 'id_plan');
    }
    // Relación con el modelo de pagos uno a muchos.
    public function pagos()
    {
        return $this->hasMany(Payments::class, 'id_cliente');
    }
    // Relación con el modelo de puntos de acceso uno a uno.
    public function accessPoint()
    {
        return $this->belongsTo(Access_Point::class, 'id_point');
    }
    /*
    public function getDebtorsCountAttribute()
    {
        return $this->pagos()->where('estado', '0')->count();
    }

    public function getPaidCountAttribute()
    {
        return $this->pagos()->where('estado', '1')->count();
    }
    */
}
