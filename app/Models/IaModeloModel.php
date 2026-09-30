<?php

namespace App\Models;

use CodeIgniter\Model;

class IaModeloModel extends Model
{
    protected $table = 'cfg_ia_modelo';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = ['nombre', 'proveedor', 'url', 'modelo', 'api_key', 'proposito', 'activo'];

    protected $useTimestamps = true;

    public const PROPOSITOS = [
        'analisis_cv' => 'Analisis de CV',
        'matching' => 'Matching candidato-vacante',
        'chatbot' => 'Chatbot / asistente',
        'general' => 'Uso general',
    ];

    public function getModeloPorProposito(string $proposito): ?array
    {
        return $this->where('proposito', $proposito)
            ->where('activo', 1)
            ->orderBy('id', 'DESC')
            ->first();
    }
}
