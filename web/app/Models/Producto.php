<?php
// Historia: H-01, H-02

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    // Tabla existente en MySQL — no crear migración
    protected $table      = 'producto';
    protected $primaryKey = 'id_producto';
    public    $timestamps = false; // sin created_at / updated_at
    // actualizado_en existe pero es manejado por MySQL ON UPDATE; no lo toca Laravel

    protected $fillable = [
        'sku',
        'nombre_comercial',
        'id_categoria',
        'id_unidad',
        'cantidad_disponible',
        'activo',
    ];

    protected $casts = [
        'activo'              => 'boolean',
        'cantidad_disponible' => 'integer',
        'actualizado_en'      => 'datetime',
    ];

    // FK: producto.id_categoria → categoria.id_categoria
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    // FK: producto.id_unidad → unidad_medida.id_unidad
    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidad', 'id_unidad');
    }

    public function ubicacion(): HasOne
    {
    return $this->hasOne(Ubicacion::class, 'id_producto');
    }
}
