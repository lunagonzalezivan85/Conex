<?php

namespace App\Models;

use CodeIgniter\Model;

class CVModel extends Model
{
    protected $table = 'cand_cv';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'candidato_id', 'archivo_path', 'archivo_nombre', 'es_principal',
    ];

    protected $useTimestamps = true;
}
