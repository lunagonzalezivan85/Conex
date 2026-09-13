<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model
{
    protected $table = 'emp_empresa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'user_id', 'razon_social', 'ruc', 'rubro', 'descripcion',
        'sitio_web', 'logo', 'telefono', 'direccion', 'ciudad', 'region', 'pais',
        'latitud', 'longitud', 'verificada',
    ];

    protected $useTimestamps = true;
}
