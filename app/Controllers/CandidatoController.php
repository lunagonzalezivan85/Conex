<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CandidatoModel;
use App\Models\ExperienciaModel;
use App\Models\EducacionModel;
use App\Models\HabilidadModel;
use App\Models\IdiomaModel;
use App\Models\CVModel;
use App\Models\AnalisisCVModel;
use App\Libraries\GeminiAI;
use App\Models\PostulacionModel;
use App\Models\VacanteModel;
use App\Models\CategoriaModel;
use App\Models\DocumentoPostulacionModel;
use App\Models\VerificacionProcesoModel;

class CandidatoController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        $porcentajePerfil = $candidato['porcentaje_perfil'] ?? 0;

        $stats = ['postulaciones' => 0, 'en_revision' => 0, 'favoritas' => 0, 'visitas' => 0];
        $postulacionesRecientes = [];
        $vacantesRecomendadas = [];

        if ($candidato) {
            $db = \Config\Database::connect();
            $candidatoId = $candidato['id'];

            $stats['postulaciones'] = $db->table('post_postulacion')
                ->where('candidato_id', $candidatoId)
                ->countAllResults();

            $stats['en_revision'] = $db->table('post_postulacion')
                ->where('candidato_id', $candidatoId)
                ->where('estado', 'en_revision')
                ->countAllResults();

            $postulacionesRecientes = $db->table('post_postulacion')
                ->select('post_postulacion.id, post_postulacion.estado, post_postulacion.created_at,
                    vac_vacante.id as vacante_id, vac_vacante.titulo as vacante_titulo,
                    vac_vacante.slug as vacante_slug,
                    emp_empresa.razon_social as empresa_nombre')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id')
                ->where('post_postulacion.candidato_id', $candidatoId)
                ->orderBy('post_postulacion.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $vacantesRecomendadas = $db->table('vac_vacante')
                ->select('vac_vacante.id, vac_vacante.titulo, vac_vacante.slug, vac_vacante.ciudad,
                    vac_vacante.modalidad, vac_vacante.salario_min, vac_vacante.salario_max,
                    emp_empresa.razon_social as empresa_nombre')
                ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id')
                ->where('vac_vacante.estado', 'publicada')
                ->orderBy('vac_vacante.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('candidato/dashboard', [
            'porcentajePerfil' => $porcentajePerfil,
            'stats' => $stats,
            'postulacionesRecientes' => $postulacionesRecientes,
            'vacantesRecomendadas' => $vacantesRecomendadas,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Mi Panel',
            'pageTitle' => 'Mi Panel',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'dashboard',
            'css' => [],
        ]);
    }

    public function perfil()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        $user = $userModel->find($userId);

        $experiencias = [];
        $educaciones = [];
        $habilidades = [];
        $idiomas = [];
        $cvs = [];
        $ultimoAnalisis = null;

        if ($candidato) {
            $this->calcularPorcentajePerfil($candidato['id']);
            $candidato = $candidatoModel->find($candidato['id']);

            $experienciaModel = new ExperienciaModel();
            $educacionModel = new EducacionModel();
            $habilidadModel = new HabilidadModel();
            $idiomaModel = new IdiomaModel();
            $cvModel = new CVModel();
            $analisisModel = new AnalisisCVModel();
            $db = \Config\Database::connect();

            $experiencias = $experienciaModel->where('candidato_id', $candidato['id'])->findAll();
            $educaciones = $educacionModel->where('candidato_id', $candidato['id'])->findAll();
            $habilidades = $db->table('cand_habilidad')
                ->select('cand_habilidad.id, cand_habilidad.nivel, cat_habilidad.nombre')
                ->join('cat_habilidad', 'cat_habilidad.id = cand_habilidad.habilidad_id')
                ->where('cand_habilidad.candidato_id', $candidato['id'])
                ->get()->getResultArray();

            $idiomas = $db->table('cand_idioma')
                ->select('cand_idioma.id, cand_idioma.nivel, cat_idioma.nombre')
                ->join('cat_idioma', 'cat_idioma.id = cand_idioma.idioma_id')
                ->where('cand_idioma.candidato_id', $candidato['id'])
                ->get()->getResultArray();

            $cvs = $cvModel->where('candidato_id', $candidato['id'])->orderBy('created_at', 'DESC')->findAll();

            $analisisPorCV = [];
            foreach ($cvs as $cv) {
                $analisis = $analisisModel
                    ->where('cv_id', $cv['id'])
                    ->where('estado', 'completado')
                    ->orderBy('created_at', 'DESC')
                    ->first();
                if ($analisis) {
                    $analisisPorCV[$cv['id']] = $analisis;
                }
            }
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('candidato/perfil', [
            'candidato' => $candidato,
            'user' => $user,
            'porcentajePerfil' => $candidato['porcentaje_perfil'] ?? 0,
            'experiencias' => $experiencias,
            'educaciones' => $educaciones,
            'habilidades' => $habilidades,
            'idiomas' => $idiomas,
            'cvs' => $cvs,
            'analisisPorCV' => $analisisPorCV,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Mi Perfil',
            'pageTitle' => 'Mi Perfil',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'perfil',
            'css' => [],
        ]);
    }

    public function postulaciones()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userId = session()->get('user_id');
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $userId)->first();

        $postulaciones = [];
        if ($candidato) {
            $db = \Config\Database::connect();
            $postulaciones = $db->table('post_postulacion')
                ->select('post_postulacion.id as postulacion_id, post_postulacion.estado, post_postulacion.created_at,
                    post_postulacion.puntaje_match,
                    vac_vacante.id as vacante_id, vac_vacante.titulo as vacante_titulo,
                    vac_vacante.slug as vacante_slug,
                    emp_empresa.razon_social as empresa_nombre,
                    post_verificacion_proceso.paso_actual, post_verificacion_proceso.estado as proceso_estado')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id')
                ->join('post_verificacion_proceso', 'post_verificacion_proceso.postulacion_id = post_postulacion.id', 'left')
                ->where('post_postulacion.candidato_id', $candidato['id'])
                ->orderBy('post_postulacion.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('candidato/postulaciones', [
            'postulaciones' => $postulaciones,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Mis Postulaciones',
            'pageTitle' => 'Mis Postulaciones',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'postulaciones',
            'css' => [],
            'js' => [],
        ]);
    }

    public function subirDocumento()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $userId)->first();

        if (!$candidato) {
            return $this->response->setJSON(['error' => 'Perfil no encontrado'])->setStatusCode(404);
        }

        $postulacionId = $this->request->getPost('postulacion_id');
        $requisitoId = $this->request->getPost('requisito_id') ?: null;
        $nombreDocumento = $this->request->getPost('nombre_documento');

        $db = \Config\Database::connect();
        $postulacion = $db->table('post_postulacion')
            ->where('id', $postulacionId)
            ->where('candidato_id', $candidato['id'])
            ->get()
            ->getRowArray();

        if (!$postulacion) {
            return $this->response->setJSON(['error' => 'Postulacion no encontrada'])->setStatusCode(404);
        }

        $file = $this->request->getFile('archivo');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'Archivo no valido'])->setStatusCode(400);
        }

        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $extension = $file->getExtension();
        if (!in_array(strtolower($extension), $allowedTypes)) {
            return $this->response->setJSON(['error' => 'Tipo de archivo no permitido. Use PDF, JPG, PNG, DOC o DOCX'])->setStatusCode(400);
        }

        $uploadPath = WRITEPATH . 'uploads/documentos/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $candidato['id'] . '_' . $postulacionId . '_' . time() . '.' . $extension;
        $file->move($uploadPath, $newName);

        $documentoModel = new DocumentoPostulacionModel();

        $documentoExistente = $documentoModel->where('postulacion_id', $postulacionId)
            ->where('requisito_id', $requisitoId)
            ->first();

        if ($documentoExistente) {
            $documentoModel->update($documentoExistente['id'], [
                'nombre_documento' => $nombreDocumento,
                'archivo_path' => 'uploads/documentos/' . $newName,
                'archivo_nombre' => $newName,
                'estado' => 'pendiente',
                'comentario_verificacion' => null,
                'verificado_por' => null,
                'verificado_at' => null,
            ]);
        } else {
            $documentoModel->insert([
                'postulacion_id' => $postulacionId,
                'candidato_id' => $candidato['id'],
                'requisito_id' => $requisitoId,
                'nombre_documento' => $nombreDocumento,
                'archivo_path' => 'uploads/documentos/' . $newName,
                'archivo_nombre' => $newName,
                'estado' => 'pendiente',
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function guardarPerfil()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userModel->update($userId, [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
        ]);

        $candidato = $candidatoModel->where('user_id', $userId)->first();

        $candidatoData = [
            'profesion' => $this->request->getPost('profesion'),
            'sobre_mi' => $this->request->getPost('sobre_mi'),
            'ciudad' => $this->request->getPost('ciudad'),
            'region' => $this->request->getPost('region'),
            'disponibilidad' => $this->request->getPost('disponibilidad'),
            'modalidad_preferida' => $this->request->getPost('modalidad_preferida'),
            'salario_esperado_usd' => $this->request->getPost('salario_esperado_usd'),
            'portafolio_url' => $this->request->getPost('portafolio_url'),
            'linkedin_url' => $this->request->getPost('linkedin_url'),
        ];

        if ($candidato) {
            $candidatoModel->update($candidato['id'], $candidatoData);
            $this->calcularPorcentajePerfil($candidato['id']);
        } else {
            $candidatoData['user_id'] = $userId;
            $candidatoData['porcentaje_perfil'] = 10;
            $candidatoModel->insert($candidatoData);
            $this->calcularPorcentajePerfil($candidatoModel->getInsertID());
        }

        session()->set([
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
        ]);

        return redirect()->to('candidato/perfil')->with('info', 'Perfil actualizado correctamente.');
    }

    public function guardarExperiencia()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $experienciaModel = new ExperienciaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return redirect()->to('candidato/perfil')->with('error', 'Completa primero tu informacion personal.');
        }

        $empresas = $this->request->getPost('empresa');
        $cargos = $this->request->getPost('cargo');
        $descripciones = $this->request->getPost('exp_descripcion');
        $fechasInicio = $this->request->getPost('fecha_inicio');
        $fechasFin = $this->request->getPost('fecha_fin');
        $actuales = $this->request->getPost('actual');

        $experienciaModel->where('candidato_id', $candidato['id'])->delete();

        if (!empty($empresas) && is_array($empresas)) {
            for ($i = 0; $i < count($empresas); $i++) {
                if (empty($empresas[$i]) || empty($cargos[$i])) {
                    continue;
                }
                $experienciaModel->insert([
                    'candidato_id' => $candidato['id'],
                    'empresa' => $empresas[$i],
                    'cargo' => $cargos[$i],
                    'descripcion' => $descripciones[$i] ?? null,
                    'fecha_inicio' => $fechasInicio[$i] ?? null,
                    'fecha_fin' => empty($actuales[$i]) ? ($fechasFin[$i] ?? null) : null,
                    'actual' => !empty($actuales[$i]),
                ]);
            }
        }

        $this->calcularPorcentajePerfil($candidato['id']);
        return redirect()->to('candidato/perfil')->with('info', 'Experiencia guardada correctamente.');
    }

    public function guardarEducacion()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $educacionModel = new EducacionModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return redirect()->to('candidato/perfil')->with('error', 'Completa primero tu informacion personal.');
        }

        $instituciones = $this->request->getPost('institucion');
        $titulos = $this->request->getPost('titulo');
        $fechasInicio = $this->request->getPost('edu_fecha_inicio');
        $fechasFin = $this->request->getPost('edu_fecha_fin');
        $enCurso = $this->request->getPost('en_curso');

        $educacionModel->where('candidato_id', $candidato['id'])->delete();

        if (!empty($instituciones) && is_array($instituciones)) {
            for ($i = 0; $i < count($instituciones); $i++) {
                if (empty($instituciones[$i]) || empty($titulos[$i])) {
                    continue;
                }
                $educacionModel->insert([
                    'candidato_id' => $candidato['id'],
                    'nivel_educacion_id' => 1,
                    'institucion' => $instituciones[$i],
                    'titulo' => $titulos[$i],
                    'fecha_inicio' => $fechasInicio[$i] ?? null,
                    'fecha_fin' => empty($enCurso[$i]) ? ($fechasFin[$i] ?? null) : null,
                    'en_curso' => !empty($enCurso[$i]),
                ]);
            }
        }

        $this->calcularPorcentajePerfil($candidato['id']);
        return redirect()->to('candidato/perfil')->with('info', 'Educacion guardada correctamente.');
    }

    public function guardarHabilidades()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $habilidadModel = new HabilidadModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return redirect()->to('candidato/perfil')->with('error', 'Completa primero tu informacion personal.');
        }

        $habilidades = $this->request->getPost('habilidades');

        $habilidadModel->where('candidato_id', $candidato['id'])->delete();

        if (!empty($habilidades) && is_array($habilidades)) {
            $db = \Config\Database::connect();
            foreach ($habilidades as $nombre) {
                $nombre = trim($nombre);
                if (empty($nombre)) continue;

                $existing = $db->table('cat_habilidad')->where('nombre', $nombre)->get()->getRowArray();
                if ($existing) {
                    $habilidadId = $existing['id'];
                } else {
                    $slug = url_title($nombre, '-', true);
                    $db->table('cat_habilidad')->insert(['nombre' => $nombre, 'slug' => $slug]);
                    $habilidadId = $db->insertID();
                }

                $habilidadModel->insert([
                    'candidato_id' => $candidato['id'],
                    'habilidad_id' => $habilidadId,
                    'nivel' => 'intermedio',
                ]);
            }
        }

        $this->calcularPorcentajePerfil($candidato['id']);
        return redirect()->to('candidato/perfil')->with('info', 'Habilidades guardadas correctamente.');
    }

    public function guardarIdiomas()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $idiomaModel = new IdiomaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return redirect()->to('candidato/perfil')->with('error', 'Completa primero tu informacion personal.');
        }

        $nombres = $this->request->getPost('idioma');
        $niveles = $this->request->getPost('nivel');

        $idiomaModel->where('candidato_id', $candidato['id'])->delete();

        if (!empty($nombres) && is_array($nombres)) {
            $db = \Config\Database::connect();
            for ($i = 0; $i < count($nombres); $i++) {
                $nombre = trim($nombres[$i]);
                if (empty($nombre)) continue;

                $existing = $db->table('cat_idioma')->where('nombre', $nombre)->get()->getRowArray();
                if ($existing) {
                    $idiomaId = $existing['id'];
                } else {
                    $slug = url_title($nombre, '-', true);
                    $db->table('cat_idioma')->insert(['nombre' => $nombre, 'slug' => $slug]);
                    $idiomaId = $db->insertID();
                }

                $nivel = $niveles[$i] ?? 'intermedio';

                $idiomaModel->insert([
                    'candidato_id' => $candidato['id'],
                    'idioma_id' => $idiomaId,
                    'nivel' => $nivel,
                ]);
            }
        }

        $this->calcularPorcentajePerfil($candidato['id']);
        return redirect()->to('candidato/perfil')->with('info', 'Idiomas guardados correctamente.');
    }

    public function subirCV()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $cvModel = new CVModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return $this->response->setJSON(['error' => 'Completa primero tu informacion personal'])->setStatusCode(400);
        }

        $file = $this->request->getFile('cv');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'No se recibio el archivo'])->setStatusCode(400);
        }

        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['error' => 'Formato no permitido. Solo PDF, DOC o DOCX'])->setStatusCode(400);
        }

        if ($file->getSize() > 5242880) {
            return $this->response->setJSON(['error' => 'El archivo supera el limite de 5MB'])->setStatusCode(400);
        }

        $uploadPath = WRITEPATH . 'uploads/cv/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $cvModel->insert([
            'candidato_id' => $candidato['id'],
            'archivo_path' => 'uploads/cv/' . $newName,
            'archivo_nombre' => $file->getClientName(),
            'es_principal' => true,
        ]);

        $cvId = $cvModel->getInsertID();

        return $this->response->setJSON([
            'success' => true,
            'cv_id' => $cvId,
            'nombre' => $file->getClientName(),
            'size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
        ]);
    }

    public function eliminarCV()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $cvModel = new CVModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return $this->response->setJSON(['error' => 'Candidato no encontrado'])->setStatusCode(400);
        }

        $cvId = $this->request->getPost('cv_id');
        $cv = $cvModel->find($cvId);
        if (!$cv || $cv['candidato_id'] != $candidato['id']) {
            return $this->response->setJSON(['error' => 'CV no encontrado'])->setStatusCode(404);
        }

        $filePath = WRITEPATH . $cv['archivo_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $analisisModel = new AnalisisCVModel();
        $analisisModel->where('cv_id', $cvId)->delete();
        $cvModel->delete($cvId);

        return $this->response->setJSON(['success' => true]);
    }

    public function analizarCV()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $cvModel = new CVModel();
        $analisisModel = new AnalisisCVModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return $this->response->setJSON(['error' => 'Completa primero tu informacion personal'])->setStatusCode(400);
        }

        $cvId = $this->request->getPost('cv_id');
        $cv = $cvModel->find($cvId);
        if (!$cv || $cv['candidato_id'] != $candidato['id']) {
            return $this->response->setJSON(['error' => 'CV no encontrado'])->setStatusCode(404);
        }

        $gemini = new GeminiAI();
        if (!$gemini->hasApiKey()) {
            return $this->response->setJSON(['error' => 'No se ha configurado la API key de Gemini'])->setStatusCode(500);
        }

        $cvText = $this->extractCVText(WRITEPATH . 'uploads/cv/' . basename($cv['archivo_path']));
        if (empty($cvText)) {
            return $this->response->setJSON(['error' => 'No se pudo extraer texto del CV'])->setStatusCode(500);
        }

        $prompt = 'Analiza el siguiente CV y devuelve un JSON con score, fortalezas, debilidades, recomendacion, habilidades_detectadas, experiencia_anios y nivel_educacion.';
        $result = $gemini->analyzeCV($cvText);

        $analisisModel->insert([
            'candidato_id' => $candidato['id'],
            'cv_id' => $cvId,
            'prompt' => $prompt,
            'respuesta' => $result['respuesta'],
            'score_match' => $result['score_match'],
            'fortalezas' => $result['fortalezas'],
            'debilidades' => $result['debilidades'],
            'recomendacion' => $result['recomendacion'],
            'modelo_ia' => 'gemini-3.6-flash',
            'tokens_usados' => $result['tokens_usados'],
            'tiempo_respuesta_ms' => $result['tiempo_respuesta_ms'],
            'estado' => $result['estado'],
            'error' => $result['error'],
        ]);

        $analisisId = $analisisModel->getInsertID();

        $parsed = json_decode($result['respuesta'], true);
        if (!is_array($parsed)) $parsed = [];

        return $this->response->setJSON([
            'success' => true,
            'analisis_id' => $analisisId,
            'estado' => $result['estado'],
            'score' => $result['score_match'],
            'fortalezas' => $result['fortalezas'],
            'debilidades' => $result['debilidades'],
            'recomendacion' => $result['recomendacion'],
            'habilidades_detectadas' => $parsed['habilidades_detectadas'] ?? [],
            'experiencia_anios' => $parsed['experiencia_anios'] ?? null,
            'nivel_educacion' => $parsed['nivel_educacion'] ?? null,
            'datos_personales' => $parsed['datos_personales'] ?? [],
            'experiencias' => $parsed['experiencias'] ?? [],
            'educaciones' => $parsed['educaciones'] ?? [],
            'idiomas' => $parsed['idiomas'] ?? [],
            'error' => $result['error'],
        ]);
    }

    public function aplicarAnalisis()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $candidatoModel = new CandidatoModel();
        $habilidadModel = new HabilidadModel();
        $experienciaModel = new ExperienciaModel();
        $educacionModel = new EducacionModel();
        $idiomaModel = new IdiomaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            return $this->response->setJSON(['error' => 'Candidato no encontrado'])->setStatusCode(400);
        }

        $analisisId = $this->request->getPost('analisis_id');
        $analisisModel = new AnalisisCVModel();
        $analisis = $analisisModel->find($analisisId);
        if (!$analisis) {
            return $this->response->setJSON(['error' => 'Analisis no encontrado'])->setStatusCode(404);
        }

        $parsed = json_decode($analisis['respuesta'], true);
        if (!is_array($parsed)) $parsed = [];

        $db = \Config\Database::connect();
        $aplicado = [];

        // 1. Datos personales
        if (!empty($parsed['datos_personales'])) {
            $dp = $parsed['datos_personales'];
            $updateData = [];

            if (!empty($dp['resumen']) && empty($candidato['sobre_mi'])) {
                $updateData['sobre_mi'] = $dp['resumen'];
            }
            if (!empty($dp['ubicacion']) && empty($candidato['direccion'])) {
                $updateData['direccion'] = $dp['ubicacion'];
            }
            if (!empty($dp['linkedin']) && empty($candidato['linkedin_url'])) {
                $updateData['linkedin_url'] = $dp['linkedin'];
            }
            if (!empty($dp['telefono'])) {
                $userRow = $userModel->find($userId);
                if ($userRow && empty($userRow['telefono'])) {
                    $userModel->update($userId, ['telefono' => $dp['telefono']]);
                }
            }

            if (!empty($updateData)) {
                $candidatoModel->update($candidato['id'], $updateData);
                $aplicado[] = 'datos personales';
            }
        }

        // 2. Experiencias laborales
        if (!empty($parsed['experiencias'])) {
            $experienciaModel->where('candidato_id', $candidato['id'])->delete();
            $count = 0;
            foreach ($parsed['experiencias'] as $exp) {
                if (empty($exp['empresa']) && empty($exp['cargo'])) continue;
                $experienciaModel->insert([
                    'candidato_id' => $candidato['id'],
                    'empresa' => $exp['empresa'] ?? 'No especificado',
                    'cargo' => $exp['cargo'] ?? 'No especificado',
                    'fecha_inicio' => !empty($exp['fecha_inicio']) ? $exp['fecha_inicio'] : '2000-01-01',
                    'fecha_fin' => !empty($exp['fecha_fin']) ? $exp['fecha_fin'] : null,
                    'actual' => !empty($exp['actual']) ? 1 : 0,
                    'descripcion' => $exp['descripcion'] ?? '',
                ]);
                $count++;
            }
            if ($count > 0) $aplicado[] = 'experiencias (' . $count . ')';
        }

        // 3. Educaciones
        if (!empty($parsed['educaciones'])) {
            $educacionModel->where('candidato_id', $candidato['id'])->delete();
            $count = 0;
            foreach ($parsed['educaciones'] as $edu) {
                if (empty($edu['institucion']) && empty($edu['titulo'])) continue;

                $nivel = strtolower(trim($edu['nivel'] ?? ''));
                $nivelMap = [
                    'primaria' => 'primaria',
                    'secundaria' => 'secundaria',
                    'tecnico' => 'tecnico',
                    'tecnic' => 'tecnico',
                    'universitario' => 'universitario',
                    'universidad' => 'universitario',
                    'posgrado' => 'posgrado',
                    'doctorado' => 'doctorado',
                ];
                $nivelSlug = $nivelMap[$nivel] ?? 'tecnico';

                $nivelRow = $db->table('cat_nivel_educacion')->where('slug', $nivelSlug)->get()->getRowArray();
                if (!$nivelRow) {
                    $nivelRow = $db->table('cat_nivel_educacion')->limit(1)->get()->getRowArray();
                }
                $nivelEducacionId = $nivelRow ? $nivelRow['id'] : 1;

                $educacionModel->insert([
                    'candidato_id' => $candidato['id'],
                    'nivel_educacion_id' => $nivelEducacionId,
                    'institucion' => $edu['institucion'] ?? 'No especificado',
                    'titulo' => $edu['titulo'] ?? 'No especificado',
                    'fecha_inicio' => !empty($edu['fecha_inicio']) ? $edu['fecha_inicio'] : null,
                    'fecha_fin' => !empty($edu['fecha_fin']) ? $edu['fecha_fin'] : null,
                    'en_curso' => !empty($edu['en_curso']) ? 1 : 0,
                ]);
                $count++;
            }
            if ($count > 0) $aplicado[] = 'educaciones (' . $count . ')';
        }

        // 4. Habilidades
        if (!empty($parsed['habilidades_detectadas'])) {
            $count = 0;
            foreach ($parsed['habilidades_detectadas'] as $nombre) {
                $nombre = trim($nombre);
                if (empty($nombre)) continue;

                $existing = $db->table('cat_habilidad')->where('nombre', $nombre)->get()->getRowArray();
                if (!$existing) {
                    $slug = url_title($nombre, '-', true);
                    $existing = $db->table('cat_habilidad')->where('slug', $slug)->get()->getRowArray();
                }
                if ($existing) {
                    $habilidadId = $existing['id'];
                } else {
                    $slug = url_title($nombre, '-', true);
                    $db->table('cat_habilidad')->insert([
                        'nombre' => $nombre,
                        'slug' => $slug,
                        'estado' => 'activo',
                    ]);
                    $habilidadId = $db->insertID();
                }

                if (empty($habilidadId)) continue;

                $yaExiste = $db->table('cand_habilidad')
                    ->where('candidato_id', $candidato['id'])
                    ->where('habilidad_id', $habilidadId)
                    ->get()->getRowArray();
                if (!$yaExiste) {
                    $db->table('cand_habilidad')->insert([
                        'candidato_id' => $candidato['id'],
                        'habilidad_id' => $habilidadId,
                        'nivel' => 'intermedio',
                    ]);
                    $count++;
                }
            }
            if ($count > 0) $aplicado[] = 'habilidades (' . $count . ')';
        }

        // 5. Idiomas
        if (!empty($parsed['idiomas'])) {
            $count = 0;
            foreach ($parsed['idiomas'] as $idi) {
                $nombre = trim($idi['nombre'] ?? '');
                if (empty($nombre)) continue;

                $nivel = $idi['nivel'] ?? 'intermedio';
                if (!in_array($nivel, ['basico', 'intermedio', 'avanzado', 'nativo'])) {
                    $nivel = 'intermedio';
                }

                $existing = $db->table('cat_idioma')->where('nombre', $nombre)->get()->getRowArray();
                if ($existing) {
                    $idiomaId = $existing['id'];
                } else {
                    $slug = url_title($nombre, '-', true);
                    $db->table('cat_idioma')->insert(['nombre' => $nombre, 'slug' => $slug]);
                    $idiomaId = $db->insertID();
                }

                $yaExiste = $idiomaModel->where('candidato_id', $candidato['id'])->where('idioma_id', $idiomaId)->first();
                if (!$yaExiste) {
                    $idiomaModel->insert([
                        'candidato_id' => $candidato['id'],
                        'idioma_id' => $idiomaId,
                        'nivel' => $nivel,
                    ]);
                    $count++;
                }
            }
            if ($count > 0) $aplicado[] = 'idiomas (' . $count . ')';
        }

        $this->calcularPorcentajePerfil($candidato['id']);

        return $this->response->setJSON([
            'success' => true,
            'aplicado' => $aplicado,
        ]);
    }

    public function buscarEmpleo()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $vacanteModel = new VacanteModel();
        $categoriaModel = new CategoriaModel();

        $q = $this->request->getGet('q');
        $categoria = $this->request->getGet('categoria');
        $modalidad = $this->request->getGet('modalidad');
        $tipoContrato = $this->request->getGet('tipo_contrato');
        $ciudad = $this->request->getGet('ciudad');

        $builder = $vacanteModel->where('vac_vacante.estado', 'publicada');

        if (!empty($q)) {
            $builder->like('vac_vacante.titulo', $q);
        }
        if (!empty($categoria)) {
            $builder->where('vac_vacante.categoria_id', $categoria);
        }
        if (!empty($modalidad)) {
            $builder->where('vac_vacante.modalidad', $modalidad);
        }
        if (!empty($tipoContrato)) {
            $builder->where('vac_vacante.tipo_contrato_id', $tipoContrato);
        }
        if (!empty($ciudad)) {
            $builder->like('vac_vacante.ciudad', $ciudad);
        }

        $vacantes = $builder->orderBy('vac_vacante.fecha_publicacion', 'DESC')->paginate(12);
        $categorias = $categoriaModel->where('estado', 'activo')->findAll();

        $sidebarSections = $this->getSidebarSections();

        $content = view('candidato/buscar-empleo', [
            'vacantes' => $vacantes,
            'categorias' => $categorias,
            'pager' => $vacanteModel->pager,
            'filtros' => [
                'q' => $q,
                'categoria' => $categoria,
                'modalidad' => $modalidad,
                'tipo_contrato' => $tipoContrato,
                'ciudad' => $ciudad,
            ],
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Buscar Empleo',
            'pageTitle' => 'Buscar Empleo',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'buscar',
            'css' => ['buscar-empleo.css'],
        ]);
    }

    public function verVacante($slug)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel->where('slug', $slug)->first();

        if (!$vacante) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userId = session()->get('user_id');
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $userId)->first();

        $yaPostulado = false;
        if ($candidato) {
            $db = \Config\Database::connect();
            $yaPostulado = $db->table('post_postulacion')
                ->where('vacante_id', $vacante['id'])
                ->where('candidato_id', $candidato['id'])
                ->countAllResults() > 0;
        }

        $db = \Config\Database::connect();
        $empresa = $db->table('emp_empresa')
            ->where('id', $vacante['empresa_id'])
            ->get()->getRowArray();

        $requisitos = $db->table('vac_requisito')
            ->select('vac_requisito.*, cat_requisito.nombre as requisito_nombre')
            ->join('cat_requisito', 'cat_requisito.id = vac_requisito.requisito_id')
            ->where('vac_requisito.vacante_id', $vacante['id'])
            ->get()->getResultArray();

        $sidebarSections = $this->getSidebarSections();

        $content = view('candidato/vacante-detalle', [
            'vacante' => $vacante,
            'empresa' => $empresa,
            'requisitos' => $requisitos,
            'yaPostulado' => $yaPostulado,
            'candidato' => $candidato,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => $vacante['titulo'],
            'pageTitle' => $vacante['titulo'],
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'buscar',
            'css' => ['vacante-detalle.css'],
        ]);
    }

    public function postularse($vacanteId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userId = session()->get('user_id');
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $userId)->first();

        if (!$candidato) {
            return redirect()->back()->with('error', 'Debes completar tu perfil antes de postularte.');
        }

        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel->find($vacanteId);

        if (!$vacante || $vacante['estado'] !== 'publicada') {
            return redirect()->back()->with('error', 'Esta vacante no esta disponible.');
        }

        $db = \Config\Database::connect();
        $yaPostulado = $db->table('post_postulacion')
            ->where('vacante_id', $vacanteId)
            ->where('candidato_id', $candidato['id'])
            ->countAllResults() > 0;

        if ($yaPostulado) {
            return redirect()->back()->with('error', 'Ya te has postulado a esta vacante.');
        }

        $mensaje = $this->request->getPost('mensaje');

        $puntaje = 0;
        if (!empty($vacante['anios_experiencia']) && $vacante['anios_experiencia'] > 0) {
            $experienciaModel = new ExperienciaModel();
            $experiencias = $experienciaModel->where('candidato_id', $candidato['id'])->findAll();
            $aniosTotales = 0;
            foreach ($experiencias as $exp) {
                if (!empty($exp['fecha_inicio'])) {
                    $fin = !empty($exp['fecha_fin']) ? strtotime($exp['fecha_fin']) : time();
                    $inicio = strtotime($exp['fecha_inicio']);
                    if ($fin > $inicio) {
                        $aniosTotales += ($fin - $inicio) / (365 * 24 * 60 * 60);
                    }
                }
            }
            if ($aniosTotales >= $vacante['anios_experiencia']) {
                $puntaje = 100;
            } else {
                $puntaje = (int)(($aniosTotales / $vacante['anios_experiencia']) * 100);
            }
        }
        if ($puntaje > 0) {
            $puntaje = max(10, min(100, $puntaje));
        }

        $db->table('post_postulacion')->insert([
            'vacante_id' => $vacanteId,
            'candidato_id' => $candidato['id'],
            'mensaje' => $mensaje,
            'estado' => 'enviada',
            'puntaje_match' => $puntaje,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('candidato/postulaciones')->with('success', 'Te has postulado correctamente.');
    }

    public function obtenerAnalisis($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $analisisModel = new AnalisisCVModel();
        $analisis = $analisisModel->find($id);
        if (!$analisis) {
            return $this->response->setJSON(['error' => 'Analisis no encontrado'])->setStatusCode(404);
        }

        return $this->response->setJSON($analisis);
    }

    private function extractCVText(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'txt') {
            return file_get_contents($filePath);
        }

        if ($ext === 'pdf') {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                return $pdf->getText();
            } catch (\Throwable $e) {
                return '';
            }
        }

        if ($ext === 'doc' || $ext === 'docx') {
            $text = '';
            $zip = new \ZipArchive();
            if ($ext === 'docx' && $zip->open($filePath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($xml) {
                    $text = strip_tags(str_replace(['<w:p>', '</w:p>'], ["\n", ''], $xml));
                    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
                }
                return $text;
            }

            $output = [];
            $returnVar = 0;
            exec('antiword ' . escapeshellarg($filePath) . ' 2>&1', $output, $returnVar);
            if ($returnVar === 0) {
                return implode("\n", $output);
            }
            return '';
        }

        return '';
    }

    private function guardarHabilidadesDesdeAnalisis(int $candidatoId, string $respuesta): void
    {
        $data = json_decode($respuesta, true);
        if (!is_array($data) || empty($data['habilidades_detectadas'])) {
            return;
        }

        $habilidadModel = new HabilidadModel();
        $db = \Config\Database::connect();

        foreach ($data['habilidades_detectadas'] as $nombre) {
            $nombre = trim($nombre);
            if (empty($nombre)) continue;

            $existing = $db->table('cat_habilidad')->where('nombre', $nombre)->get()->getRowArray();
            if ($existing) {
                $habilidadId = $existing['id'];
            } else {
                $slug = url_title($nombre, '-', true);
                $db->table('cat_habilidad')->insert([
                    'nombre' => $nombre,
                    'slug' => $slug,
                ]);
                $habilidadId = $db->insertID();
            }

            $yaExiste = $habilidadModel->where('candidato_id', $candidatoId)->where('habilidad_id', $habilidadId)->first();
            if (!$yaExiste) {
                $habilidadModel->insert([
                    'candidato_id' => $candidatoId,
                    'habilidad_id' => $habilidadId,
                    'nivel' => 'intermedio',
                ]);
            }
        }
    }

    public function info()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        $sidebarSections = $this->getSidebarSections();

        $content = view('shared/info-conex');

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Informacion CONEX',
            'pageTitle' => 'Informacion CONEX',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'info',
            'css' => [],
        ]);
    }

    private function getSidebarSections(): array
    {
        $iconHome = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        $iconUser = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        $iconFile = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
        $iconBriefcase = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
        $iconSearch = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>';
        $iconInfo = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
        $iconPlan = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>';

        return [
            [
                'title' => '',
                'links' => [
                    ['key' => 'dashboard', 'url' => 'candidato', 'label' => 'Inicio', 'icon' => $iconHome],
                    ['key' => 'perfil', 'url' => 'candidato/perfil', 'label' => 'Mi Perfil', 'icon' => $iconUser],
                    ['key' => 'postulaciones', 'url' => 'candidato/postulaciones', 'label' => 'Mis Postulaciones', 'icon' => $iconFile],
                ],
            ],
            [
                'title' => 'Empleo',
                'links' => [
                    ['key' => 'buscar', 'url' => 'candidato/buscar-empleo', 'label' => 'Buscar Vacantes', 'icon' => $iconSearch],
                ],
            ],
            [
                'title' => 'Plataforma',
                'links' => [
                    ['key' => 'planes', 'url' => 'candidato/planes', 'label' => 'Planes', 'icon' => $iconPlan],
                    ['key' => 'info', 'url' => 'candidato/info', 'label' => 'Informacion CONEX', 'icon' => $iconInfo],
                ],
            ],
        ];
    }

    public function planes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'candidato') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $db = \Config\Database::connect();

        $planActual = null;

        $planes = $db->table('crm_plan')
            ->where('estado', 'activo')
            ->orderBy('precio_mensual', 'ASC')
            ->get()
            ->getResultArray();

        $sidebarSections = $this->getSidebarSections();

        $content = view('candidato/planes', [
            'planActual' => $planActual,
            'planes' => $planes,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Planes',
            'pageTitle' => 'Planes',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'planes',
            'css' => ['planes-panel.css'],
        ]);
    }

    private function calcularPorcentajePerfil($candidatoId)
    {
        $db = \Config\Database::connect();
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->find($candidatoId);
        if (!$candidato) return;

        $porcentaje = 0;

        // Datos personales (40%)
        if (!empty($candidato['profesion'])) $porcentaje += 5;
        if (!empty($candidato['sobre_mi'])) $porcentaje += 10;
        if (!empty($candidato['ciudad'])) $porcentaje += 5;
        if (!empty($candidato['disponibilidad'])) $porcentaje += 5;
        if (!empty($candidato['modalidad_preferida'])) $porcentaje += 5;
        if (!empty($candidato['linkedin_url']) || !empty($candidato['portafolio_url'])) $porcentaje += 5;
        if (!empty($candidato['direccion'])) $porcentaje += 5;

        // Experiencia (20%)
        $expCount = $db->table('cand_experiencia')->where('candidato_id', $candidatoId)->countAllResults();
        if ($expCount > 0) $porcentaje += 20;

        // Educacion (15%)
        $eduCount = $db->table('cand_educacion')->where('candidato_id', $candidatoId)->countAllResults();
        if ($eduCount > 0) $porcentaje += 15;

        // Habilidades (15%)
        $habCount = $db->table('cand_habilidad')->where('candidato_id', $candidatoId)->countAllResults();
        if ($habCount > 0) $porcentaje += 15;

        // Idiomas (10%)
        $idiomaCount = $db->table('cand_idioma')->where('candidato_id', $candidatoId)->countAllResults();
        if ($idiomaCount > 0) $porcentaje += 10;

        $candidatoModel->update($candidatoId, ['porcentaje_perfil' => $porcentaje]);

        return $porcentaje;
    }
}
