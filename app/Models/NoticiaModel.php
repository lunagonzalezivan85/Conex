<?php

namespace App\Models;

use CodeIgniter\Model;

class NoticiaModel extends Model
{
    protected $table = 'not_noticia';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['titulo', 'slug', 'resumen', 'contenido', 'imagen', 'estado'];

    protected $useTimestamps = true;

    public function findBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }
}
