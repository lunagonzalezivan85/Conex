<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuth;
use App\Models\ApiTokenModel;
use App\Models\CandidatoModel;
use App\Models\EmpresaModel;
use App\Models\UserModel;

class AuthController extends BaseApiController
{
    /**
     * POST /api/login
     * Body: { usuario, password, device_name? }
     * El campo "usuario" acepta username o email (igual que la web).
     */
    public function login()
    {
        $rules = [
            'usuario'  => 'required',
            'password' => 'required',
        ];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $data = $this->body();
        $userModel = new UserModel();

        $user = $userModel->where('usuario', $data['usuario'])
            ->orWhere('email', $data['usuario'])
            ->first();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->fail('Credenciales invalidas', 401);
        }

        if ($user['estado'] !== 'activo') {
            return $this->fail('Tu cuenta esta ' . $user['estado'] . '. Contacta al administrador.', 403);
        }

        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        $tokenModel = new ApiTokenModel();
        $tokenData = $tokenModel->createToken(
            $user['id'],
            $data['device_name'] ?? 'mobile',
            $this->request->getIPAddress(),
            $this->request->getUserAgent()->getAgentString()
        );

        $roleSlug = $userModel->getRoleSlug($user['role_id']);

        return $this->ok([
            'token'      => $tokenData['token'],
            'token_type' => 'Bearer',
            'expires_at' => $tokenData['expires_at'],
            'user'       => $this->userPayload($user, $roleSlug),
        ]);
    }

    /**
     * POST /api/register
     * Body: { tipo_cuenta: candidato|empresa, nombre, apellido, usuario,
     *         email, telefono, password, password_confirm, razon_social? }
     */
    public function register()
    {
        $data = $this->body();
        $tipoCuenta = $data['tipo_cuenta'] ?? '';

        $rules = [
            'tipo_cuenta'      => 'required|in_list[candidato,empresa]',
            'nombre'           => 'required|min_length[2]|max_length[100]',
            'apellido'         => 'required|min_length[2]|max_length[100]',
            'usuario'          => 'required|min_length[3]|max_length[100]|is_unique[auth_user.usuario]',
            'email'            => 'required|valid_email|is_unique[auth_user.email]',
            'telefono'         => 'required|min_length[8]|max_length[20]',
            'password'         => 'required|min_length[10]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/]',
            'password_confirm' => 'required|matches[password]',
        ];
        if ($tipoCuenta === 'empresa') {
            $rules['razon_social'] = 'required|min_length[3]|max_length[150]';
        }

        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $userModel = new UserModel();
        $roleId = $userModel->getRoleIdBySlug($tipoCuenta);
        if (!$roleId) {
            return $this->fail('Tipo de cuenta no valido', 422);
        }

        // UserModel hashea el password automaticamente (beforeInsert)
        $userId = $userModel->insert([
            'role_id'         => $roleId,
            'nombre'          => $data['nombre'],
            'apellido'        => $data['apellido'],
            'usuario'         => $data['usuario'],
            'email'           => $data['email'],
            'telefono'        => $data['telefono'],
            'password'        => $data['password'],
            'estado'          => 'activo',
            'perfil_completo' => false,
        ], true);

        if (!$userId) {
            return $this->fail('No se pudo crear la cuenta', 500, $userModel->errors());
        }

        if ($tipoCuenta === 'candidato') {
            (new CandidatoModel())->insert([
                'user_id'   => $userId,
                'apellidos' => $data['apellido'],
            ]);
        } else {
            (new EmpresaModel())->insert([
                'user_id'      => $userId,
                'razon_social' => $data['razon_social'],
            ]);
        }

        // Login automatico: emitir token
        $tokenModel = new ApiTokenModel();
        $tokenData = $tokenModel->createToken(
            $userId,
            $data['device_name'] ?? 'mobile',
            $this->request->getIPAddress(),
            $this->request->getUserAgent()->getAgentString()
        );

        $user = $userModel->find($userId);
        unset($user['password']);

        return $this->ok([
            'token'      => $tokenData['token'],
            'token_type' => 'Bearer',
            'expires_at' => $tokenData['expires_at'],
            'user'       => $this->userPayload($user, $tipoCuenta),
        ], 201);
    }

    /**
     * POST /api/logout — revoca el token actual.
     */
    public function logout()
    {
        $token = ApiAuth::token();
        if ($token) {
            (new ApiTokenModel())->delete($token['id']);
        }
        return $this->ok(['message' => 'Sesion cerrada']);
    }

    /**
     * GET /api/me — usuario actual + perfil segun rol.
     */
    public function me()
    {
        $user = $this->authUser();
        $roleSlug = $this->roleSlug();

        $payload = $this->userPayload($user, $roleSlug);

        if ($roleSlug === 'candidato') {
            $payload['candidato'] = (new CandidatoModel())->where('user_id', $user['id'])->first();
        } elseif ($roleSlug === 'empresa') {
            $payload['empresa'] = (new EmpresaModel())->where('user_id', $user['id'])->first();
        }

        return $this->ok(['user' => $payload]);
    }

    private function userPayload(array $user, ?string $roleSlug): array
    {
        return [
            'id'              => (int)$user['id'],
            'nombre'          => $user['nombre'],
            'apellido'        => $user['apellido'],
            'usuario'         => $user['usuario'],
            'email'           => $user['email'],
            'telefono'        => $user['telefono'] ?? null,
            'avatar'          => $user['avatar'] ?? null,
            'role'            => $roleSlug,
            'perfil_completo' => (bool)($user['perfil_completo'] ?? false),
        ];
    }
}
