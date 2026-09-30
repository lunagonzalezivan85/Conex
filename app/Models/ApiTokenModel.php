<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiTokenModel extends Model
{
    protected $table = 'api_token';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id', 'nombre', 'token_hash', 'ip', 'user_agent',
        'last_used_at', 'expires_at',
    ];

    protected $useTimestamps = true;

    public const TOKEN_LENGTH = 64;
    public const TTL_DAYS = 30;

    /**
     * Crea un token nuevo. Devuelve el token en texto plano (solo se
     * muestra una vez, en la BD se guarda el hash SHA-256).
     */
    public function createToken(int $userId, string $nombre = 'mobile', ?string $ip = null, ?string $userAgent = null): array
    {
        $plain = bin2hex(random_bytes(self::TOKEN_LENGTH / 2)); // 64 chars hex
        $expiresAt = date('Y-m-d H:i:s', time() + self::TTL_DAYS * 86400);

        $this->insert([
            'user_id'    => $userId,
            'nombre'     => $nombre,
            'token_hash' => hash('sha256', $plain),
            'ip'         => $ip,
            'user_agent' => substr((string)$userAgent, 0, 255),
            'expires_at' => $expiresAt,
        ]);

        return [
            'id'         => $this->getInsertID(),
            'token'      => $plain,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Busca un token valido (no expirado) a partir del texto plano.
     */
    public function findValidByToken(string $plain): ?array
    {
        $record = $this->where('token_hash', hash('sha256', $plain))->first();
        if (!$record) {
            return null;
        }
        if (!empty($record['expires_at']) && strtotime($record['expires_at']) < time()) {
            return null;
        }
        return $record;
    }

    public function touch(int $id, ?string $ip = null): void
    {
        $this->update($id, [
            'last_used_at' => date('Y-m-d H:i:s'),
            'ip'           => $ip,
        ]);
    }

    /**
     * Revoca todos los tokens de un usuario (ej: cambio de password).
     */
    public function revokeAll(int $userId): void
    {
        $this->where('user_id', $userId)->delete();
    }
}
