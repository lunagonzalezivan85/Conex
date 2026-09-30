<?php

namespace App\Filters;

use App\Libraries\ApiAuth;
use App\Models\ApiTokenModel;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Autenticacion por Bearer token para la API.
 * El token en texto plano viaja en el header Authorization;
 * en BD solo se guarda su hash SHA-256.
 */
class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'Token de autenticacion requerido',
            ]);
        }

        $tokenModel = new ApiTokenModel();
        $token = $tokenModel->findValidByToken($m[1]);

        if (!$token) {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'Token invalido o expirado',
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($token['user_id']);

        if (!$user || $user['estado'] !== 'activo') {
            return service('response')->setStatusCode(401)->setJSON([
                'error' => 'Cuenta inactiva o inexistente',
            ]);
        }

        $tokenModel->touch($token['id'], $request->getIPAddress());
        unset($user['password']);

        ApiAuth::login($user, $token, $userModel->getRoleSlug($user['role_id']));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada que hacer
    }
}
