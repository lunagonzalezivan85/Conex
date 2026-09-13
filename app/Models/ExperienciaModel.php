<?php

namespace App\Models;

use CodeIgniter\Model;

class ExperienciaModel extends Model
{
    protected $table = 'cand_experiencia';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'candidato_id', 'empresa', 'cargo', 'descripcion',
        'fecha_inicio', 'fecha_fin', 'actual',
    ];

    protected $useTimestamps = true;
}
