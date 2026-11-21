<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuth;
use App\Libraries\AccountPurgeService;
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

        $usuario = trim((string)($data['usuario'] ?? ''));
        $password = (string)($data['password'] ?? '');
        $cedulaNorm = strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $usuario));
        $telefonoNorm = preg_replace('/\D/', '', $usuario);

        $builder = $userModel->groupStart()
            ->where('usuario', $usuario)
            ->orWhere('email', $usuario);
        if (!empty($cedulaNorm)) {
            $builder->orWhere('usuario', $cedulaNorm);
        }
        if (strlen($telefonoNorm) >= 8) {
            $builder->orWhere('telefono', $usuario);
        }
        $user = $builder->groupEnd()->first();

        $passValid = false;
        if ($user) {
            $passValid = password_verify($password, $user['password']) || password_verify(trim($password), $user['password']);
        }

        if (!$user || !$passValid) {
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
     * POST /api/auth/google
     * Body: { id_token: string, device_name?: string }
     * Valida el token con Google OAuth2 y autentica/registra al usuario.
     */
    public function googleLogin()
    {
        $rules = [
            'id_token' => 'required',
        ];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $data = $this->body();
        $idToken = trim($data['id_token']);

        // Validar token con el endpoint oficial de Google OAuth2
        $googleUser = $this->verifyGoogleToken($idToken);
        if (!$googleUser) {
            return $this->fail('Token de Google no valido o expirado', 401);
        }

        $email = strtolower(trim($googleUser['email'] ?? ''));
        if (empty($email) || empty($googleUser['email_verified'])) {
            return $this->fail('El correo de Google no esta verificado', 400);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user) {
            // Usuario existente
            if ($user['estado'] !== 'activo') {
                return $this->fail('Tu cuenta esta ' . $user['estado'] . '. Contacta al administrador.', 403);
            }

            // Actualizar last_login y avatar si no tiene
            $updateData = ['last_login' => date('Y-m-d H:i:s')];
            if (empty($user['avatar']) && !empty($googleUser['picture'])) {
                $updateData['avatar'] = $googleUser['picture'];
            }
            $userModel->update($user['id'], $updateData);
            $user = $userModel->find($user['id']);
            $roleSlug = $userModel->getRoleSlug($user['role_id']);
        } else {
            // Nuevo usuario registrado via Google -> Rol candidato
            $roleId = $userModel->getRoleIdBySlug('candidato');
            if (!$roleId) {
                return $this->fail('Error en configuracion del sistema', 500);
            }

            // Generar un nombre de usuario unico a partir del correo o nombre
            $baseUser = explode('@', $email)[0];
            $baseUser = preg_replace('/[^a-zA-Z0-9_]/', '', $baseUser) ?: 'user';
            $uniqueUser = $baseUser;
            $counter = 1;
            while ($userModel->where('usuario', $uniqueUser)->first()) {
                $uniqueUser = $baseUser . $counter++;
            }

            $nombre = $googleUser['given_name'] ?? ($googleUser['name'] ?? 'Usuario');
            $apellido = $googleUser['family_name'] ?? '';
            $avatar = $googleUser['picture'] ?? null;

            // Password aleatorio seguro de 32 caracteres (acceso via Google)
            $randomPassword = bin2hex(random_bytes(16));

            $userId = $userModel->insert([
                'role_id'         => $roleId,
                'nombre'          => $nombre,
                'apellido'        => $apellido,
                'usuario'         => $uniqueUser,
                'email'           => $email,
                'telefono'        => null,
                'password'        => $randomPassword,
                'avatar'          => $avatar,
                'estado'          => 'activo',
                'perfil_completo' => false,
                'last_login'      => date('Y-m-d H:i:s'),
            ], true);

            if (!$userId) {
                return $this->fail('No se pudo registrar la cuenta con Google', 500, $userModel->errors());
            }

            // Crear registro en tabla candidatos
            (new CandidatoModel())->insert([
                'user_id'   => $userId,
                'apellidos' => $apellido,
            ]);

            $user = $userModel->find($userId);
            $roleSlug = 'candidato';
        }

        // Generar token de sesion API
        $tokenModel = new ApiTokenModel();
        $tokenData = $tokenModel->createToken(
            $user['id'],
            $data['device_name'] ?? 'google-auth',
            $this->request->getIPAddress(),
            $this->request->getUserAgent()->getAgentString()
        );

        return $this->ok([
            'token'      => $tokenData['token'],
            'token_type' => 'Bearer',
            'expires_at' => $tokenData['expires_at'],
            'user'       => $this->userPayload($user, $roleSlug),
        ]);
    }

    /**
     * Valida el token con Google OAuth2 TokenInfo API.
     */
    private function verifyGoogleToken(string $idToken): ?array
    {
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['sub'])) {
            return null;
        }

        // Validar emisor y expiracion
        $iss = $data['iss'] ?? '';
        if ($iss !== 'accounts.google.com' && $iss !== 'https://accounts.google.com') {
            return null;
        }

        if (isset($data['exp']) && (int)$data['exp'] < time()) {
            return null;
        }

        // Convertir boolean string si viene como texto
        if (isset($data['email_verified'])) {
            $data['email_verified'] = ($data['email_verified'] === true || $data['email_verified'] === 'true');
        }

        return $data;
    }

    /**
     * POST /api/register
     * Body: { tipo_cuenta: candidato|empresa, nombre, apellido, usuario,
     *         email, telefono, password, password_confirm, razon_social? }
     * Si llega `cedula` → flujo de la app (wizard): cedula=usuario, PIN=password.
     */
    public function register()
    {
        $data = $this->body();
        $tipoCuenta = $data['tipo_cuenta'] ?? (!empty($data['razon_social']) ? 'empresa' : 'candidato');

        // Flujo express app para candidato con cedula
        if (!empty($data['cedula']) && $tipoCuenta === 'candidato') {
            return $this->registerApp($data);
        }

        $userModel = new UserModel();

        if ($tipoCuenta === 'empresa') {
            $rules = [
                'razon_social'     => 'required|min_length[3]|max_length[150]',
                'nombre'           => 'required|min_length[2]|max_length[100]',
                'apellido'         => 'required|min_length[2]|max_length[100]',
                'email'            => 'required|valid_email|is_unique[auth_user.email]',
                'telefono'         => 'required|min_length[8]|max_length[20]',
                'password'         => 'required|min_length[6]|max_length[100]',
                'ruc'              => 'permit_empty|max_length[50]',
                'rubro'            => 'permit_empty|max_length[100]',
                'ciudad'           => 'permit_empty|max_length[100]',
            ];
            if ($res = $this->validateApi($rules)) {
                return $res;
            }

            $roleId = $userModel->getRoleIdBySlug('empresa');
            if (!$roleId) {
                return $this->fail('Tipo de cuenta no valido', 422);
            }

            // Generar usuario base si no viene especificado
            $baseUsuario = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $data['email'])[0]));
            if (empty($baseUsuario) || strlen($baseUsuario) < 3) {
                $baseUsuario = 'empresa' . rand(100, 999);
            }
            $usuario = $data['usuario'] ?? ($baseUsuario . rand(10, 99));

            // Asegurar unicidad de usuario
            while ($userModel->where('usuario', $usuario)->first()) {
                $usuario = $baseUsuario . rand(100, 9999);
            }

            $userId = $userModel->insert([
                'role_id'         => $roleId,
                'nombre'          => $data['nombre'],
                'apellido'        => $data['apellido'],
                'usuario'         => $usuario,
                'email'           => $data['email'],
                'telefono'        => $data['telefono'],
                'password'        => $data['password'],
                'estado'          => 'activo',
                'perfil_completo' => false,
            ], true);

            if (!$userId) {
                return $this->fail('No se pudo crear la cuenta de empresa', 500, $userModel->errors());
            }

            $empresaModel = new EmpresaModel();
            $empresaId = $empresaModel->insert([
                'user_id'      => $userId,
                'razon_social' => $data['razon_social'],
                'ruc'          => !empty($data['ruc']) ? trim($data['ruc']) : null,
                'rubro'        => !empty($data['rubro']) ? trim($data['rubro']) : null,
                'ciudad'       => !empty($data['ciudad']) ? trim($data['ciudad']) : null,
                'telefono'     => $data['telefono'],
                'verificada'   => 0,
            ], true);

            // Activacion de 1 mes de prueba gratis (30 dias)
            $db = \Config\Database::connect();
            $db->table('crm_contrato')->insert([
                'empresa_id'       => $empresaId,
                'plan_id'          => 1, // Plan gratis / prueba
                'codigo'           => 'TRIAL-' . $empresaId . '-' . date('Ymd'),
                'tipo_facturacion' => 'mensual',
                'fecha_inicio'     => date('Y-m-d'),
                'fecha_fin'        => date('Y-m-d', strtotime('+30 days')),
                'estado'           => 'activo',
                'auto_renovar'     => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            // Asignacion automatica de lead en CRM
            $asesor = $userModel->where('role_id', 2)->where('estado', 'activo')->first();
            $asesorId = $asesor ? (int)$asesor['id'] : 5;

            $db->table('crm_lead')->insert([
                'empresa_id'        => $empresaId,
                'tipo'              => 'registro_app',
                'nombre_contacto'   => trim($data['nombre'] . ' ' . $data['apellido']),
                'email_contacto'    => $data['email'],
                'telefono_contacto' => $data['telefono'],
                'empresa_nombre'    => $data['razon_social'],
                'rubro'             => $data['rubro'] ?? null,
                'origen'            => 'app_mobile',
                'plan_interes_id'   => 1,
                'estado'            => 'prospecto',
                'asesor_id'         => $asesorId,
                'notas'             => 'Registrado desde la app móvil CONEX. 1 mes de prueba gratis activado (5 vacantes, 8 candidatos en USD).',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            // Login automatico: emitir token
            $tokenModel = new ApiTokenModel();
            $tokenData = $tokenModel->createToken(
                $userId,
                $data['device_name'] ?? 'conex-app',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()->getAgentString()
            );

            $user = $userModel->find($userId);
            unset($user['password']);

            return $this->ok([
                'message'    => 'Cuenta de empresa creada con 1 mes de prueba gratis activado.',
                'token'      => $tokenData['token'],
                'token_type' => 'Bearer',
                'expires_at' => $tokenData['expires_at'],
                'user'       => $this->userPayload($user, 'empresa'),
            ], 201);
        }

        // Flujo estandar web para candidato
        $rules = [
            'tipo_cuenta'      => 'required|in_list[candidato,empresa]',
            'nombre'           => 'required|min_length[2]|max_length[100]',
            'apellido'         => 'required|min_length[2]|max_length[100]',
            'usuario'          => 'required|min_length[3]|max_length[100]|is_unique[auth_user.usuario]',
            'email'            => 'required|valid_email|is_unique[auth_user.email]',
            'telefono'         => 'required|min_length[8]|max_length[20]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $roleId = $userModel->getRoleIdBySlug($tipoCuenta);
        if (!$roleId) {
            return $this->fail('Tipo de cuenta no valido', 422);
        }

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

        (new CandidatoModel())->insert([
            'user_id'   => $userId,
            'apellidos' => $data['apellido'],
        ]);

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
     * Registro desde la app (wizard de cedula).
     * Body: { cedula, nombre, apellido, telefono, pin, email?, terminos, device_name? }
     * usuario = cedula | password = PIN de 4 digitos | email opcional (placeholder si falta).
     * La persona puede no saber usar un telefono: sin correo ni contrasena segura.
     */
    private function registerApp(array $data)
    {
        // Normalizar cedula/documento (alfanumerico en mayusculas, permite letras de IDs de cualquier pais)
        if (isset($data['cedula'])) {
            $data['cedula'] = strtoupper(preg_replace('/[^0-9A-Za-z]/', '', (string) $data['cedula']));
        }

        $rules = [
            'cedula'   => 'required|min_length[5]|max_length[30]|is_unique[auth_user.usuario]',
            'nombre'   => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'telefono' => 'required|min_length[8]|max_length[20]',
            'pin'      => 'required|numeric|exact_length[4]',
            'email'    => 'permit_empty|valid_email|is_unique[auth_user.email]',
            'terminos' => 'required',
        ];

        $validation = service('validation');
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->fail('Datos invalidos', 422, $validation->getErrors());
        }

        $userModel = new UserModel();
        $roleId = $userModel->getRoleIdBySlug('candidato');
        if (!$roleId) {
            return $this->fail('Tipo de cuenta no valido', 422);
        }

        $cedula = $data['cedula'];
        $email  = trim((string) ($data['email'] ?? '')) ?: $cedula . '@conex.local';

        // UserModel hashea el password automaticamente (beforeInsert)
        $userId = $userModel->insert([
            'role_id'         => $roleId,
            'nombre'          => $data['nombre'],
            'apellido'        => $data['apellido'],
            'usuario'         => $cedula,
            'email'           => $email,
            'telefono'        => $data['telefono'],
            'password'        => (string) $data['pin'],
            'estado'          => 'activo',
            'perfil_completo' => false,
        ], true);

        if (!$userId) {
            return $this->fail('No se pudo crear la cuenta', 500, $userModel->errors());
        }

        (new CandidatoModel())->insert([
            'user_id'   => $userId,
            'apellidos' => $data['apellido'],
        ]);

        // Login automatico: emitir token
        $tokenModel = new ApiTokenModel();
        $tokenData = $tokenModel->createToken(
            $userId,
            $data['device_name'] ?? 'conex-app',
            $this->request->getIPAddress(),
            $this->request->getUserAgent()->getAgentString()
        );

        $user = $userModel->find($userId);
        unset($user['password']);

        return $this->ok([
            'token'      => $tokenData['token'],
            'token_type' => 'Bearer',
            'expires_at' => $tokenData['expires_at'],
            'user'       => $this->userPayload($user, 'candidato'),
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
     * POST /api/usuario/eliminar-cuenta — baja y borrado definitivo de cuenta del usuario actual.
     * En estricto cumplimiento de la Cláusula 6 de la Política de Privacidad de CONEX.
     */
    public function eliminarCuenta()
    {
        $user = $this->authUser();
        if (empty($user['id'])) {
            return $this->fail('Token de autenticacion requerido', 401);
        }

        $userId = (int)$user['id'];
        $res = AccountPurgeService::purgeUser($userId, 'Baja y eliminación voluntaria desde la app móvil');

        if (!$res['success']) {
            return $this->fail($res['error'] ?? 'No se pudo procesar la eliminación', 500);
        }

        return $this->ok([
            'message' => 'Tu cuenta y todos tus datos personales asociados han sido eliminados de forma definitiva.',
            'summary' => $res['summary'] ?? [],
        ]);
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

    /**
     * POST /api/admin/usuarios/{id}/reset-password
     */
    public function adminResetPassword(int $userId)
    {
        if ($res = $this->requireRole(['admin', 'asesor'])) {
            return $res;
        }

        $userModel = new UserModel();
        $targetUser = $userModel->find($userId);
        if (!$targetUser) {
            return $this->fail('Usuario no encontrado', 404);
        }

        $data = $this->body();
        $newPassword = trim((string)($data['new_password'] ?? ''));
        $autoGenerated = false;

        if ($newPassword === '') {
            $prefix = 'Conex';
            $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
            $randStr = substr(str_shuffle($chars), 0, 4);
            $newPassword = $prefix . date('y') . '!' . $randStr;
            $autoGenerated = true;
        } elseif (strlen($newPassword) < 4) {
            return $this->fail('La contraseña debe tener al menos 4 caracteres', 422);
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $db = \Config\Database::connect();
        $db->table('auth_user')->where('id', $userId)->update([
            'password'   => $hashed,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('api_token')->where('user_id', $userId)->delete();

        return $this->ok([
            'message'        => 'Contraseña restablecida exitosamente',
            'user_id'        => $userId,
            'usuario'        => $targetUser['usuario'],
            'new_password'   => $newPassword,
            'auto_generated' => $autoGenerated,
        ]);
    }

    /**
     * GET /api/notificaciones — lista de notificaciones del usuario y contador de no leidas.
     */
    public function notificaciones()
    {
        $user = $this->authUser();
        if (!$user) {
            return $this->fail('Token requerido', 401);
        }
        $db = \Config\Database::connect();
        if (!$db->tableExists('not_notificacion')) {
            return $this->ok(['unread_count' => 0, 'notificaciones' => []]);
        }
        $notificaciones = $db->table('not_notificacion')
            ->where('user_id', $user['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(30)
            ->get()->getResultArray();

        $unread = 0;
        foreach ($notificaciones as $n) {
            if ((int)$n['leida'] === 0) $unread++;
        }

        return $this->ok([
            'unread_count'   => $unread,
            'notificaciones' => $notificaciones,
        ]);
    }

    /**
     * POST /api/notificaciones/leer — marca todas las notificaciones como leidas.
     */
    public function leerNotificaciones()
    {
        $user = $this->authUser();
        if (!$user) {
            return $this->fail('Token requerido', 401);
        }
        $db = \Config\Database::connect();
        if ($db->tableExists('not_notificacion')) {
            $db->table('not_notificacion')
                ->where('user_id', $user['id'])
                ->update(['leida' => 1]);
        }
        return $this->ok(['ok' => true]);
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
            'avatar_url'      => !empty($user['avatar']) ? (str_starts_with($user['avatar'], 'http') ? $user['avatar'] : base_url('uploads/' . $user['avatar'])) : null,
            'role'            => $roleSlug,
            'perfil_completo' => (bool)($user['perfil_completo'] ?? false),
        ];
    }
}
