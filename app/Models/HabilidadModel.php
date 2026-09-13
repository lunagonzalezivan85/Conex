<?php

namespace App\Models;

use CodeIgniter\Model;

class HabilidadModel extends Model
{
    protected $table = 'cand_habilidad';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'candidato_id', 'habilidad_id', 'nivel',
    ];

    protected $useTimestamps = false;
}
