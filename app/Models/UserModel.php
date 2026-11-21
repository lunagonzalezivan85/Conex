<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'auth_user';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'role_id', 'nombre', 'apellido', 'usuario', 'email', 'password',
        'telefono', 'avatar', 'estado', 'perfil_completo', 'last_login',
    ];

    protected $useTimestamps = true;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $pass = (string) $data['data']['password'];
            // Si ya es un hash bcrypt ($2y$, $2a$, $2b$), evitar re-hashear
            if (!preg_match('/^\$2[ayb]\$\d{2}\$[A-Za-z0-9\.\/]{53}$/', $pass)) {
                $data['data']['password'] = password_hash($pass, PASSWORD_BCRYPT);
            }
        }
        return $data;
    }

    public function getRoleSlug(int $roleId): ?string
    {
        $row = $this->db->table('auth_role')->where('id', $roleId)->get()->getRowArray();
        return $row['slug'] ?? null;
    }

    public function getRoleIdBySlug(string $slug): ?int
    {
        $row = $this->db->table('auth_role')->where('slug', $slug)->get()->getRowArray();
        return $row['id'] ?? null;
    }
}
