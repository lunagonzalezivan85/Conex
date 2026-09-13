<?php

namespace App\Models;

use CodeIgniter\Model;

class VerificacionProcesoModel extends Model
{
    protected $table = 'post_verificacion_proceso';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'postulacion_id', 'candidato_id', 'vacante_id',
        'asignado_a', 'paso_actual', 'estado', 'notas',
        'fecha_asignacion', 'fecha_contacto', 'fecha_documentos',
        'fecha_entrevista', 'fecha_evaluacion', 'fecha_presentacion',
    ];

    protected $useTimestamps = true;

    public const PASOS = [
        1 => 'Asignar a usuario',
        2 => 'Contactar al postulante',
        3 => 'Subir documentacion',
        4 => 'Entrevista con el postulante',
        5 => 'Evaluacion',
        6 => 'Presentar a empresa',
    ];
}
