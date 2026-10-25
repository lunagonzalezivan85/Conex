<?php

namespace App\Models;

use CodeIgniter\Model;

class SrvContactoModel extends Model
{
    protected $table = 'srv_contacto';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'servicio_id', 'user_id', 'nombre', 'telefono', 'mensaje',
        'ip_hash', 'estado',
    ];

    protected $useTimestamps = true;
}
