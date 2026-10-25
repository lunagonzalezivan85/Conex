<?php

namespace App\Models;

use CodeIgniter\Model;

class CandServicioModel extends Model
{
    protected $table = 'srv_servicio';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'candidato_id', 'oficio', 'descripcion', 'precio_desde', 'precio_tipo',
        'ciudad', 'zona', 'estado', 'destacado', 'contactos',
    ];

    protected $useTimestamps = true;
}
