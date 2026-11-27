<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombre_usuario',
        'hash_contrasena',
        'id_rol',
        'activo',
    ];

    protected $hidden = [
        'hash_contrasena',
    ];

    public function getAuthPasswordName()
    {
        return 'hash_contrasena';
    }

    public function getAuthPassword()
    {
        return $this->hash_contrasena;
    }
}