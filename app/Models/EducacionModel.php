<?php

namespace App\Models;

use CodeIgniter\Model;

class EducacionModel extends Model
{
    protected $table = 'cand_educacion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'candidato_id', 'nivel_educacion_id', 'institucion', 'titulo',
        'fecha_inicio', 'fecha_fin', 'en_curso',
    ];

    protected $useTimestamps = true;
}
