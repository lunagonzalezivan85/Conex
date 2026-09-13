<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Debes iniciar sesion para acceder.');
        }

        if (!empty($arguments)) {
            $allowedRoles = $arguments;
            $userRoleSlug = $session->get('role_slug');

            if (!$userRoleSlug) {
                $userModel = new \App\Models\UserModel();
                $userRoleSlug = $userModel->getRoleSlug($session->get('role_id'));
                $session->set('role_slug', $userRoleSlug);
            }

            if (!in_array($userRoleSlug, $allowedRoles)) {
                return redirect()->to('')->with('error', 'No tienes permisos para acceder a esta seccion.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
