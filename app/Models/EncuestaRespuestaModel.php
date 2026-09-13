<?php

namespace App\Models;

use CodeIgniter\Model;

class EncuestaRespuestaModel extends Model
{
    protected $table = 'enc_respuesta';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'encuesta_id', 'postulacion_id', 'candidato_id',
        'puntualidad', 'profesionalismo', 'aptitud_tecnica',
        'comunicacion', 'recomendacion', 'comentario',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
