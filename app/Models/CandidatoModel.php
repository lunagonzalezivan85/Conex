<?php

namespace App\Models;

use CodeIgniter\Model;

class CandidatoModel extends Model
{
    protected $table = 'cand_candidato';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'user_id', 'apellidos', 'fecha_nacimiento', 'profesion',
        'nivel_experiencia_id', 'sobre_mi', 'direccion', 'ciudad', 'region', 'pais',
        'latitud', 'longitud', 'disponibilidad', 'modalidad_preferida',
        'salario_esperado_usd', 'portafolio_url', 'linkedin_url', 'porcentaje_perfil',
    ];

    protected $useTimestamps = true;
}
