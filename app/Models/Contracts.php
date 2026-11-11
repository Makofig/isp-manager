<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    use HasFactory;

    // nombre de la tabla
    protected $table = 'plan';

    protected $fillable = [
        'nombre',
        'megabytes',
        'costo',
    ];

    // Relación con el modelo cliente uno a muchos.
    public function clients()
    {
        return $this->hasMany(Client::class, 'id_plan');
    }
}
