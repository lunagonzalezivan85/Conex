<?php

namespace App\Controllers;

use App\Models\NoticiaModel;
use App\Models\UserModel;

class NoticiaController extends BaseController
{
    public function listar(): string
    {
        $noticiaModel = new NoticiaModel();
        $noticias = $noticiaModel->where('estado', 'publicado')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('layouts/publico', [
            'content' => view('public/noticias', [
                'noticias' => $noticias,
            ]),
            'css' => ['noticias.css'],
            'title' => 'Noticias',
            'meta_description' => 'Noticias y articulos sobre empleo, reclutamiento y mercado laboral en Nicaragua. Mantente al dia con CONEX.',
            'meta_keywords' => 'noticias empleo, articulos reclutamiento, mercado laboral Nicaragua, CONEX noticias',
        ]);
    }

    public function ver($slug): string
    {
        $noticiaModel = new NoticiaModel();
        $noticia = $noticiaModel->findBySlug($slug);

        if (!$noticia || $noticia['estado'] !== 'publicado') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/publico', [
            'content' => view('public/noticia-detalle', [
                'noticia' => $noticia,
            ]),
            'css' => ['noticias.css'],
            'title' => $noticia['titulo'],
            'meta_description' => $noticia['resumen'],
            'canonical_url' => base_url('noticias/' . $noticia['slug']),
        ]);
    }

    public function adminListar(): string
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $noticiaModel = new NoticiaModel();
        $noticias = $noticiaModel->orderBy('created_at', 'DESC')->findAll();

        $sidebarSections = $this->getSidebarSections($roleSlug);

        return view('layouts/panel', [
            'content' => view('admin/noticias', [
                'noticias' => $noticias,
                'sidebarSections' => $sidebarSections,
                'roleSlug' => $roleSlug,
            ]),
            'sidebarSections' => $sidebarSections,
            'roleSlug' => $roleSlug,
            'activeSection' => 'noticias',
            'css' => ['noticias-admin.css'],
        ]);
    }

    public function adminCrear(): string
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $sidebarSections = $this->getSidebarSections($roleSlug);

        return view('layouts/panel', [
            'content' => view('admin/noticia-form', [
                'sidebarSections' => $sidebarSections,
                'roleSlug' => $roleSlug,
            ]),
            'sidebarSections' => $sidebarSections,
            'roleSlug' => $roleSlug,
            'activeSection' => 'noticias',
            'css' => ['noticias-admin.css'],
        ]);
    }

    public function adminGuardar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $noticiaModel = new NoticiaModel();

        $titulo = $this->request->getPost('titulo');
        $slug = url_title($titulo, '-', true);

        $data = [
            'titulo' => $titulo,
            'slug' => $slug,
            'resumen' => $this->request->getPost('resumen'),
            'contenido' => $this->request->getPost('contenido'),
            'estado' => $this->request->getPost('estado'),
        ];

        $imagen = $this->request->getFile('imagen');
        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $nombreImagen = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/uploads/noticias', $nombreImagen);
            $data['imagen'] = $nombreImagen;
        }

        if ($noticiaModel->insert($data)) {
            return redirect()->to('admin/noticias')->with('info', 'Noticia creada correctamente.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear la noticia.');
    }

    public function adminEditar($id): string
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $noticiaModel = new NoticiaModel();
        $noticia = $noticiaModel->find($id);

        if (!$noticia) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $sidebarSections = $this->getSidebarSections($roleSlug);

        return view('layouts/panel', [
            'content' => view('admin/noticia-form', [
                'noticia' => $noticia,
                'sidebarSections' => $sidebarSections,
                'roleSlug' => $roleSlug,
            ]),
            'sidebarSections' => $sidebarSections,
            'roleSlug' => $roleSlug,
            'activeSection' => 'noticias',
            'css' => ['noticias-admin.css'],
        ]);
    }

    public function adminActualizar($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $noticiaModel = new NoticiaModel();

        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'resumen' => $this->request->getPost('resumen'),
            'contenido' => $this->request->getPost('contenido'),
            'estado' => $this->request->getPost('estado'),
        ];

        $titulo = $this->request->getPost('titulo');
        if ($titulo) {
            $data['slug'] = url_title($titulo, '-', true);
        }

        $imagen = $this->request->getFile('imagen');
        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $nombreImagen = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/uploads/noticias', $nombreImagen);
            $data['imagen'] = $nombreImagen;
        }

        if ($noticiaModel->update($id, $data)) {
            return redirect()->to('admin/noticias')->with('info', 'Noticia actualizada correctamente.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar la noticia.');
    }

    public function adminEliminar($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $noticiaModel = new NoticiaModel();
        $noticiaModel->delete($id);

        return redirect()->to('admin/noticias')->with('info', 'Noticia eliminada.');
    }

    private function getSidebarSections(string $roleSlug): array
    {
        $iconHome = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        $iconUsers = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
        $iconBriefcase = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
        $iconChart = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>';
        $iconCheck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/><path d="M21 3v6h-6"/></svg>';
        $iconNews = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6z"/></svg>';
        $iconInfo = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';

        $sections = [
            [
                'title' => '',
                'links' => [
                    ['key' => 'dashboard', 'url' => 'admin', 'label' => 'Inicio', 'icon' => $iconHome],
                    ['key' => 'usuarios', 'url' => 'admin/usuarios', 'label' => 'Usuarios', 'icon' => $iconUsers],
                ],
            ],
            [
                'title' => 'Gestion',
                'links' => [
                    ['key' => 'vacantes', 'url' => 'admin/vacantes', 'label' => 'Vacantes', 'icon' => $iconBriefcase],
                    ['key' => 'postulantes', 'url' => 'admin/postulantes', 'label' => 'Postulantes', 'icon' => $iconCheck],
                    ['key' => 'noticias', 'url' => 'admin/noticias', 'label' => 'Noticias', 'icon' => $iconNews],
                    ['key' => 'reportes', 'url' => 'admin/reportes', 'label' => 'Reportes', 'icon' => $iconChart],
                ],
            ],
            [
                'title' => 'Plataforma',
                'links' => [
                    ['key' => 'info', 'url' => 'admin/info', 'label' => 'Informacion CONEX', 'icon' => $iconInfo],
                ],
            ],
        ];

        return $sections;
    }
}
