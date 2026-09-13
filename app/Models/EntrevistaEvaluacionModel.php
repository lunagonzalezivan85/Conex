<?php

namespace App\Models;

use CodeIgniter\Model;

class EntrevistaEvaluacionModel extends Model
{
    protected $table = 'post_entrevista_evaluacion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'proceso_id', 'criterio', 'calificacion', 'observacion',
    ];

    protected $useTimestamps = true;

    public const CRITERIOS = [
        'porte_aspecto' => 'Porte y aspecto',
        'comunicacion' => 'Comunicacion verbal y expresion',
        'puntualidad' => 'Puntualidad y actitud',
        'experiencia' => 'Experiencia y conocimientos',
        'motivacion' => 'Motivacion e interes',
    ];

    public const CALIFICACIONES = [
        0 => 'Sin calificar',
        1 => 'Malo',
        2 => 'Regular',
        3 => 'Bueno',
        4 => 'Muy bueno',
        5 => 'Excelente',
    ];
}
