<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadServicioModel extends Model
{
    protected $table = 'crm_lead_servicio';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'lead_id', 'servicio', 'cantidad', 'precio', 'incluido',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    public const SERVICIOS = [
        'destacar_vacantes'      => 'Destacar vacantes',
        'candidatos_destacados'  => 'Candidatos destacados',
        'verificacion_express'   => 'Verificacion express',
        'analisis_cv_ia'         => 'Analisis de CV con IA',
        'multi_usuario'          => 'Multi-usuario',
        'soporte_prioritario'    => 'Soporte prioritario',
    ];
}
