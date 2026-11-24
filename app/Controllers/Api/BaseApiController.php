<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\ApiAuth;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Base para todos los controllers de la API.
 * Respuestas JSON consistentes + helpers de auth y validacion.
 */
class BaseApiController extends BaseController
{
    protected function ok($data = [], int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON($data);
    }

    protected function fail(string $message, int $code = 400, ?array $errors = null): ResponseInterface
    {
        $body = ['error' => $message];
        if ($errors) {
            $body['errors'] = $errors;
        }
        return $this->response->setStatusCode($code)->setJSON($body);
    }

    /**
     * Usuario autenticado por token (llenado por ApiAuthFilter).
     */
    protected function authUser(): array
    {
        return ApiAuth::user() ?? [];
    }

    protected function roleSlug(): ?string
    {
        return ApiAuth::roleSlug();
    }

    /**
     * Verifica que el usuario este autenticado.
     */
    protected function requireAuth(): ?ResponseInterface
    {
        if (empty($this->authUser()['id'])) {
            return $this->fail('Token de autenticacion requerido', 401);
        }
        return null;
    }

    /**
     * Verifica que el usuario tenga uno de los roles dados.
     */
    protected function requireRole(array $roles): ?ResponseInterface
    {
        if (!in_array($this->roleSlug(), $roles, true)) {
            return $this->fail('No tienes acceso a este recurso', 403);
        }
        return null;
    }

    /**
     * Lee el body del request como array (JSON o form).
     */
    protected function body(): array
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (\Throwable $e) {
            $json = null;
        }
        if (is_array($json) && !empty($json)) {
            return $json;
        }
        return $this->request->getPost() ?? [];
    }

    /**
     * Valida input (JSON o POST). Devuelve errores o null.
     */
    protected function validateApi(array $rules, ?array $data = null): ?ResponseInterface
    {
        $input = $data ?? $this->body();
        $validation = service('validation');
        $validation->setRules($rules);

        if (!$validation->run($input)) {
            return $this->fail('Datos invalidos', 422, $validation->getErrors());
        }
        return null;
    }

    /**
     * Paginacion estandar: ?page=1&per_page=20 (max 50)
     */
    protected function paginationParams(): array
    {
        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $perPage = min(50, max(1, (int)($this->request->getGet('per_page') ?? 12)));
        return [$page, $perPage];
    }

    /**
     * Payload publico del usuario autenticado (respuestas de login/perfil).
     * Compartido por Auth, Candidato y Empresa.
     */
    protected function userPayload(array $user, ?string $roleSlug): array
    {
        return [
            'id'              => (int)$user['id'],
            'nombre'          => $user['nombre'],
            'apellido'        => $user['apellido'],
            'usuario'         => $user['usuario'],
            'email'           => $user['email'],
            'telefono'        => $user['telefono'] ?? null,
            'avatar'          => $user['avatar'] ?? null,
            'avatar_url'      => !empty($user['avatar']) ? (str_starts_with($user['avatar'], 'http') ? $user['avatar'] : base_url('uploads/' . $user['avatar'])) : null,
            'role'            => $roleSlug,
            'perfil_completo' => (bool)($user['perfil_completo'] ?? false),
        ];
    }
}
