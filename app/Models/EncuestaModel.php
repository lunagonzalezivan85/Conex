<?php

namespace App\Models;

use CodeIgniter\Model;

class EncuestaModel extends Model
{
    protected $table = 'enc_encuesta';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'empresa_id', 'vacante_id', 'estado',
    ];

    protected $useTimestamps = true;
}
