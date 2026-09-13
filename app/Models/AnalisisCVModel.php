<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalisisCVModel extends Model
{
    protected $table = 'ai_analisis_cv';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'candidato_id', 'cv_id', 'postulacion_id', 'vacante_id',
        'prompt', 'respuesta', 'score_match', 'fortalezas',
        'debilidades', 'recomendacion', 'modelo_ia',
        'tokens_usados', 'tiempo_respuesta_ms', 'estado', 'error',
    ];

    protected $useTimestamps = true;
}
