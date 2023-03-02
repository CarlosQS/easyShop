<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Tienda;

class Producto extends Model
{
    use HasFactory;

    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'productos_tiendas', 'producto_id', 'tienda_id');
    }
}
