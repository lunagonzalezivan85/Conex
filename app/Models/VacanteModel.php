<?php

namespace App\Models;

use CodeIgniter\Model;

class VacanteModel extends Model
{
    protected $table = 'vac_vacante';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'empresa_id', 'categoria_id', 'tipo_contrato_id', 'nivel_experiencia_id',
        'titulo', 'slug', 'descripcion', 'funciones', 'ubicacion', 'ciudad', 'region',
        'modalidad', 'salario_min', 'salario_max', 'moneda', 'anios_experiencia',
        'vacantes_disponibles', 'max_postulantes', 'estado', 'destacada',
        'fecha_publicacion', 'fecha_cierre',
    ];

    protected $useTimestamps = true;

    public function getVacantesWithRelations()
    {
        return $this->select('vac_vacante.*, emp_empresa.razon_social as empresa_nombre, emp_empresa.logo as empresa_logo, cat_categoria.nombre as categoria_nombre, cat_tipo_contrato.nombre as tipo_contrato_nombre')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->join('cat_categoria', 'cat_categoria.id = vac_vacante.categoria_id', 'left')
            ->join('cat_tipo_contrato', 'cat_tipo_contrato.id = vac_vacante.tipo_contrato_id', 'left');
    }
}
