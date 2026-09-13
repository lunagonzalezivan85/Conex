<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LeadModel;
use App\Models\LeadSeguimientoModel;
use App\Models\LeadServicioModel;
use App\Models\ContratoVersionModel;

class CrmController extends BaseController
{
    public function pipeline()
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
        $userId = session()->get('user_id');

        $leads = $db->table('crm_lead')
            ->select('crm_lead.*, auth_user.nombre as asesor_nombre, auth_user.apellido as asesor_apellido, crm_plan.nombre as plan_nombre')
            ->join('auth_user', 'auth_user.id = crm_lead.asesor_id', 'left')
            ->join('crm_plan', 'crm_plan.id = crm_lead.plan_interes_id', 'left')
            ->orderBy('crm_lead.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        $estados = LeadModel::ESTADOS;
        $leadsPorEstado = [];
        foreach ($estados as $key => $label) {
            $leadsPorEstado[$key] = [];
        }
        foreach ($leads as $lead) {
            if (isset($leadsPorEstado[$lead['estado']])) {
                $leadsPorEstado[$lead['estado']][] = $lead;
            }
        }

        $asesores = $db->table('auth_user')
            ->select('auth_user.id, auth_user.nombre, auth_user.apellido')
            ->join('auth_role', 'auth_role.id = auth_user.role_id')
            ->whereIn('auth_role.slug', ['admin', 'asesor'])
            ->get()
            ->getResultArray();

        $planes = $db->table('crm_plan')->where('estado', 'activo')->get()->getResultArray();

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('crm/pipeline', [
            'leadsPorEstado' => $leadsPorEstado,
            'estados' => $estados,
            'asesores' => $asesores,
            'planes' => $planes,
            'userId' => $userId,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'CRM Pipeline',
            'pageTitle' => 'CRM - Pipeline de Ventas',
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'crm-pipeline',
            'css' => ['crm.css'],
            'js' => ['crm.js'],
        ]);
    }

    public function crearLead()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $leadModel = new LeadModel();

        $data = [
            'tipo'             => $this->request->getPost('tipo') ?? 'nuevo',
            'empresa_id'       => $this->request->getPost('empresa_id') ?: null,
            'nombre_contacto'  => $this->request->getPost('nombre_contacto'),
            'email_contacto'   => $this->request->getPost('email_contacto'),
            'telefono_contacto' => $this->request->getPost('telefono_contacto'),
            'empresa_nombre'   => $this->request->getPost('empresa_nombre'),
            'rubro'            => $this->request->getPost('rubro'),
            'tamano_empresa'   => $this->request->getPost('tamano_empresa') ?: null,
            'origen'           => $this->request->getPost('origen') ?? 'web',
            'plan_interes_id'  => $this->request->getPost('plan_interes_id') ?: null,
            'estado'           => $this->request->getPost('tipo') === 'upsell' ? 'propuesta' : 'prospecto',
            'asesor_id'        => $this->request->getPost('asesor_id') ?: session()->get('user_id'),
            'notas'            => $this->request->getPost('notas'),
        ];

        if ($leadModel->insert($data)) {
            return redirect()->to('admin/crm')->with('success', 'Lead creado correctamente.');
        }
        return redirect()->back()->with('error', 'Error al crear el lead.');
    }

    public function detalleLead($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $db = \Config\Database::connect();

        $lead = $db->table('crm_lead')
            ->select('crm_lead.*, auth_user.nombre as asesor_nombre, auth_user.apellido as asesor_apellido, crm_plan.nombre as plan_nombre')
            ->join('auth_user', 'auth_user.id = crm_lead.asesor_id', 'left')
            ->join('crm_plan', 'crm_plan.id = crm_lead.plan_interes_id', 'left')
            ->where('crm_lead.id', $id)
            ->get()
            ->getRowArray();

        if (!$lead) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $seguimientos = $db->table('crm_lead_seguimiento')
            ->select('crm_lead_seguimiento.*, auth_user.nombre as asesor_nombre, auth_user.apellido as asesor_apellido')
            ->join('auth_user', 'auth_user.id = crm_lead_seguimiento.asesor_id', 'left')
            ->where('lead_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $servicios = $db->table('crm_lead_servicio')
            ->where('lead_id', $id)
            ->get()
            ->getResultArray();

        $planes = $db->table('crm_plan')->where('estado', 'activo')->get()->getResultArray();
        $asesores = $db->table('auth_user')
            ->select('auth_user.id, auth_user.nombre, auth_user.apellido')
            ->join('auth_role', 'auth_role.id = auth_user.role_id')
            ->whereIn('auth_role.slug', ['admin', 'asesor'])
            ->get()
            ->getResultArray();

        $sidebarSections = $this->getSidebarSections($roleSlug);

        $content = view('crm/detalle-lead', [
            'lead' => $lead,
            'seguimientos' => $seguimientos,
            'servicios' => $servicios,
            'planes' => $planes,
            'asesores' => $asesores,
            'estados' => LeadModel::ESTADOS,
            'origenes' => LeadModel::ORIGENES,
            'tiposSeguimiento' => LeadSeguimientoModel::TIPOS,
            'serviciosDisponibles' => LeadServicioModel::SERVICIOS,
        ]);

        return view('layouts/panel', [
            'content' => $content,
            'title' => 'Detalle Lead',
            'pageTitle' => 'CRM - ' . esc($lead['empresa_nombre'] ?? $lead['nombre_contacto']),
            'roleSlug' => $roleSlug,
            'sidebarSections' => $sidebarSections,
            'activeSection' => 'crm-pipeline',
            'css' => ['crm.css'],
        ]);
    }

    public function actualizarLead($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $leadModel = new LeadModel();

        $data = [
            'nombre_contacto'  => $this->request->getPost('nombre_contacto'),
            'email_contacto'   => $this->request->getPost('email_contacto'),
            'telefono_contacto' => $this->request->getPost('telefono_contacto'),
            'empresa_nombre'   => $this->request->getPost('empresa_nombre'),
            'rubro'            => $this->request->getPost('rubro'),
            'tamano_empresa'   => $this->request->getPost('tamano_empresa') ?: null,
            'origen'           => $this->request->getPost('origen'),
            'plan_interes_id'  => $this->request->getPost('plan_interes_id') ?: null,
            'estado'           => $this->request->getPost('estado'),
            'asesor_id'        => $this->request->getPost('asesor_id') ?: null,
            'notas'            => $this->request->getPost('notas'),
        ];

        if ($leadModel->update($id, $data)) {
            return redirect()->to('admin/crm/lead/' . $id)->with('success', 'Lead actualizado.');
        }
        return redirect()->back()->with('error', 'Error al actualizar.');
    }

    public function cambiarEstadoLead($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'No autenticado'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return $this->response->setJSON(['error' => 'Sin acceso'])->setStatusCode(403);
        }

        $nuevoEstado = $this->request->getPost('estado');

        if (!array_key_exists($nuevoEstado, LeadModel::ESTADOS)) {
            return $this->response->setJSON(['error' => 'Estado invalido'])->setStatusCode(400);
        }

        $leadModel = new LeadModel();
        $leadModel->update($id, ['estado' => $nuevoEstado]);

        return $this->response->setJSON(['success' => true]);
    }

    public function agregarSeguimiento($leadId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $segModel = new LeadSeguimientoModel();

        $data = [
            'lead_id'     => $leadId,
            'asesor_id'   => session()->get('user_id'),
            'tipo_contacto' => $this->request->getPost('tipo_contacto') ?? 'llamada',
            'comentario'  => $this->request->getPost('comentario'),
            'fecha_proxima_accion' => $this->request->getPost('fecha_proxima_accion') ?: null,
        ];

        $segModel->insert($data);

        $leadModel = new LeadModel();
        $lead = $leadModel->find($leadId);

        if ($lead && $lead['estado'] === 'prospecto') {
            $leadModel->update($leadId, ['estado' => 'contacto']);
        }

        return redirect()->to('admin/crm/lead/' . $leadId)->with('success', 'Seguimiento agregado.');
    }

    public function agregarServicio($leadId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $servModel = new LeadServicioModel();

        $data = [
            'lead_id'   => $leadId,
            'servicio'  => $this->request->getPost('servicio'),
            'cantidad'  => $this->request->getPost('cantidad') ?? 1,
            'precio'    => $this->request->getPost('precio') ?? 0,
            'incluido'  => $this->request->getPost('incluido') ? 1 : 0,
        ];

        $servModel->insert($data);

        return redirect()->to('admin/crm/lead/' . $leadId)->with('success', 'Servicio agregado.');
    }

    public function convertirLead($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $roleSlug = $userModel->getRoleSlug(session()->get('role_id'));

        if (!in_array($roleSlug, ['admin', 'asesor'])) {
            return redirect()->to('')->with('error', 'No tienes acceso.');
        }

        $db = \Config\Database::connect();
        $lead = $db->table('crm_lead')->where('id', $id)->get()->getRowArray();

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead no encontrado.');
        }

        $planId = $this->request->getPost('plan_id');
        $tipoFacturacion = $this->request->getPost('tipo_facturacion') ?? 'mensual';

        $db->transStart();

        $empresaId = $lead['empresa_id'];

        if (!$empresaId) {
            $userEmail = $lead['email_contacto'];
            $userPass = password_hash('Conex123!', PASSWORD_BCRYPT);

            $db->table('auth_user')->insert([
                'usuario'   => explode('@', $userEmail)[0],
                'email'     => $userEmail,
                'password'  => $userPass,
                'nombre'    => $lead['nombre_contacto'],
                'role_id'   => 3,
                'estado'    => 'activo',
                'perfil_completo' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $userId = $db->insertID();

            $db->table('emp_empresa')->insert([
                'user_id'      => $userId,
                'razon_social' => $lead['empresa_nombre'],
                'rubro'        => $lead['rubro'],
                'telefono'     => $lead['telefono_contacto'],
                'verificada'   => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $empresaId = $db->insertID();
        }

        $codigo = 'CTR-' . $empresaId . '-' . str_pad(date('z') + 1, 3, '0', STR_PAD_LEFT);

        $db->table('crm_contrato')->insert([
            'empresa_id'      => $empresaId,
            'plan_id'         => $planId,
            'codigo'          => $codigo,
            'tipo_facturacion' => $tipoFacturacion,
            'fecha_inicio'    => date('Y-m-d'),
            'estado'          => 'activo',
            'auto_renovar'    => 0,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
        $contratoId = $db->insertID();

        $db->table('crm_contrato_version')->insert([
            'contrato_id'    => $contratoId,
            'version'        => 1,
            'plan_id_nuevo'  => $planId,
            'tipo_facturacion_nuevo' => $tipoFacturacion,
            'precio_nuevo'   => 0,
            'motivo'         => 'Creacion de contrato',
            'modificado_por' => session()->get('user_id'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $db->table('crm_lead')->where('id', $id)->update([
            'estado'     => 'cerrado',
            'empresa_id' => $empresaId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus()) {
            return redirect()->to('admin/crm')->with('success', 'Lead convertido a empresa. Contrato creado.');
        }
        return redirect()->back()->with('error', 'Error al convertir el lead.');
    }

    public function getSidebarSections($roleSlug = 'admin')
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
