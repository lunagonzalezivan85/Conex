<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'crm_lead';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'empresa_id', 'tipo', 'nombre_contacto', 'email_contacto', 'telefono_contacto',
        'empresa_nombre', 'rubro', 'tamano_empresa', 'origen',
        'plan_interes_id', 'estado', 'asesor_id', 'notas',
    ];

    protected $useTimestamps = true;

    public const ESTADOS = [
        'prospecto' => 'Prospecto',
        'contacto'  => 'Contacto',
        'union'     => 'Union',
        'propuesta' => 'Propuesta',
        'cerrado'   => 'Cerrado',
        'perdido'   => 'Perdido',
    ];

    public const ORIGENES = [
        'web'          => 'Sitio web',
        'feria_laboral' => 'Feria laboral',
        'referido'     => 'Referido',
        'campana'      => 'Campana',
        'redes'        => 'Redes sociales',
        'directo'      => 'Contacto directo',
        'otro'         => 'Otro',
    ];
}
