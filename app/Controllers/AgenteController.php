<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LeadModel;
use App\Models\CandidatoModel;
use App\Models\VacanteModel;
use App\Models\PostulacionModel;
use App\Models\VerificacionProcesoModel;

class AgenteController extends BaseController
{
    /**
     * Verifica acceso para rol asesor o admin
     */
    private function checkAccess(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso al portal de agentes.');
        }

        return null;
    }

    private function getSidebar(string $roleSlug): array
    {
        $iconHome = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        $iconUsers = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
        $iconBriefcase = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
        $iconCheck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.39 0 4.68.94 6.36 2.64"/><path d="M21 3v6h-6"/></svg>';
        $iconCrm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>';
        $iconUserPlus = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>';
        $iconTool = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>';
        $iconInfo = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';

        return [
            [
                'title' => 'Portal del Agente',
                'links' => [
                    ['key' => 'agente-dashboard', 'url' => 'agente', 'label' => 'Mi Tablero', 'icon' => $iconHome],
                    ['key' => 'agente-captar', 'url' => 'agente/captar', 'label' => 'Captar Empresas/Socios', 'icon' => $iconCrm],
                    ['key' => 'agente-verificacion', 'url' => 'agente/verificacion', 'label' => 'Consola de Verificación', 'icon' => $iconCheck],
                    ['key' => 'agente-registro-campo', 'url' => 'agente/registro-campo', 'label' => 'Registro en Campo (Feria)', 'icon' => $iconUserPlus],
                    ['key' => 'agente-prestadores', 'url' => 'agente/prestadores', 'label' => 'Validar Prestadores', 'icon' => $iconTool],
                ],
            ],
            [
                'title' => 'Gestión General',
                'links' => [
                    ['key' => 'crm-pipeline', 'url' => 'admin/crm', 'label' => 'Pipeline CRM Completo', 'icon' => $iconCrm],
                    ['key' => 'vacantes', 'url' => 'admin/vacantes', 'label' => 'Vacantes', 'icon' => $iconBriefcase],
                    ['key' => 'usuarios', 'url' => 'admin/usuarios', 'label' => 'Directorio de Usuarios', 'icon' => $iconUsers],
                    ['key' => 'info', 'url' => 'admin/info', 'label' => 'Información CONEX', 'icon' => $iconInfo],
                ],
            ],
        ];
    }

    /**
     * Dashboard del Agente / Asesor
     */
    public function index()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $db = \Config\Database::connect();
        $userId = (int)session()->get('user_id');
        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        // Leads asignados al agente
        $leadsQuery = $db->table('crm_lead');
        if ($roleSlug === 'asesor') {
            $leadsQuery->where('asesor_id', $userId);
        }
        $totalLeads = $leadsQuery->countAllResults();

        $leadsCerrados = $db->table('crm_lead')
            ->where('estado', 'cerrado');
        if ($roleSlug === 'asesor') {
            $leadsCerrados->where('asesor_id', $userId);
        }
        $totalGanados = $leadsCerrados->countAllResults();

        // Postulaciones pendientes de verificación
        $postulacionesPendientes = $db->table('post_postulacion')
            ->whereIn('estado', ['enviada', 'en_revision'])
            ->countAllResults();

        // Total colocaciones (contratados o aceptados)
        $totalColocados = $db->table('post_postulacion')
            ->whereIn('estado', ['aceptada', 'contratada'])
            ->countAllResults();

        // Prestadores sin verificar
        $prestadoresPendientes = $db->table('cand_candidato')
            ->where('ofrece_servicios', 1)
            ->where('prestador_verificado', 0)
            ->countAllResults();

        // Total candidatos en la plataforma
        $totalCandidatos = $db->table('cand_candidato')->countAllResults();

        // Prospectos / Leads recientes del agente
        $leadsRecientesQuery = $db->table('crm_lead')
            ->select('crm_lead.*, crm_plan.nombre as plan_nombre')
            ->join('crm_plan', 'crm_plan.id = crm_lead.plan_interes_id', 'left')
            ->orderBy('crm_lead.created_at', 'DESC')
            ->limit(6);
        if ($roleSlug === 'asesor') {
            $leadsRecientesQuery->where('crm_lead.asesor_id', $userId);
        }
        $leadsRecientes = $leadsRecientesQuery->get()->getResultArray();

        // Postulantes urgentes por revisar
        $postulantesUrgentes = $db->table('post_postulacion')
            ->select('post_postulacion.*, auth_user.nombre, auth_user.apellido, auth_user.telefono,
                      cand_candidato.id as candidato_id, cand_candidato.profesion,
                      vac_vacante.titulo as vacante_titulo, emp_empresa.razon_social as empresa_nombre')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->whereIn('post_postulacion.estado', ['enviada', 'en_revision'])
            ->orderBy('post_postulacion.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Vacantes con mayor demanda
        $vacantesActivas = $db->table('vac_vacante')
            ->select('vac_vacante.*,
                      COALESCE(emp_empresa.razon_social, vac_vacante.empresa_externa) as empresa_nombre,
                      (SELECT COUNT(*) FROM post_postulacion WHERE post_postulacion.vacante_id = vac_vacante.id) as total_postulantes')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->where('vac_vacante.estado', 'publicada')
            ->orderBy('vac_vacante.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $content = view('agente/dashboard', [
            'stats' => [
                'totalLeads'              => $totalLeads,
                'totalGanados'            => $totalGanados,
                'postulacionesPendientes' => $postulacionesPendientes,
                'totalColocados'          => $totalColocados,
                'prestadoresPendientes'   => $prestadoresPendientes,
                'totalCandidatos'         => $totalCandidatos,
            ],
            'leadsRecientes'      => $leadsRecientes,
            'postulantesUrgentes' => $postulantesUrgentes,
            'vacantesActivas'     => $vacantesActivas,
            'roleSlug'            => $roleSlug,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Portal del Agente',
            'pageTitle'       => 'Portal del Agente / Asesor',
            'roleSlug'        => $roleSlug,
            'sidebarSections' => $this->getSidebar($roleSlug),
            'activeSection'   => 'agente-dashboard',
            'css'             => ['agente-portal.css'],
        ]);
    }

    /**
     * Módulo 1: Captar Empresas y Socios
     */
    public function captar()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $db = \Config\Database::connect();
        $userId = (int)session()->get('user_id');
        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        $query = $db->table('crm_lead')
            ->select('crm_lead.*, crm_plan.nombre as plan_nombre, auth_user.nombre as asesor_nombre, auth_user.apellido as asesor_apellido')
            ->join('crm_plan', 'crm_plan.id = crm_lead.plan_interes_id', 'left')
            ->join('auth_user', 'auth_user.id = crm_lead.asesor_id', 'left')
            ->orderBy('crm_lead.created_at', 'DESC');

        if ($roleSlug === 'asesor') {
            $query->where('crm_lead.asesor_id', $userId);
        }
        $leads = $query->get()->getResultArray();

        $planes = $db->table('crm_plan')->where('estado', 'activo')->get()->getResultArray();

        $content = view('agente/captar', [
            'leads'    => $leads,
            'planes'   => $planes,
            'estados'  => LeadModel::ESTADOS,
            'origenes' => LeadModel::ORIGENES,
            'roleSlug' => $roleSlug,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Captar Empresas y Socios',
            'pageTitle'       => 'Captación de Empresas y Socios / Aliados',
            'roleSlug'        => $roleSlug,
            'sidebarSections' => $this->getSidebar($roleSlug),
            'activeSection'   => 'agente-captar',
            'css'             => ['agente-portal.css'],
        ]);
    }

    /**
     * Guardar nuevo prospecto (Empresa o Socio)
     */
    public function guardarLead()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $rules = [
            'empresa_nombre'  => 'required|min_length[2]|max_length[150]',
            'nombre_contacto' => 'required|min_length[2]|max_length[150]',
            'email_contacto'  => 'permit_empty|valid_email|max_length[150]',
            'telefono_contacto' => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor verifica los campos obligatorios.');
        }

        $userId = (int)session()->get('user_id');
        $tipoProspecto = $this->request->getPost('tipo_prospecto') ?: 'empresa';
        $notasOriginales = trim($this->request->getPost('notas') ?? '');
        $notasFinales = "[Tipo: " . strtoupper($tipoProspecto) . "] " . $notasOriginales;

        $leadModel = new LeadModel();
        $leadModel->insert([
            'tipo'              => 'nuevo',
            'empresa_nombre'    => trim($this->request->getPost('empresa_nombre')),
            'nombre_contacto'   => trim($this->request->getPost('nombre_contacto')),
            'email_contacto'    => trim($this->request->getPost('email_contacto') ?? '') ?: 'prospecto@conex.ni',
            'telefono_contacto' => trim($this->request->getPost('telefono_contacto') ?? ''),
            'rubro'             => trim($this->request->getPost('rubro') ?? ''),
            'tamano_empresa'    => $this->request->getPost('tamano_empresa') ?: 'pequena',
            'origen'            => $this->request->getPost('origen') ?: 'directo',
            'plan_interes_id'   => $this->request->getPost('plan_interes_id') ?: null,
            'estado'            => 'prospecto',
            'asesor_id'         => $userId,
            'notas'             => $notasFinales,
        ]);

        return redirect()->to('agente/captar')->with('info', 'Prospecto captado exitosamente y registrado en tu pipeline.');
    }

    /**
     * Módulo 2: Consola de Verificación de Candidatos
     */
    public function verificacion()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        $postulantes = $db->table('post_postulacion')
            ->select('post_postulacion.id as postulacion_id, post_postulacion.estado, post_postulacion.created_at as fecha_postulacion,
                      cand_candidato.id as candidato_id, cand_candidato.profesion, cand_candidato.ciudad, cand_candidato.disponibilidad,
                      auth_user.id as user_id, auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.telefono, auth_user.avatar,
                      vac_vacante.id as vacante_id, vac_vacante.titulo as vacante_titulo,
                      COALESCE(emp_empresa.razon_social, vac_vacante.empresa_externa) as empresa_nombre,
                      post_verificacion_proceso.paso_actual')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->join('post_verificacion_proceso', 'post_verificacion_proceso.postulacion_id = post_postulacion.id', 'left')
            ->orderBy('post_postulacion.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $content = view('agente/verificacion', [
            'postulantes' => $postulantes,
            'roleSlug'    => $roleSlug,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Consola de Verificación',
            'pageTitle'       => 'Consola de Verificación de Candidatos',
            'roleSlug'        => $roleSlug,
            'sidebarSections' => $this->getSidebar($roleSlug),
            'activeSection'   => 'agente-verificacion',
            'css'             => ['agente-portal.css'],
        ]);
    }

    /**
     * Módulo 3: Registro en Campo (Ferias de Empleo)
     */
    public function registroCampo()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        // Vacantes abiertas para auto-postulación en el acto
        $vacantes = $db->table('vac_vacante')
            ->select('vac_vacante.id, vac_vacante.titulo, vac_vacante.ciudad, vac_vacante.modalidad,
                      COALESCE(emp_empresa.razon_social, vac_vacante.empresa_externa) as empresa_nombre')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->where('vac_vacante.estado', 'publicada')
            ->orderBy('vac_vacante.titulo', 'ASC')
            ->get()
            ->getResultArray();

        $candidatoRegistrado = session()->getFlashdata('candidato_registrado');

        $content = view('agente/registro-campo', [
            'vacantes'            => $vacantes,
            'candidatoRegistrado' => $candidatoRegistrado,
            'roleSlug'            => $roleSlug,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Registro en Campo',
            'pageTitle'       => 'Registro Express en Campo y Ferias',
            'roleSlug'        => $roleSlug,
            'sidebarSections' => $this->getSidebar($roleSlug),
            'activeSection'   => 'agente-registro-campo',
            'css'             => ['agente-portal.css'],
        ]);
    }

    /**
     * Guardar candidato desde campo
     */
    public function guardarCandidatoCampo()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $nombre = trim($this->request->getPost('nombre') ?? '');
        $apellido = trim($this->request->getPost('apellido') ?? '');
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $cedula = trim($this->request->getPost('cedula') ?? '');
        $profesion = trim($this->request->getPost('profesion') ?? '');
        $ciudad = trim($this->request->getPost('ciudad') ?? 'Managua');
        $salario = (float)($this->request->getPost('salario_esperado_usd') ?? 0);
        $disponibilidad = $this->request->getPost('disponibilidad') ?: 'inmediata';
        $ofreceServicios = $this->request->getPost('ofrece_servicios') ? 1 : 0;
        $vacanteId = (int)$this->request->getPost('vacante_id');

        if (empty($nombre) || empty($telefono)) {
            return redirect()->back()->withInput()->with('error', 'El nombre y el teléfono son indispensables para el registro.');
        }

        // Generar email único si no se provee
        $email = trim($this->request->getPost('email') ?? '');
        if (empty($email)) {
            $slugClean = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($nombre));
            $telClean = preg_replace('/[^0-9]/', '', $telefono);
            $email = $slugClean . ($telClean ? substr($telClean, -4) : rand(100, 999)) . '@conex.ni';
        }

        // Generar username único
        $username = strtolower(explode(' ', trim($nombre))[0]) . rand(100, 9999);
        $tempPassword = 'Conex' . rand(1000, 9999) . '!';

        $db = \Config\Database::connect();
        $db->transStart();

        $userModel = new UserModel();
        $roleCandidatoId = $userModel->getRoleIdBySlug('candidato') ?? 4;

        $userId = $userModel->insert([
            'role_id'         => $roleCandidatoId,
            'nombre'          => $nombre,
            'apellido'        => $apellido,
            'usuario'         => $username,
            'email'           => $email,
            'password'        => $tempPassword,
            'telefono'        => $telefono,
            'estado'          => 'activo',
            'perfil_completo' => 0,
        ]);

        $candidatoModel = new CandidatoModel();
        $candidatoId = $candidatoModel->insert([
            'user_id'              => $userId,
            'apellidos'            => $apellido,
            'profesion'            => $profesion,
            'ciudad'               => $ciudad,
            'pais'                 => 'Nicaragua',
            'salario_esperado_usd' => $salario,
            'disponibilidad'       => $disponibilidad,
            'ofrece_servicios'     => $ofreceServicios,
            'sobre_mi'             => "Candidato registrado en campo/feria por el agente " . session()->get('nombre') . ($cedula ? " - Cédula: {$cedula}" : ''),
            'porcentaje_perfil'    => 50,
        ]);

        $postulacionGenerada = null;
        if ($vacanteId > 0) {
            $postModel = new PostulacionModel();
            $postId = $postModel->insert([
                'vacante_id'   => $vacanteId,
                'candidato_id' => $candidatoId,
                'estado'       => 'enviada',
            ]);

            $vacante = $db->table('vac_vacante')->where('id', $vacanteId)->get()->getRowArray();
            $postulacionGenerada = [
                'id' => $postId,
                'titulo' => $vacante['titulo'] ?? 'Vacante seleccionada',
            ];
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Error al registrar candidato.');
        }

        // Datos para credencial entregable
        $credenciales = [
            'nombre'       => $nombre . ' ' . $apellido,
            'usuario'      => $username,
            'password'     => $tempPassword,
            'telefono'     => $telefono,
            'email'        => $email,
            'profesion'    => $profesion,
            'postulacion'  => $postulacionGenerada,
            'candidato_id' => $candidatoId,
        ];

        return redirect()->to('agente/registro-campo')->with('candidato_registrado', $credenciales)->with('info', '¡Candidato registrado exitosamente!');
    }

    /**
     * Módulo 4: Validación de Prestadores de Servicios
     */
    public function prestadores()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        $prestadores = $db->table('cand_candidato')
            ->select('cand_candidato.id as candidato_id, cand_candidato.profesion, cand_candidato.ciudad,
                      cand_candidato.prestador_verificado, cand_candidato.ofrece_servicios,
                      auth_user.id as user_id, auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.telefono,
                      (SELECT COUNT(*) FROM srv_servicio WHERE srv_servicio.candidato_id = cand_candidato.id AND srv_servicio.deleted_at IS NULL) as total_servicios,
                      (SELECT GROUP_CONCAT(oficio SEPARATOR ", ") FROM srv_servicio WHERE srv_servicio.candidato_id = cand_candidato.id AND srv_servicio.deleted_at IS NULL) as oficios_lista')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('cand_candidato.ofrece_servicios', 1)
            ->where('cand_candidato.deleted_at', null)
            ->orderBy('cand_candidato.prestador_verificado', 'ASC')
            ->orderBy('cand_candidato.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        $content = view('agente/prestadores', [
            'prestadores' => $prestadores,
            'roleSlug'    => $roleSlug,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Validar Prestadores',
            'pageTitle'       => 'Validación de Prestadores de Oficios y Servicios',
            'roleSlug'        => $roleSlug,
            'sidebarSections' => $this->getSidebar($roleSlug),
            'activeSection'   => 'agente-prestadores',
            'css'             => ['agente-portal.css'],
        ]);
    }

    /**
     * Conceder o revocar verificación de prestador
     */
    public function verificarPrestador($candidatoId)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $db = \Config\Database::connect();
        $candidato = $db->table('cand_candidato')->where('id', (int)$candidatoId)->get()->getRowArray();

        if (!$candidato) {
            return redirect()->to('agente/prestadores')->with('error', 'Prestador no encontrado.');
        }

        $nuevoEstado = !empty($candidato['prestador_verificado']) ? 0 : 1;
        $db->table('cand_candidato')
            ->where('id', (int)$candidatoId)
            ->update(['prestador_verificado' => $nuevoEstado, 'updated_at' => date('Y-m-d H:i:s')]);

        $msg = $nuevoEstado ? 'Prestador verificado exitosamente con el sello oficial de CONEX.' : 'Se ha retirado la verificación del prestador.';
        return redirect()->to('agente/prestadores')->with('info', $msg);
    }
}
