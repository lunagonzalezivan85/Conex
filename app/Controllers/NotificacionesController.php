<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Notificaciones web — paridad con la app (GET /api/notificaciones).
 * Lista las notificaciones del usuario logueado y permite marcarlas leidas.
 */
class NotificacionesController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userId = (int)session()->get('user_id');
        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug((int)session()->get('role_id')) ?? 'candidato';

        $db = \Config\Database::connect();
        $notificaciones = $db->tableExists('not_notificacion')
            ? $db->table('not_notificacion')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->get()->getResultArray()
            : [];

        $sidebarSections = $this->sidebarSections($roleSlug);

        $content = view('notificaciones/lista', ['notificaciones' => $notificaciones]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Notificaciones',
            'pageTitle'       => 'Notificaciones',
            'roleSlug'        => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection'   => 'notificaciones',
        ]);
    }

    /** POST /notificaciones/leer — marca todas como leidas y vuelve. */
    public function leer()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        $userId = (int)session()->get('user_id');
        $db = \Config\Database::connect();
        if ($db->tableExists('not_notificacion')) {
            $db->table('not_notificacion')
                ->where('user_id', $userId)
                ->where('leida', 0)
                ->update(['leida' => 1]);
        }
        return redirect()->to('notificaciones');
    }

    /** Sidebar minimo: volver al dashboard del rol + esta pagina. */
    private function sidebarSections(string $roleSlug): array
    {
        $iconHome = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        $iconBell = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>';

        $home = ['admin' => 'admin', 'asesor' => 'agente', 'empresa' => 'empresa'][$roleSlug] ?? 'candidato';

        return [[
            'title' => '',
            'links' => [
                ['key' => 'dashboard', 'url' => $home, 'label' => 'Volver al panel', 'icon' => $iconHome],
                ['key' => 'notificaciones', 'url' => 'notificaciones', 'label' => 'Notificaciones', 'icon' => $iconBell],
            ],
        ]];
    }
}
