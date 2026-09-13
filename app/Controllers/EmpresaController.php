<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmpresaModel;
use App\Models\VacanteModel;
use App\Models\CategoriaModel;
use App\Models\PostulacionModel;
use App\Models\IncidenciaModel;
use App\Models\EncuestaModel;
use App\Models\EncuestaRespuestaModel;
use App\Libraries\GeminiAI;

class EmpresaController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();

        $planEmpresa = null;
        if ($empresa) {
            $db = \Config\Database::connect();
            $planEmpresa = $db->table('crm_contrato')
                ->select('crm_contrato.*, crm_plan.nombre as plan_nombre, crm_plan.slug as plan_slug, crm_plan.max_vacantes, crm_plan.precio_mensual')
                ->join('crm_plan', 'crm_plan.id = crm_contrato.plan_id')
                ->where('crm_contrato.empresa_id', $empresa['id'])
                ->where('crm_contrato.estado', 'activo')
                ->get()
                ->getRowArray();
        }

        $vacanteModel = new VacanteModel();
        $postulacionModel = new PostulacionModel();

        $vacantesActivas = 0;
        $totalPostulantes = 0;
        $pendientesRevision = 0;
        $vacantesRecientes = [];
        $postulantesRecientes = [];
        $encuestasPendientes = 0;

        if ($empresa) {
            $db = \Config\Database::connect();

            $vacantesActivas = $vacanteModel->where('empresa_id', $empresa['id'])
                ->where('estado', 'publicada')
                ->countAllResults();

            $totalPostulantes = $db->table('post_postulacion')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->where('vac_vacante.empresa_id', $empresa['id'])
                ->countAllResults();

            $pendientesRevision = $db->table('post_postulacion')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->where('vac_vacante.empresa_id', $empresa['id'])
                ->where('post_postulacion.estado', 'enviada')
                ->countAllResults();

            $vacantesRecientes = $vacanteModel->where('empresa_id', $empresa['id'])
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();

            $postulantesRecientes = $db->table('post_postulacion')
                ->select('post_postulacion.id, post_postulacion.estado, post_postulacion.created_at,
                    vac_vacante.titulo as vacante_titulo,
                    auth_user.nombre, auth_user.apellido')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
                ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
                ->where('vac_vacante.empresa_id', $empresa['id'])
                ->orderBy('post_postulacion.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $encuestaModel = new EncuestaModel();
            $encuestasPendientes = $encuestaModel->where('empresa_id', $empresa['id'])
                ->where('estado', 'abierta')
                ->countAllResults();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/dashboard', [
            'empresa' => $empresa,
            'planEmpresa' => $planEmpresa,
            'vacantesActivas' => $vacantesActivas,
            'totalPostulantes' => $totalPostulantes,
            'pendientesRevision' => $pendientesRevision,
            'vacantesRecientes' => $vacantesRecientes,
            'postulantesRecientes' => $postulantesRecientes,
            'encuestasPendientes' => $encuestasPendientes,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Panel Empresa',
            'pageTitle' => 'Panel Empresa',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'dashboard',
            'css' => [],
        ]);
    }

    public function planes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();
        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();

        $planEmpresa = null;
        $planes = [];
        $vacantesActivas = 0;

        if ($empresa) {
            $db = \Config\Database::connect();

            $planEmpresa = $db->table('crm_contrato')
                ->select('crm_contrato.*, crm_plan.nombre as plan_nombre, crm_plan.slug as plan_slug, crm_plan.max_vacantes, crm_plan.precio_mensual')
                ->join('crm_plan', 'crm_plan.id = crm_contrato.plan_id')
                ->where('crm_contrato.empresa_id', $empresa['id'])
                ->where('crm_contrato.estado', 'activo')
                ->get()
                ->getRowArray();

            $planes = $db->table('crm_plan')
                ->where('estado', 'activo')
                ->orderBy('precio_mensual', 'ASC')
                ->get()
                ->getResultArray();

            $vacanteModel = new VacanteModel();
            $vacantesActivas = $vacanteModel->where('empresa_id', $empresa['id'])
                ->where('estado', 'publicada')
                ->countAllResults();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/planes', [
            'planEmpresa' => $planEmpresa,
            'planes' => $planes,
            'vacantesActivas' => $vacantesActivas,
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

    public function vacantes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();
        $vacanteModel = new VacanteModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();
        $vacantes = [];
        if ($empresa) {
            $vacantes = $vacanteModel->where('empresa_id', $empresa['id'])->orderBy('created_at', 'DESC')->findAll();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/vacantes', [
            'vacantes' => $vacantes,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Mis Vacantes',
            'pageTitle' => 'Mis Vacantes',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'vacantes',
            'css' => [],
        ]);
    }

    public function verVacante($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();
        $vacanteModel = new VacanteModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();
        $vacante = $vacanteModel->find($id);

        if (!$vacante || !$empresa || $vacante['empresa_id'] != $empresa['id']) {
            return redirect()->to('empresa/vacantes')->with('error', 'Vacante no encontrada.');
        }

        $db = \Config\Database::connect();

        $totalPostulantes = $db->table('post_postulacion')
            ->where('vacante_id', $id)
            ->countAllResults();

        $postulantesVerificados = $db->table('post_postulacion')
            ->join('post_verificacion_proceso', 'post_verificacion_proceso.postulacion_id = post_postulacion.id', 'inner')
            ->where('post_postulacion.vacante_id', $id)
            ->where('post_verificacion_proceso.estado', 'completado')
            ->countAllResults();

        $postulantesEnProceso = $db->table('post_postulacion')
            ->join('post_verificacion_proceso', 'post_verificacion_proceso.postulacion_id = post_postulacion.id', 'inner')
            ->where('post_postulacion.vacante_id', $id)
            ->where('post_verificacion_proceso.estado', 'en_proceso')
            ->countAllResults();

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/vacante-detalle', [
            'vacante' => $vacante,
            'totalPostulantes' => $totalPostulantes,
            'postulantesVerificados' => $postulantesVerificados,
            'postulantesEnProceso' => $postulantesEnProceso,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Detalle de Vacante',
            'pageTitle' => esc($vacante['titulo']),
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'vacantes',
            'css' => ['vacante-detalle.css'],
        ]);
    }

    public function crearVacante()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();
        $categoriaModel = new CategoriaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();
        if (!$empresa) {
            return redirect()->to('empresa/perfil')->with('error', 'Completa primero el perfil de tu empresa.');
        }

        $categorias = $categoriaModel->where('estado', 'activo')->findAll();

        $db = \Config\Database::connect();
        $requisitos = $db->table('cat_requisito')->orderBy('nombre')->get()->getResultArray();
        $habilidades = $db->table('cat_habilidad')->where('estado', 'activo')->orderBy('nombre')->get()->getResultArray();
        $nivelesEdu = $db->table('cat_nivel_educacion')->where('estado', 'activo')->orderBy('orden')->get()->getResultArray();

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/crear-vacante', [
            'categorias' => $categorias,
            'empresa' => $empresa,
            'requisitos' => $requisitos,
            'habilidades' => $habilidades,
            'nivelesEdu' => $nivelesEdu,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Publicar Vacante',
            'pageTitle' => 'Publicar Vacante',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'crear-vacante',
            'css' => ['ubicacion.css', 'crear-vacante.css'],
            'js' => ['ubicacion.js', 'crear-vacante.js'],
        ]);
    }

    public function sugerirVacante()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $titulo = $this->request->getPost('titulo');
        $tipo = $this->request->getPost('tipo'); // 'descripcion' o 'funciones'

        if (empty($titulo)) {
            return $this->response->setJSON(['error' => 'El titulo es obligatorio'])->setStatusCode(400);
        }

        $gemini = new GeminiAI();
        if (!$gemini->hasApiKey()) {
            return $this->response->setJSON(['error' => 'API key no configurada'])->setStatusCode(500);
        }

        if ($tipo === 'funciones') {
            $prompt = "Eres un reclutador experto. Para el puesto de \"{$titulo}\", genera una lista de 5-8 funciones principales en formato de lista con guiones (-), sin markdown, sin titulo, solo las funciones. Responde en español, de forma concisa y profesional.";
        } else {
            $prompt = "Eres un reclutador experto. Para el puesto de \"{$titulo}\", genera una descripcion de vacante atractiva y profesional de 2-3 parrafos. Incluye un resumen del puesto, el contexto de la empresa y lo que se espera del candidato. Responde en español, sin markdown, solo texto plano.";
        }

        $body = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,
            ],
        ]);

        $apiKey = env('GEMINI_API_KEY', '');
        $model = 'gemini-3.6-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post($url, [
                'body' => $body,
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody(), true);
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($text)) {
                return $this->response->setJSON(['error' => 'No se pudo generar la sugerencia'])->setStatusCode(500);
            }

            return $this->response->setJSON(['texto' => trim($text)]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function guardarVacante()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();
        $vacanteModel = new VacanteModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();
        if (!$empresa) {
            return redirect()->to('empresa/perfil')->with('error', 'Completa primero el perfil de tu empresa.');
        }

        $titulo = $this->request->getPost('titulo');
        $slug = url_title($titulo, '-', true) . '-' . $empresa['id'] . '-' . rand(100, 999);
        $db = \Config\Database::connect();

        $vacanteData = [
            'empresa_id' => $empresa['id'],
            'categoria_id' => $this->request->getPost('categoria_id'),
            'titulo' => $titulo,
            'slug' => $slug,
            'descripcion' => $this->request->getPost('descripcion'),
            'funciones' => $this->request->getPost('funciones'),
            'ciudad' => $this->request->getPost('ciudad'),
            'region' => $this->request->getPost('region'),
            'modalidad' => $this->request->getPost('modalidad'),
            'salario_min' => $this->request->getPost('salario_min') ?: null,
            'salario_max' => $this->request->getPost('salario_max') ?: null,
            'moneda' => 'USD',
            'anios_experiencia' => $this->request->getPost('anios_experiencia') ?: 0,
            'vacantes_disponibles' => $this->request->getPost('vacantes_disponibles') ?: 1,
            'max_postulantes' => $this->request->getPost('max_postulantes') ?: 0,
            'estado' => $this->request->getPost('estado') ?: 'publicada',
            'fecha_publicacion' => ($this->request->getPost('estado') ?: 'publicada') === 'publicada' ? date('Y-m-d H:i:s') : null,
        ];

        $vacanteModel->insert($vacanteData);
        $vacanteId = $vacanteModel->getInsertID();

        // Guardar requisitos
        $requisitosSel = $this->request->getPost('requisitos');
        if (!empty($requisitosSel) && is_array($requisitosSel)) {
            foreach ($requisitosSel as $reqId) {
                $tipo = $this->request->getPost('req_tipo_' . $reqId) ?? 'obligatorio';
                $db->table('vac_requisito')->insert([
                    'vacante_id' => $vacanteId,
                    'requisito_id' => $reqId,
                    'tipo' => $tipo,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Guardar habilidades
        $habilidadesSel = $this->request->getPost('habilidades');
        if (!empty($habilidadesSel) && is_array($habilidadesSel)) {
            foreach ($habilidadesSel as $habId) {
                $nivel = $this->request->getPost('hab_nivel_' . $habId) ?? 'intermedio';
                $tipo = $this->request->getPost('hab_tipo_' . $habId) ?? 'obligatorio';
                $db->table('vac_habilidad')->insert([
                    'vacante_id' => $vacanteId,
                    'habilidad_id' => $habId,
                    'nivel_requerido' => $nivel,
                    'tipo' => $tipo,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to('empresa/vacantes')->with('info', 'Vacante publicada correctamente.');
    }

    public function postulantes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();
        $vacanteModel = new VacanteModel();
        $postulacionModel = new PostulacionModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();
        $vacantes = [];
        $postulaciones = [];

        if ($empresa) {
            $vacantes = $vacanteModel->where('empresa_id', $empresa['id'])->orderBy('created_at', 'DESC')->findAll();

            $db = \Config\Database::connect();
            $postulaciones = $db->table('post_postulacion')
                ->select('post_postulacion.*, vac_vacante.titulo as vacante_titulo, vac_vacante.slug as vacante_slug, auth_user.nombre, auth_user.apellido, auth_user.email, cand_candidato.profesion')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
                ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
                ->where('vac_vacante.empresa_id', $empresa['id'])
                ->orderBy('post_postulacion.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // Cargar evaluaciones del proceso de verificacion
            foreach ($postulaciones as &$p) {
                $proceso = $db->table('post_verificacion_proceso')
                    ->where('postulacion_id', $p['id'])
                    ->get()
                    ->getRowArray();

                $p['proceso'] = $proceso;
                $p['evaluaciones'] = [];
                $p['puntaje_total'] = 0;
                $p['recomendacion'] = null;
                $p['fecha_entrevista_empresa'] = null;

                if ($proceso) {
                    $evals = $db->table('post_entrevista_evaluacion')
                        ->where('proceso_id', $proceso['id'])
                        ->get()
                        ->getResultArray();

                    $p['evaluaciones'] = $evals;

                    // Calcular puntaje total (promedio de criterios finales * 20 = 0-100)
                    $criteriosFinales = ['match_perfil', 'puntaje_entrevista', 'anos_experiencia', 'disponibilidad', 'ubicacion'];
                    $suma = 0;
                    $count = 0;
                    foreach ($criteriosFinales as $criterio) {
                        foreach ($evals as $ev) {
                            if ($ev['criterio'] === $criterio && $ev['calificacion'] > 0) {
                                $suma += $ev['calificacion'];
                                $count++;
                                break;
                            }
                        }
                    }
                    $p['puntaje_total'] = $count > 0 ? round(($suma / $count / 5) * 100) : 0;

                    // Obtener recomendacion
                    foreach ($evals as $ev) {
                        if ($ev['criterio'] === 'recomendacion_final') {
                            $p['recomendacion'] = (int) $ev['calificacion'];
                            break;
                        }
                    }

                    // Fecha de presentacion
                    $p['fecha_entrevista_empresa'] = $proceso['fecha_presentacion'] ?? null;
                }
            }
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/postulantes', [
            'vacantes' => $vacantes,
            'postulaciones' => $postulaciones,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Postulantes',
            'pageTitle' => 'Postulantes',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'postulantes',
            'css' => ['postulantes-empresa.css'],
        ]);
    }

    public function verPostulante($postulacionId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $db = \Config\Database::connect();

        $postulacion = $db->table('post_postulacion')
            ->select('post_postulacion.*, vac_vacante.titulo as vacante_titulo, post_verificacion_proceso.fecha_presentacion as fecha_entrevista')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->join('post_verificacion_proceso', 'post_verificacion_proceso.postulacion_id = post_postulacion.id', 'left')
            ->where('post_postulacion.id', $postulacionId)
            ->get()
            ->getRowArray();

        if (!$postulacion || !$empresa) {
            return redirect()->to('empresa/postulantes')->with('error', 'Postulacion no encontrada.');
        }

        $vacante = $db->table('vac_vacante')->where('id', $postulacion['vacante_id'])->get()->getRowArray();
        if (!$vacante || $vacante['empresa_id'] != $empresa['id']) {
            return redirect()->to('empresa/postulantes')->with('error', 'Postulacion no encontrada.');
        }

        $candidatoId = $postulacion['candidato_id'];

        $candidato = $db->table('cand_candidato')
            ->select('cand_candidato.*, auth_user.nombre, auth_user.email, auth_user.telefono, auth_user.avatar,
                cat_nivel_experiencia.nombre as nivel_exp_nombre')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = cand_candidato.nivel_experiencia_id', 'left')
            ->where('cand_candidato.id', $candidatoId)
            ->get()
            ->getRowArray();

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
            ->get()
            ->getResultArray();

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/postulante-detalle', [
            'postulacion' => $postulacion,
            'candidato' => $candidato,
            'experiencias' => $experiencias,
            'educacion' => $educacion,
            'habilidades' => $habilidades,
            'idiomas' => $idiomas,
            'cvs' => $cvs,
            'fechaEntrevista' => $postulacion['fecha_entrevista'] ?? null,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Perfil del Postulante',
            'pageTitle' => esc($candidato['nombre'] ?? '') . ' ' . esc($candidato['apellidos'] ?? ''),
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'postulantes',
            'css' => ['postulante-detalle.css'],
            'js' => ['postulante-estado.js'],
        ]);
    }

    public function cambiarEstadoPostulante($postulacionId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autenticado']);
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos']);
        }

        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $db = \Config\Database::connect();

        $postulacion = $db->table('post_postulacion')
            ->select('post_postulacion.*, vac_vacante.empresa_id')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->where('post_postulacion.id', $postulacionId)
            ->get()
            ->getRowArray();

        if (!$postulacion || !$empresa || $postulacion['empresa_id'] != $empresa['id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Postulacion no encontrada']);
        }

        $nuevoEstado = $this->request->getJSON()->estado ?? $this->request->getPost('estado');

        $estadosValidos = ['en_proceso', 'rechazado', 'contratado'];
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estado invalido']);
        }

        $db->table('post_postulacion')
            ->where('id', $postulacionId)
            ->update(['estado' => $nuevoEstado, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['success' => true, 'estado' => $nuevoEstado]);
    }

    public function perfil()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $empresa = $empresaModel->where('user_id', $userId)->first();
        $user = $userModel->find($userId);

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/perfil', [
            'empresa' => $empresa,
            'user' => $user,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Perfil Empresa',
            'pageTitle' => 'Perfil Empresa',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'perfil',
            'css' => [],
        ]);
    }

    public function guardarPerfil()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $empresaModel = new EmpresaModel();

        $userId = session()->get('user_id');
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userModel->update($userId, [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
        ]);

        $empresa = $empresaModel->where('user_id', $userId)->first();

        $empresaData = [
            'razon_social' => $this->request->getPost('razon_social'),
            'ruc' => $this->request->getPost('ruc'),
            'rubro' => $this->request->getPost('rubro'),
            'sitio_web' => $this->request->getPost('sitio_web'),
            'descripcion' => $this->request->getPost('descripcion'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
            'ciudad' => $this->request->getPost('ciudad'),
            'region' => $this->request->getPost('region'),
            'pais' => $this->request->getPost('pais'),
        ];

        if ($empresa) {
            $empresaModel->update($empresa['id'], $empresaData);
        } else {
            $empresaData['user_id'] = $userId;
            $empresaModel->insert($empresaData);
        }

        session()->set([
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
        ]);

        return redirect()->to('empresa/perfil')->with('info', 'Perfil actualizado correctamente.');
    }

    public function atencionCliente()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userId = session()->get('user_id');
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $incidenciaModel = new IncidenciaModel();
        $incidencias = [];
        if ($empresa) {
            $incidencias = $incidenciaModel->where('empresa_id', $empresa['id'])
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/atencion-cliente', [
            'incidencias' => $incidencias,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Atencion al Cliente',
            'pageTitle' => 'Atencion al Cliente',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'atencion',
            'css' => ['atencion-cliente.css'],
        ]);
    }

    public function guardarIncidencia()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userId = session()->get('user_id');
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        if (!$empresa) {
            return redirect()->to('empresa/perfil')->with('error', 'Completa primero el perfil de tu empresa.');
        }

        $incidenciaModel = new IncidenciaModel();
        $incidenciaModel->insert([
            'empresa_id' => $empresa['id'],
            'user_id' => $userId,
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'categoria' => $this->request->getPost('categoria'),
            'prioridad' => $this->request->getPost('prioridad'),
            'estado' => 'abierta',
        ]);

        return redirect()->to('empresa/atencion')->with('info', 'Incidencia registrada correctamente. Te contactaremos pronto.');
    }

    public function cerrarVacante($vacanteId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $userId = session()->get('user_id');
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel->find($vacanteId);

        if (!$vacante || $vacante['empresa_id'] != $empresa['id']) {
            return redirect()->to('empresa/vacantes')->with('error', 'Vacante no encontrada.');
        }

        $vacanteModel->update($vacanteId, ['estado' => 'cerrada', 'fecha_cierre' => date('Y-m-d H:i:s')]);

        // Generar encuesta si hay postulantes aceptados
        $db = \Config\Database::connect();
        $aceptados = $db->table('post_postulacion')
            ->where('vacante_id', $vacanteId)
            ->where('estado', 'aceptada')
            ->countAllResults();

        if ($aceptados > 0) {
            $encuestaModel = new EncuestaModel();
            $encuestaExistente = $encuestaModel->where('vacante_id', $vacanteId)->first();
            if (!$encuestaExistente) {
                $encuestaModel->insert([
                    'empresa_id' => $empresa['id'],
                    'vacante_id' => $vacanteId,
                    'estado' => 'abierta',
                ]);
            }
        }

        return redirect()->to('empresa/encuestas')->with('info', 'Vacante cerrada. Se ha generado una encuesta para calificar a los postulantes seleccionados.');
    }

    public function encuestas()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userId = session()->get('user_id');
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $encuestas = [];
        if ($empresa) {
            $db = \Config\Database::connect();
            $encuestas = $db->table('enc_encuesta')
                ->select('enc_encuesta.*, vac_vacante.titulo as vacante_titulo,
                    (SELECT COUNT(*) FROM post_postulacion WHERE post_postulacion.vacante_id = enc_encuesta.vacante_id AND post_postulacion.estado = "aceptada") as total_postulantes')
                ->join('vac_vacante', 'vac_vacante.id = enc_encuesta.vacante_id')
                ->where('enc_encuesta.empresa_id', $empresa['id'])
                ->orderBy('enc_encuesta.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        $sidebarSections = $this->getSidebarSections();

        $content = view('empresa/encuestas', [
            'encuestas' => $encuestas,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Encuestas',
            'pageTitle' => 'Encuestas',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'encuestas',
            'css' => ['encuestas.css'],
            'js' => ['encuestas.js'],
        ]);
    }

    public function obtenerEncuestaForm($encuestaId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $encuestaModel = new EncuestaModel();
        $encuesta = $encuestaModel->find($encuestaId);

        if (!$encuesta || $encuesta['empresa_id'] != $empresa['id']) {
            return $this->response->setJSON(['error' => 'Encuesta no encontrada'])->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $vacante = $db->table('vac_vacante')->where('id', $encuesta['vacante_id'])->get()->getRowArray();

        $postulantes = $db->table('post_postulacion')
            ->select('post_postulacion.id as postulacion_id, post_postulacion.candidato_id,
                auth_user.nombre, auth_user.apellido, cand_candidato.profesion')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('post_postulacion.vacante_id', $encuesta['vacante_id'])
            ->where('post_postulacion.estado', 'aceptada')
            ->get()
            ->getResultArray();

        $html = view('empresa/encuesta-form', [
            'encuesta_id' => $encuestaId,
            'vacante_titulo' => $vacante['titulo'] ?? '',
            'postulantes' => $postulantes,
        ]);

        return $this->response->setJSON(['html' => $html]);
    }

    public function guardarEncuesta($encuestaId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if ($roleSlug !== 'empresa') {
            return redirect()->to('')->with('error', 'No tienes acceso a esta seccion.');
        }

        $userId = session()->get('user_id');
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->where('user_id', $userId)->first();

        $encuestaModel = new EncuestaModel();
        $encuesta = $encuestaModel->find($encuestaId);

        if (!$encuesta || $encuesta['empresa_id'] != $empresa['id']) {
            return redirect()->to('empresa/encuestas')->with('error', 'Encuesta no encontrada.');
        }

        $respuestaModel = new EncuestaRespuestaModel();
        $postulantes = $this->request->getPost('postulantes');

        if (!empty($postulantes) && is_array($postulantes)) {
            foreach ($postulantes as $p) {
                $respuestaModel->insert([
                    'encuesta_id' => $encuestaId,
                    'postulacion_id' => $p['postulacion_id'],
                    'candidato_id' => $p['candidato_id'],
                    'puntualidad' => (int)($p['puntualidad'] ?? 0),
                    'profesionalismo' => (int)($p['profesionalismo'] ?? 0),
                    'aptitud_tecnica' => (int)($p['aptitud_tecnica'] ?? 0),
                    'comunicacion' => (int)($p['comunicacion'] ?? 0),
                    'recomendacion' => (int)($p['recomendacion'] ?? 0),
                    'comentario' => $p['comentario'] ?? null,
                ]);
            }
        }

        $encuestaModel->update($encuestaId, ['estado' => 'respondida']);

        return redirect()->to('empresa/encuestas')->with('info', 'Encuesta enviada correctamente. Gracias por tu feedback.');
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
        $iconBriefcase = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
        $iconUser = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        $iconUsers = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
        $iconPlus = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
        $iconInfo = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
        $iconHeadset = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/><path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>';
        $iconSurvey = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>';
        $iconPlan = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>';

        return [
            [
                'title' => '',
                'links' => [
                    ['key' => 'dashboard', 'url' => 'empresa', 'label' => 'Inicio', 'icon' => $iconHome],
                    ['key' => 'vacantes', 'url' => 'empresa/vacantes', 'label' => 'Mis Vacantes', 'icon' => $iconBriefcase],
                    ['key' => 'perfil', 'url' => 'empresa/perfil', 'label' => 'Perfil Empresa', 'icon' => $iconUser],
                ],
            ],
            [
                'title' => 'Gestion',
                'links' => [
                    ['key' => 'postulantes', 'url' => 'empresa/postulantes', 'label' => 'Postulantes', 'icon' => $iconUsers],
                    ['key' => 'crear-vacante', 'url' => 'empresa/vacante/crear', 'label' => 'Publicar Vacante', 'icon' => $iconPlus],
                    ['key' => 'encuestas', 'url' => 'empresa/encuestas', 'label' => 'Encuestas', 'icon' => $iconSurvey],
                ],
            ],
            [
                'title' => 'Plataforma',
                'links' => [
                    ['key' => 'planes', 'url' => 'empresa/planes', 'label' => 'Planes', 'icon' => $iconPlan],
                    ['key' => 'atencion', 'url' => 'empresa/atencion', 'label' => 'Atencion al Cliente', 'icon' => $iconHeadset],
                    ['key' => 'info', 'url' => 'empresa/info', 'label' => 'Informacion CONEX', 'icon' => $iconInfo],
                ],
            ],
        ];
    }
}
