<?php

namespace App\Models;

use CodeIgniter\Model;

class SrvReporteModel extends Model
{
    protected $table = 'srv_reporte';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'servicio_id', 'motivo', 'detalle', 'ip_hash',
    ];

    protected $useTimestamps = true;
    protected $updatedField = '';
}
