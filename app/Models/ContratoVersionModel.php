<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratoVersionModel extends Model
{
    protected $table = 'crm_contrato_version';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'contrato_id', 'version', 'plan_id_anterior', 'plan_id_nuevo',
        'tipo_facturacion_anterior', 'tipo_facturacion_nuevo',
        'precio_anterior', 'precio_nuevo', 'motivo', 'modificado_por',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
