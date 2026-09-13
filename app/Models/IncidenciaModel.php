<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidenciaModel extends Model
{
    protected $table = 'inc_incidencia';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'empresa_id', 'user_id', 'titulo', 'descripcion',
        'categoria', 'prioridad', 'estado', 'respuesta',
        'respondido_por', 'respondido_at',
    ];

    protected $useTimestamps = true;
}
