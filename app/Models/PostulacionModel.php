<?php

namespace App\Models;

use CodeIgniter\Model;

class PostulacionModel extends Model
{
    protected $table = 'post_postulacion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'vacante_id', 'candidato_id', 'cv_id', 'mensaje', 'estado',
        'puntaje_match', 'fecha_entrevista', 'notas_empresa',
    ];

    protected $useTimestamps = true;
}
