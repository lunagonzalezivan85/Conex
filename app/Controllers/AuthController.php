<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CandidatoModel;
use App\Models\EmpresaModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('layouts/publico', [
            'content' => view('auth/login'),
            'css' => ['auth.css'],
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'usuario' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');

        $user = $userModel->where('usuario', $usuario)
            ->orWhere('email', $usuario)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Credenciales invalidas.');
        }

        if ($user['estado'] !== 'activo') {
            return redirect()->back()->withInput()->with('error', 'Tu cuenta esta ' . $user['estado'] . '. Contacta al administrador.');
        }

        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        $this->session->set([
            'user_id' => $user['id'],
            'nombre' => $user['nombre'],
            'apellido' => $user['apellido'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'isLoggedIn' => true,
        ]);

        $roleSlug = $userModel->getRoleSlug($user['role_id']);

        return match ($roleSlug) {
            'admin', 'asesor' => redirect()->to('admin'),
            'empresa' => redirect()->to('empresa'),
            'candidato' => redirect()->to('candidato'),
            default => redirect()->to(''),
        };
    }

    public function registro(): string
    {
        return view('layouts/publico', [
            'content' => view('auth/registro'),
            'css' => ['auth.css'],
            'js' => ['auth.js'],
        ]);
    }

    public function attemptRegistro()
    {
        $tipoCuenta = $this->request->getPost('tipo_cuenta');

        $rules = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'usuario' => 'required|min_length[3]|max_length[100]|is_unique[auth_user.usuario]',
            'email' => 'required|valid_email|is_unique[auth_user.email]',
            'telefono' => 'required|min_length[8]|max_length[20]',
            'password' => 'required|min_length[10]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/]',
            'password_confirm' => 'required|matches[password]',
            'tipo_cuenta' => 'required|in_list[candidato,empresa]',
        ];

        if ($tipoCuenta === 'empresa') {
            $rules['razon_social'] = 'required|min_length[3]|max_length[150]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $empresaModel = new EmpresaModel();

        $roleSlug = $tipoCuenta === 'candidato' ? 'candidato' : 'empresa';
        $roleId = $userModel->getRoleIdBySlug($roleSlug);

        $userData = [
            'role_id' => $roleId,
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'usuario' => $this->request->getPost('usuario'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'estado' => 'activo',
            'perfil_completo' => false,
        ];

        $userId = $userModel->insert($userData, true);

        if ($tipoCuenta === 'candidato') {
            $candidatoModel->insert([
                'user_id' => $userId,
                'apellidos' => $this->request->getPost('apellido'),
                'porcentaje_perfil' => 0,
            ]);
        } else {
            $empresaModel->insert([
                'user_id' => $userId,
                'razon_social' => $this->request->getPost('razon_social'),
            ]);
        }

        $this->session->set([
            'user_id' => $userId,
            'nombre' => $userData['nombre'],
            'apellido' => $userData['apellido'],
            'email' => $userData['email'],
            'role_id' => $roleId,
            'isLoggedIn' => true,
        ]);

        return match ($roleSlug) {
            'empresa' => redirect()->to('empresa'),
            'candidato' => redirect()->to('candidato'),
            default => redirect()->to(''),
        };
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('');
    }

    public function googleRedirect()
    {
        // TODO: Implementar Google OAuth
        // Pasos documentados en docs/AUTH.md
        return redirect()->to('login')->with('error', 'Login con Google estara disponible proximamente.');
    }

    public function googleCallback()
    {
        // TODO: Implementar Google OAuth callback
        return redirect()->to('login')->with('error', 'Login con Google estara disponible proximamente.');
    }

    public function verificar($token)
    {
        // TODO: Activar cuando tengamos servicio de email/SMS
        // Por ahora las cuentas se crean activas
        return redirect()->to('login')->with('info', 'La verificacion por correo estara disponible proximamente.');
    }
}
