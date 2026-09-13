<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentoPostulacionModel extends Model
{
    protected $table = 'post_documento';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'postulacion_id', 'candidato_id', 'requisito_id',
        'nombre_documento', 'archivo_path', 'archivo_nombre',
        'estado', 'comentario_verificacion', 'verificado_por', 'verificado_at',
    ];

    protected $useTimestamps = true;
}
