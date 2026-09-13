<?php

namespace App\Models;

use CodeIgniter\Model;

class IdiomaModel extends Model
{
    protected $table = 'cand_idioma';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'candidato_id', 'idioma_id', 'nivel',
    ];

    protected $useTimestamps = false;
}
