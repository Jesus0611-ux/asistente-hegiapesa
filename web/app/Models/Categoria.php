<?php
// Historia: H-01

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    // Tabla existente en MySQL — no crear migración
    protected $table      = 'categoria';
    protected $primaryKey = 'id_categoria';
    public    $timestamps = false; // sin created_at / updated_at

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Un categoría agrupa muchos productos
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}
