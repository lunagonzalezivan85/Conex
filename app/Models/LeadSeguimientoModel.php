<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadSeguimientoModel extends Model
{
    protected $table = 'crm_lead_seguimiento';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'lead_id', 'asesor_id', 'tipo_contacto', 'comentario', 'fecha_proxima_accion',
    ];

    protected $useTimestamps = true;

    public const TIPOS = [
        'llamada'   => 'Llamada',
        'email'     => 'Email',
        'reunion'   => 'Reunion',
        'whatsapp'  => 'WhatsApp',
        'visita'    => 'Visita',
        'otro'      => 'Otro',
    ];
}
