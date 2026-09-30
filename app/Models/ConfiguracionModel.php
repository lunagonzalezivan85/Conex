<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table = 'cfg_configuracion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = ['clave', 'valor'];

    protected $useTimestamps = true;

    /**
     * Devuelve todas las configuraciones como array clave => valor
     */
    public function getAll(): array
    {
        $rows = $this->findAll();
        $out = [];
        foreach ($rows as $r) {
            $out[$r['clave']] = $r['valor'];
        }
        return $out;
    }

    /**
     * Guarda o actualiza una clave
     */
    public function setValor(string $clave, ?string $valor): void
    {
        $existing = $this->where('clave', $clave)->first();
        if ($existing) {
            $this->update($existing['id'], ['valor' => $valor]);
        } else {
            $this->insert(['clave' => $clave, 'valor' => $valor]);
        }
    }
}
