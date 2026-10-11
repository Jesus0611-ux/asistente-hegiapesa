<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ubicacion extends Model
{
    protected $table = 'ubicacion';

    protected $primaryKey = 'id_ubicacion';

    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'numero_pasillo',
        'codigo_estante',
        'observacion',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
