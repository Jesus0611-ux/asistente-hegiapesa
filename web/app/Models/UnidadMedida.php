<?php
// Historia: H-01

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    // Tabla existente en MySQL — no crear migración
    protected $table      = 'unidad_medida';
    protected $primaryKey = 'id_unidad';
    public    $timestamps = false; // sin created_at / updated_at

    protected $fillable = [
        'simbolo',
        'descripcion',
    ];

    // Una unidad de medida se aplica a muchos productos
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_unidad', 'id_unidad');
    }
}
