<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DocumentoPostulacionModel;
use App\Models\VerificacionProcesoModel;
use App\Models\EntrevistaEvaluacionModel;

class AdminController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $totalUsuarios = $userModel->countAll();
        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('admin/dashboard', [
            'totalUsuarios' => $totalUsuarios,
            'roleSlug' => $roleSlug,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Panel Admin',
            'pageTitle' => 'Panel Administrativo',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'dashboard',
            'css' => [],
        ]);
    }

    public function usuarios()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $usuarios = $userModel->select('auth_user.*, auth_role.slug as role_slug, auth_role.nombre as role_nombre')
            ->join('auth_role', 'auth_role.id = auth_user.role_id')
            ->findAll();

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('admin/usuarios', [
            'usuarios' => $usuarios,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Usuarios',
            'pageTitle' => 'Gestion de Usuarios',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'usuarios',
            'css' => [],
        ]);
    }

    public function vacantes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $db = \Config\Database::connect();
        $vacantes = $db->table('vac_vacante')
            ->select('vac_vacante.*, emp_empresa.razon_social as empresa_nombre,
                (SELECT COUNT(*) FROM post_postulacion WHERE post_postulacion.vacante_id = vac_vacante.id) as total_postulantes')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id')
            ->orderBy('vac_vacante.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('admin/vacantes', [
            'vacantes' => $vacantes,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Vacantes',
            'pageTitle' => 'Gestion de Vacantes',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'vacantes',
            'css' => ['vacantes-admin.css'],
        ]);
    }

    public function postulantes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $db = \Config\Database::connect();
        $postulantes = $db->table('post_postulacion')
            ->select('cand_candidato.id as candidato_id,
                auth_user.nombre, auth_user.apellido, auth_user.email,
                cand_candidato.profesion,
                COUNT(DISTINCT post_postulacion.vacante_id) as total_vacantes,
                SUM(CASE WHEN post_postulacion.estado = "contratado" THEN 1 ELSE 0 END) as total_aceptadas,
                SUM(CASE WHEN post_postulacion.estado = "enviada" THEN 1 ELSE 0 END) as total_pendientes,
                SUM(CASE WHEN post_verificacion_proceso.estado = "completado" THEN 1 ELSE 0 END) as total_verificados,
                SUM(CASE WHEN post_verificacion_proceso.estado = "en_proceso" THEN 1 ELSE 0 END) as total_en_verificacion,
                SUM(CASE WHEN post_postulacion.estado = "en_proceso" THEN 1 ELSE 0 END) as total_en_proceso_empresa,
                SUM(CASE WHEN post_postulacion.estado = "rechazado" THEN 1 ELSE 0 END) as total_rechazados,
                SUM(CASE WHEN post_postulacion.estado = "contratado" THEN 1 ELSE 0 END) as total_contratados')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->join('post_verificacion_proceso', 'post_verificacion_proceso.postulacion_id = post_postulacion.id', 'left')
            ->groupBy('cand_candidato.id, auth_user.id')
            ->orderBy('total_vacantes', 'DESC')
            ->get()
            ->getResultArray();

        $asesores = $db->table('auth_user')
            ->select('auth_user.id, auth_user.nombre, auth_user.apellido')
            ->join('auth_role', 'auth_role.id = auth_user.role_id')
            ->whereIn('auth_role.slug', ['admin', 'asesor'])
            ->get()
            ->getResultArray();

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('admin/postulantes', [
            'postulantes' => $postulantes,
            'asesores' => $asesores,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Postulantes',
            'pageTitle' => 'Verificacion de Postulantes',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'postulantes',
            'css' => ['verificacion-postulantes.css'],
        ]);
    }

    public function verificarPostulante($candidatoId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $db = \Config\Database::connect();

        $candidato = $db->table('cand_candidato')
            ->select('cand_candidato.*, auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.telefono')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('cand_candidato.id', $candidatoId)
            ->get()
            ->getRowArray();

        if (!$candidato) {
            return redirect()->to('admin/postulantes')->with('error', 'Postulante no encontrado.');
        }

        $postulaciones = $db->table('post_postulacion')
            ->select('post_postulacion.id as postulacion_id, post_postulacion.estado, post_postulacion.created_at,
                vac_vacante.id as vacante_id, vac_vacante.titulo as vacante_titulo,
                emp_empresa.razon_social as empresa_nombre')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id')
            ->where('post_postulacion.candidato_id', $candidatoId)
            ->orderBy('post_postulacion.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $documentoModel = new DocumentoPostulacionModel();
        $procesoModel = new VerificacionProcesoModel();

        $asesores = $db->table('auth_user')
            ->select('auth_user.id, auth_user.nombre, auth_user.apellido')
            ->join('auth_role', 'auth_role.id = auth_user.role_id')
            ->whereIn('auth_role.slug', ['admin', 'asesor'])
            ->get()
            ->getResultArray();

        foreach ($postulaciones as &$p) {
            $p['requisitos'] = $db->table('vac_requisito')
                ->select('vac_requisito.*, cat_requisito.nombre as requisito_nombre')
                ->join('cat_requisito', 'cat_requisito.id = vac_requisito.requisito_id')
                ->where('vac_requisito.vacante_id', $p['vacante_id'])
                ->get()
                ->getResultArray();

            $p['documentos'] = $documentoModel->where('postulacion_id', $p['postulacion_id'])->findAll();

            $p['proceso'] = $procesoModel->where('postulacion_id', $p['postulacion_id'])->first();

            // Datos de vacante y candidato para calculo automatico
            $vacante = $db->table('vac_vacante')
                ->select('vac_vacante.*, cat_nivel_experiencia.nombre as nivel_exp_nombre, cat_nivel_experiencia.anios_minimos, cat_nivel_experiencia.anios_maximos')
                ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = vac_vacante.nivel_experiencia_id', 'left')
                ->where('vac_vacante.id', $p['vacante_id'])
                ->get()
                ->getRowArray();

            $candidatoData = $db->table('cand_candidato')
                ->select('cand_candidato.*, cat_nivel_experiencia.nombre as nivel_exp_nombre, cat_nivel_experiencia.anios_minimos, cat_nivel_experiencia.anios_maximos')
                ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = cand_candidato.nivel_experiencia_id', 'left')
                ->where('cand_candidato.user_id', $candidatoId)
                ->get()
                ->getRowArray();

            $p['eval_auto'] = $this->calcularEvaluacionAutomatica($vacante, $candidatoData, $p);

            if ($p['proceso']) {
                $p['historial'] = $db->table('post_verificacion_historial')
                    ->where('proceso_id', $p['proceso']['id'])
                    ->orderBy('paso', 'ASC')
                    ->get()
                    ->getResultArray();

                $evalModel = new EntrevistaEvaluacionModel();
                $p['evaluaciones'] = $evalModel->where('proceso_id', $p['proceso']['id'])->findAll();
            } else {
                $p['historial'] = [];
                $p['evaluaciones'] = [];
            }
        }

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('admin/verificar-postulante', [
            'candidato' => $candidato,
            'postulaciones' => $postulaciones,
            'asesores' => $asesores,
            'pasos' => VerificacionProcesoModel::PASOS,
            'criterios' => EntrevistaEvaluacionModel::CRITERIOS,
            'calificaciones' => EntrevistaEvaluacionModel::CALIFICACIONES,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Verificar Postulante',
            'pageTitle' => 'Verificacion de Postulante',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'postulantes',
            'css' => ['verificacion-postulantes.css'],
            'js' => ['verificacion-postulantes.js'],
        ]);
    }

    public function perfilPostulante($candidatoId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $db = \Config\Database::connect();

        $candidato = $db->table('cand_candidato')
            ->select('cand_candidato.*, auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.telefono,
                cat_nivel_experiencia.nombre as nivel_exp_nombre')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = cand_candidato.nivel_experiencia_id', 'left')
            ->where('cand_candidato.id', $candidatoId)
            ->get()
            ->getRowArray();

        if (!$candidato) {
            return redirect()->to('admin/postulantes')->with('error', 'Postulante no encontrado.');
        }

        $experiencias = $db->table('cand_experiencia')
            ->where('candidato_id', $candidatoId)
            ->orderBy('fecha_inicio', 'DESC')
            ->get()
            ->getResultArray();

        $educacion = $db->table('cand_educacion')
            ->select('cand_educacion.*, cat_nivel_educacion.nombre as nivel_nombre')
            ->join('cat_nivel_educacion', 'cat_nivel_educacion.id = cand_educacion.nivel_educacion_id', 'left')
            ->where('cand_educacion.candidato_id', $candidatoId)
            ->orderBy('fecha_inicio', 'DESC')
            ->get()
            ->getResultArray();

        $habilidades = $db->table('cand_habilidad')
            ->select('cand_habilidad.*, cat_habilidad.nombre as habilidad_nombre')
            ->join('cat_habilidad', 'cat_habilidad.id = cand_habilidad.habilidad_id', 'left')
            ->where('cand_habilidad.candidato_id', $candidatoId)
            ->get()
            ->getResultArray();

        $idiomas = $db->table('cand_idioma')
            ->select('cand_idioma.*, cat_idioma.nombre as idioma_nombre')
            ->join('cat_idioma', 'cat_idioma.id = cand_idioma.idioma_id', 'left')
            ->where('cand_idioma.candidato_id', $candidatoId)
            ->get()
            ->getResultArray();

        $cvs = $db->table('cand_cv')
            ->where('candidato_id', $candidatoId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $postulaciones = $db->table('post_postulacion')
            ->select('post_postulacion.id as postulacion_id, post_postulacion.estado, post_postulacion.created_at,
                vac_vacante.id as vacante_id, vac_vacante.titulo as vacante_titulo,
                emp_empresa.razon_social as empresa_nombre')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id')
            ->where('post_postulacion.candidato_id', $candidatoId)
            ->orderBy('post_postulacion.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $documentoModel = new DocumentoPostulacionModel();
        $procesoModel = new VerificacionProcesoModel();

        foreach ($postulaciones as &$p) {
            $p['requisitos'] = $db->table('vac_requisito')
                ->select('vac_requisito.*, cat_requisito.nombre as requisito_nombre')
                ->join('cat_requisito', 'cat_requisito.id = vac_requisito.requisito_id')
                ->where('vac_requisito.vacante_id', $p['vacante_id'])
                ->get()
                ->getResultArray();

            $p['documentos'] = $documentoModel->where('postulacion_id', $p['postulacion_id'])->findAll();
            $p['proceso'] = $procesoModel->where('postulacion_id', $p['postulacion_id'])->first();

            if ($p['proceso']) {
                $p['historial'] = $db->table('post_verificacion_historial')
                    ->where('proceso_id', $p['proceso']['id'])
                    ->orderBy('paso', 'ASC')
                    ->get()
                    ->getResultArray();

                $evalModel = new EntrevistaEvaluacionModel();
                $p['evaluaciones'] = $evalModel->where('proceso_id', $p['proceso']['id'])->findAll();
            } else {
                $p['historial'] = [];
                $p['evaluaciones'] = [];
            }
        }

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('admin/postulante-perfil', [
            'candidato' => $candidato,
            'experiencias' => $experiencias,
            'educacion' => $educacion,
            'habilidades' => $habilidades,
            'idiomas' => $idiomas,
            'cvs' => $cvs,
            'postulaciones' => $postulaciones,
            'pasos' => VerificacionProcesoModel::PASOS,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Perfil del Postulante',
            'pageTitle' => esc($candidato['nombre'] . ' ' . $candidato['apellido']),
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'postulantes',
            'css' => ['postulante-perfil-admin.css', 'verificacion-postulantes.css'],
            'js' => ['verificacion-postulantes.js'],
        ]);
    }

    public function iniciarProceso()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $postulacionId = $this->request->getPost('postulacion_id');
        $asignadoA = $this->request->getPost('asignado_a');

        $db = \Config\Database::connect();
        $postulacion = $db->table('post_postulacion')->where('id', $postulacionId)->get()->getRowArray();

        if (!$postulacion) {
            return $this->response->setJSON(['error' => 'Postulacion no encontrada'])->setStatusCode(404);
        }

        $procesoModel = new VerificacionProcesoModel();
        $existente = $procesoModel->where('postulacion_id', $postulacionId)->first();

        if ($existente) {
            $procesoModel->update($existente['id'], [
                'asignado_a' => $asignadoA,
                'paso_actual' => 2,
                'estado' => 'en_proceso',
                'fecha_asignacion' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $procesoModel->insert([
                'postulacion_id' => $postulacionId,
                'candidato_id' => $postulacion['candidato_id'],
                'vacante_id' => $postulacion['vacante_id'],
                'asignado_a' => $asignadoA,
                'paso_actual' => 2,
                'estado' => 'en_proceso',
                'fecha_asignacion' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function avanzarPaso()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $procesoId = $this->request->getPost('proceso_id');
        $notas = $this->request->getPost('notas');

        $procesoModel = new VerificacionProcesoModel();
        $proceso = $procesoModel->find($procesoId);

        if (!$proceso) {
            return $this->response->setJSON(['error' => 'Proceso no encontrado'])->setStatusCode(404);
        }

        $pasoActual = $proceso['paso_actual'];

        // Validar: si el paso actual es 3 (documentacion), verificar que todos los requisitos tengan documentos
        if ($pasoActual == 3) {
            $db = \Config\Database::connect();
            $totalRequisitos = $db->table('vac_requisito')
                ->where('vacante_id', $proceso['vacante_id'])
                ->countAllResults();

            $totalDocumentos = $db->table('post_documento')
                ->where('postulacion_id', $proceso['postulacion_id'])
                ->countAllResults();

            if ($totalRequisitos > 0 && $totalDocumentos < $totalRequisitos) {
                return $this->response->setJSON([
                    'error' => 'No se puede avanzar: faltan documentos por subir (' . $totalDocumentos . '/' . $totalRequisitos . ' completados)'
                ])->setStatusCode(400);
            }
        }

        $nuevoPaso = min($pasoActual + 1, 6);

        $camposFecha = [
            2 => 'fecha_contacto',
            3 => 'fecha_documentos',
            4 => 'fecha_entrevista',
            5 => 'fecha_evaluacion',
            6 => 'fecha_presentacion',
        ];

        $data = [
            'paso_actual' => $nuevoPaso,
        ];

        if (isset($camposFecha[$pasoActual])) {
            $data[$camposFecha[$pasoActual]] = date('Y-m-d H:i:s');
        }

        if ($nuevoPaso >= 6) {
            $data['estado'] = 'completado';
        }

        $procesoModel->update($procesoId, $data);

        // Guardar notas del paso en el historial
        $db = \Config\Database::connect();
        $db->table('post_verificacion_historial')->insert([
            'proceso_id' => $procesoId,
            'paso' => $pasoActual,
            'notas' => $notas,
            'fecha' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'paso' => $nuevoPaso]);
    }

    public function recalcularEvaluacion()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $procesoId = $this->request->getPost('proceso_id');
        $procesoModel = new VerificacionProcesoModel();
        $proceso = $procesoModel->find($procesoId);

        if (!$proceso) {
            return $this->response->setJSON(['error' => 'Proceso no encontrado'])->setStatusCode(404);
        }

        $db = \Config\Database::connect();

        $vacante = $db->table('vac_vacante')
            ->select('vac_vacante.*, cat_nivel_experiencia.nombre as nivel_exp_nombre, cat_nivel_experiencia.anios_minimos, cat_nivel_experiencia.anios_maximos')
            ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = vac_vacante.nivel_experiencia_id', 'left')
            ->where('vac_vacante.id', $proceso['vacante_id'])
            ->get()
            ->getRowArray();

        $candidato = $db->table('cand_candidato')
            ->select('cand_candidato.*, cat_nivel_experiencia.nombre as nivel_exp_nombre, cat_nivel_experiencia.anios_minimos, cat_nivel_experiencia.anios_maximos')
            ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = cand_candidato.nivel_experiencia_id', 'left')
            ->where('cand_candidato.user_id', $proceso['candidato_id'])
            ->get()
            ->getRowArray();

        $postulacion = $db->table('post_postulacion')
            ->where('id', $proceso['postulacion_id'])
            ->get()
            ->getRowArray();

        $evalAuto = $this->calcularEvaluacionAutomatica($vacante, $candidato, $postulacion);

        // Promedio de entrevista
        $evalModel = new EntrevistaEvaluacionModel();
        $evaluacionesEntrevista = $evalModel->where('proceso_id', $procesoId)
            ->whereIn('criterio', array_keys(EntrevistaEvaluacionModel::CRITERIOS))
            ->findAll();

        $promedioEntrevista = 0;
        $total = 0;
        foreach ($evaluacionesEntrevista as $ev) {
            if ($ev['calificacion'] > 0) {
                $promedioEntrevista += $ev['calificacion'];
                $total++;
            }
        }
        $promedioEntrevista = $total > 0 ? round($promedioEntrevista / $total, 1) : 0;

        $evalAuto['puntaje_entrevista'] = [
            'score' => $total > 0 ? round($promedioEntrevista) : 0,
            'detalle' => $promedioEntrevista > 0 ? $promedioEntrevista . ' / 5 (promedio del paso 4)' : 'Sin evaluar',
        ];

        return $this->response->setJSON([
            'success' => true,
            'eval_auto' => $evalAuto,
        ]);
    }

    public function asignarPresentacion()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $procesoId = $this->request->getPost('proceso_id');
        $fechaEntrevista = $this->request->getPost('fecha_entrevista_empresa');
        $notasEmpresa = $this->request->getPost('notas_empresa');

        if (empty($fechaEntrevista)) {
            return $this->response->setJSON(['error' => 'Debe asignar una fecha de entrevista'])->setStatusCode(400);
        }

        $procesoModel = new VerificacionProcesoModel();
        $proceso = $procesoModel->find($procesoId);

        if (!$proceso) {
            return $this->response->setJSON(['error' => 'Proceso no encontrado'])->setStatusCode(404);
        }

        $procesoModel->update($procesoId, [
            'fecha_presentacion' => date('Y-m-d H:i:s', strtotime($fechaEntrevista)),
            'notas' => $notasEmpresa,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'fecha_presentacion' => date('Y-m-d H:i:s', strtotime($fechaEntrevista)),
        ]);
    }

    public function guardarEvaluacion()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $procesoId = $this->request->getPost('proceso_id');
        $evaluaciones = $this->request->getPost('evaluaciones');

        if (!$procesoId || !$evaluaciones) {
            return $this->response->setJSON(['error' => 'Datos incompletos'])->setStatusCode(400);
        }

        $evalModel = new EntrevistaEvaluacionModel();

        foreach ($evaluaciones as $criterio => $data) {
            $existente = $evalModel->where('proceso_id', $procesoId)
                ->where('criterio', $criterio)
                ->first();

            $calificacion = (int) ($data['calificacion'] ?? 0);
            $observacion = $data['observacion'] ?? null;

            if ($existente) {
                $evalModel->update($existente['id'], [
                    'calificacion' => $calificacion,
                    'observacion' => $observacion,
                ]);
            } else {
                $evalModel->insert([
                    'proceso_id' => $procesoId,
                    'criterio' => $criterio,
                    'calificacion' => $calificacion,
                    'observacion' => $observacion,
                ]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function retrocederPaso()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $procesoId = $this->request->getPost('proceso_id');
        $pasoActual = (int) $this->request->getPost('paso_actual');

        $procesoModel = new VerificacionProcesoModel();
        $proceso = $procesoModel->find($procesoId);

        if (!$proceso) {
            return $this->response->setJSON(['error' => 'Proceso no encontrado'])->setStatusCode(404);
        }

        $nuevoPaso = max($pasoActual - 1, 2);

        $data = [
            'paso_actual' => $nuevoPaso,
            'estado' => 'en_proceso',
        ];

        $camposFecha = [
            2 => 'fecha_contacto',
            3 => 'fecha_documentos',
            4 => 'fecha_entrevista',
            5 => 'fecha_evaluacion',
            6 => 'fecha_presentacion',
        ];

        if (isset($camposFecha[$pasoActual])) {
            $data[$camposFecha[$pasoActual]] = null;
        }

        $procesoModel->update($procesoId, $data);

        return $this->response->setJSON(['success' => true, 'paso' => $nuevoPaso]);
    }

    public function subirDocumento()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $postulacionId = $this->request->getPost('postulacion_id');
        $requisitoId = $this->request->getPost('requisito_id');
        $nombreDocumento = $this->request->getPost('nombre_documento');
        $archivo = $this->request->getFile('archivo');

        if (!$archivo || !$archivo->isValid()) {
            return $this->response->setJSON(['error' => 'Archivo no valido'])->setStatusCode(400);
        }

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($archivo->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['error' => 'Tipo de archivo no permitido'])->setStatusCode(400);
        }

        if ($archivo->getSizeByUnit('mb') > 5) {
            return $this->response->setJSON(['error' => 'El archivo excede 5MB'])->setStatusCode(400);
        }

        $uploadPath = WRITEPATH . 'uploads/documentos/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $nuevoNombre = $archivo->getRandomName();
        $archivo->move($uploadPath, $nuevoNombre);

        $documentoModel = new DocumentoPostulacionModel();

        $existente = $documentoModel->where('postulacion_id', $postulacionId)
            ->where('requisito_id', $requisitoId)
            ->first();

        if ($existente) {
            $documentoModel->update($existente['id'], [
                'nombre_documento' => $nombreDocumento,
                'archivo_nombre' => $nuevoNombre,
                'estado' => 'pendiente',
                'comentario_verificacion' => null,
            ]);
        } else {
            $documentoModel->insert([
                'postulacion_id' => $postulacionId,
                'requisito_id' => $requisitoId,
                'nombre_documento' => $nombreDocumento,
                'archivo_nombre' => $nuevoNombre,
                'estado' => 'pendiente',
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function verificarDocumento($documentoId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $documentoModel = new DocumentoPostulacionModel();
        $documento = $documentoModel->find($documentoId);

        if (!$documento) {
            return $this->response->setJSON(['error' => 'Documento no encontrado'])->setStatusCode(404);
        }

        $estado = $this->request->getPost('estado');
        $comentario = $this->request->getPost('comentario');

        if (!in_array($estado, ['aprobado', 'rechazado', 'pendiente'])) {
            return $this->response->setJSON(['error' => 'Estado invalido'])->setStatusCode(400);
        }

        $documentoModel->update($documentoId, [
            'estado' => $estado,
            'comentario_verificacion' => $comentario,
            'verificado_por' => session()->get('user_id'),
            'verificado_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'estado' => $estado]);
    }

    public function info()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        $sidebarSections = $this->getSidebarSections($roleSlug);

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

    private function calcularEvaluacionAutomatica(?array $vacante, ?array $candidato, array $postulacion): array
    {
        $result = [
            'match_perfil' => ['score' => 0, 'detalle' => 'Sin datos'],
            'anos_experiencia' => ['score' => 0, 'detalle' => 'Sin datos', 'cumple' => false],
            'disponibilidad' => ['score' => 0, 'detalle' => 'Sin datos'],
            'ubicacion' => ['score' => 0, 'detalle' => 'Sin datos'],
        ];

        if (!$vacante || !$candidato) {
            return $result;
        }

        // 1. Match del perfil: usar puntaje_match de post_postulacion (0-100 → 1-5 estrellas)
        $puntajeMatch = (float) ($postulacion['puntaje_match'] ?? 0);
        if ($puntajeMatch > 0) {
            $stars = max(1, min(5, ceil($puntajeMatch / 20)));
            $result['match_perfil'] = [
                'score' => $stars,
                'detalle' => $puntajeMatch . '% de match',
            ];
        } else {
            $result['match_perfil'] = [
                'score' => 0,
                'detalle' => 'No calculado',
            ];
        }

        // 2. Años de experiencia: comparar anios_experiencia de la vacante con nivel del candidato
        $aniosRequeridos = (int) ($vacante['anios_experiencia'] ?? 0);
        $aniosCandidatoMin = (int) ($candidato['anios_minimos'] ?? 0);
        $aniosCandidatoMax = (int) ($candidato['anios_maximos'] ?? 0);

        if ($aniosRequeridos > 0) {
            $cumple = $aniosCandidatoMin >= $aniosRequeridos;
            if ($cumple) {
                $score = 5;
                if ($aniosCandidatoMin >= $aniosRequeridos * 2) {
                    $score = 5;
                } elseif ($aniosCandidatoMin >= $aniosRequeridos) {
                    $score = 4;
                }
            } else {
                $dif = $aniosRequeridos - $aniosCandidatoMin;
                if ($dif <= 1) $score = 3;
                elseif ($dif <= 3) $score = 2;
                else $score = 1;
            }
            $result['anos_experiencia'] = [
                'score' => $score,
                'detalle' => "Requiere {$aniosRequeridos} años, candidato tiene {$aniosCandidatoMin}-{$aniosCandidatoMax} años",
                'cumple' => $cumple,
            ];
        } else {
            $result['anos_experiencia'] = [
                'score' => 5,
                'detalle' => 'Sin requisito mínimo de años',
                'cumple' => true,
            ];
        }

        // 3. Disponibilidad: inmediata=5, 15_dias=4, 30_dias=3, a_convenir=2
        $dispMap = [
            'inmediata' => 5,
            '15_dias' => 4,
            '30_dias' => 3,
            'a_convenir' => 2,
        ];
        $dispCandidato = $candidato['disponibilidad'] ?? 'a_convenir';
        $result['disponibilidad'] = [
            'score' => $dispMap[$dispCandidato] ?? 2,
            'detalle' => ucfirst(str_replace('_', ' ', $dispCandidato)),
        ];

        // 4. Ubicacion: comparar ciudad/region del candidato con la vacante
        $modalidad = $vacante['modalidad'] ?? 'presencial';
        if ($modalidad === 'remoto') {
            $result['ubicacion'] = [
                'score' => 5,
                'detalle' => 'Vacante remota - sin restriccion de ubicacion',
            ];
        } else {
            $ciudadMatch = !empty($candidato['ciudad']) && !empty($vacante['ciudad'])
                && strtolower($candidato['ciudad']) === strtolower($vacante['ciudad']);
            $regionMatch = !empty($candidato['region']) && !empty($vacante['region'])
                && strtolower($candidato['region']) === strtolower($vacante['region']);

            if ($ciudadMatch) {
                $result['ubicacion'] = [
                    'score' => 5,
                    'detalle' => "Misma ciudad: {$candidato['ciudad']}",
                ];
            } elseif ($regionMatch) {
                $result['ubicacion'] = [
                    'score' => 4,
                    'detalle' => "Misma region: {$candidato['region']}",
                ];
            } elseif ($modalidad === 'hibrido') {
                $result['ubicacion'] = [
                    'score' => 3,
                    'detalle' => 'Vacante hibrida - ubicacion parcial',
                ];
            } else {
                $result['ubicacion'] = [
                    'score' => 1,
                    'detalle' => "Candidato: {$candidato['ciudad']} / Vacante: {$vacante['ciudad']}",
                ];
            }
        }

        return $result;
    }

    private function getSidebarSections(string $roleSlug): array
    {
        $iconHome = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        $iconUsers = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
        $iconBriefcase = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
        $iconChart = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>';
        $iconCheck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/><path d="M21 3v6h-6"/></svg>';
        $iconSettings = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>';
        $iconInfo = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
        $iconCrm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>';

        $sections = [
            [
                'title' => '',
                'links' => [
                    ['key' => 'dashboard', 'url' => 'admin', 'label' => 'Inicio', 'icon' => $iconHome],
                    ['key' => 'crm-pipeline', 'url' => 'admin/crm', 'label' => 'CRM Pipeline', 'icon' => $iconCrm],
                    ['key' => 'usuarios', 'url' => 'admin/usuarios', 'label' => 'Usuarios', 'icon' => $iconUsers],
                ],
            ],
            [
                'title' => 'Gestion',
                'links' => [
                    ['key' => 'vacantes', 'url' => 'admin/vacantes', 'label' => 'Vacantes', 'icon' => $iconBriefcase],
                    ['key' => 'postulantes', 'url' => 'admin/postulantes', 'label' => 'Postulantes', 'icon' => $iconCheck],
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

        if ($roleSlug === 'admin') {
            $sections[] = [
                'title' => 'Sistema',
                'links' => [
                    ['key' => 'config', 'url' => 'admin/config', 'label' => 'Configuracion', 'icon' => $iconSettings],
                ],
            ];
        }

        return $sections;
    }
}
