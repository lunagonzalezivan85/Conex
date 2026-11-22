<?php

namespace App\Controllers;

use App\Models\CandidatoModel;
use App\Models\EmpresaModel;
use App\Models\UserModel;

/**
 * Mensajeria web interna (paridad con la app).
 *
 * Reutiliza el modelo de datos de la app:
 * - srv_contacto: la conversacion (servicio_id + user_id del "cliente"/empresa)
 * - srv_contacto_mensaje: el hilo (emisor_tipo: cliente|prestador|sistema)
 *
 * Sirve para empresa<->candidato y prestador<->solicitante, segun el lado
 * que ocupe el usuario logueado en cada conversacion.
 */
class ChatController extends BaseController
{
    private function user(): ?array
    {
        if (!session()->get('isLoggedIn')) {
            return null;
        }
        return (new UserModel())->find(session()->get('user_id'));
    }

    private function roleSlug(): string
    {
        return (new UserModel())->getRoleSlug(session()->get('role_id')) ?? '';
    }

    /** @return array{contacto:array, lado:string, candidato:?array}|null */
    private function contexto(int $contactoId, array $user): ?array
    {
        $db = \Config\Database::connect();
        $contacto = $db->table('srv_contacto')
            ->select('srv_contacto.*, srv_servicio.candidato_id as servicio_candidato_id, srv_servicio.oficio')
            ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
            ->where('srv_contacto.id', $contactoId)
            ->get()->getRowArray();
        if (!$contacto) {
            return null;
        }

        $candidato = (new CandidatoModel())->where('user_id', $user['id'])->first();
        $lado = null;
        if ($candidato && (int)$contacto['servicio_candidato_id'] === (int)$candidato['id']) {
            $lado = 'prestador';
        } elseif ((int)$contacto['user_id'] === (int)$user['id']) {
            $lado = 'cliente';
        }
        if (!$lado) {
            return null;
        }

        return ['contacto' => $contacto, 'lado' => $lado, 'candidato' => $candidato];
    }

    /** GET /empresa/chat | /candidato/mensajes — lista de conversaciones */
    public function index()
    {
        $user = $this->user();
        if (!$user) {
            return redirect()->to('login');
        }
        $rol = $this->roleSlug();
        $db = \Config\Database::connect();

        $candidato = (new CandidatoModel())->where('user_id', $user['id'])->first();

        $builder = $db->table('srv_contacto')
            ->select('srv_contacto.id, srv_contacto.user_id, srv_contacto.nombre, srv_contacto.estado,
                srv_contacto.created_at, srv_contacto.updated_at, srv_servicio.oficio,
                TRIM(CONCAT(COALESCE(u2.nombre, ""), " ", COALESCE(u2.apellido, ""))) AS prestador_nombre,
                (SELECT mensaje FROM srv_contacto_mensaje m WHERE m.contacto_id = srv_contacto.id ORDER BY m.id DESC LIMIT 1) AS ultimo')
            ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
            ->join('cand_candidato', 'cand_candidato.id = srv_servicio.candidato_id', 'left')
            ->join('auth_user u2', 'u2.id = cand_candidato.user_id', 'left')
            ->groupStart()
                ->where('srv_contacto.user_id', $user['id']);
        if ($candidato) {
            $builder->orWhere('srv_servicio.candidato_id', $candidato['id']);
        }
        $conversaciones = $builder->groupEnd()
            ->orderBy('srv_contacto.updated_at', 'DESC')
            ->limit(100)
            ->get()->getResultArray();

        // Lado y nombre de la contraparte por conversacion
        foreach ($conversaciones as &$c) {
            if ((int)$c['user_id'] === (int)$user['id']) {
                $c['lado'] = 'cliente';
                $c['otro'] = trim($c['prestador_nombre'] ?? '') ?: ($c['oficio'] ?? 'Conversacion');
            } else {
                $c['lado'] = 'prestador';
                $c['otro'] = $c['nombre'];
            }
        }
        unset($c);

        $content = view('chat/lista', [
            'conversaciones' => $conversaciones,
            'rol'            => $rol,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Mensajes',
            'pageTitle'       => 'Mensajes',
            'roleSlug'        => $rol,
            'sidebarSections' => $this->sidebar($rol),
            'activeSection'   => 'mensajes',
            'css'             => ['chat.css'],
        ]);
    }

    /** GET /chat/{id} — hilo de conversacion */
    public function ver($id)
    {
        $user = $this->user();
        if (!$user) {
            return redirect()->to('login');
        }
        $ctx = $this->contexto((int)$id, $user);
        if (!$ctx) {
            $dest = $this->roleSlug() === 'empresa' ? 'empresa/chat' : 'candidato/mensajes';
            return redirect()->to($dest)->with('error', 'Conversacion no encontrada.');
        }
        $rol = $this->roleSlug();
        $db = \Config\Database::connect();

        $mensajes = $db->table('srv_contacto_mensaje')
            ->where('contacto_id', (int)$id)
            ->orderBy('created_at', 'ASC')
            ->get()->getResultArray();

        // Nombre de la contraparte
        if ($ctx['lado'] === 'cliente') {
            $cand = $db->table('cand_candidato')
                ->select('auth_user.nombre, auth_user.apellido')
                ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
                ->where('cand_candidato.id', $ctx['contacto']['servicio_candidato_id'])
                ->get()->getRowArray();
            $otro = $cand ? trim($cand['nombre'] . ' ' . $cand['apellido']) : ($ctx['contacto']['oficio'] ?? 'Contacto');
        } else {
            $otro = $ctx['contacto']['nombre'];
        }

        $content = view('chat/hilo', [
            'contacto' => $ctx['contacto'],
            'mensajes' => $mensajes,
            'lado'     => $ctx['lado'],
            'otro'     => $otro,
            'rol'      => $rol,
        ]);

        return view('layouts/panel', [
            'content'         => $content,
            'title'           => 'Chat - CONEX',
            'pageTitle'       => 'Mensajes',
            'roleSlug'        => $rol,
            'sidebarSections' => $this->sidebar($rol),
            'activeSection'   => 'mensajes',
            'css'             => ['chat.css'],
        ]);
    }

    /** POST /chat/{id}/mensaje — enviar mensaje en el hilo */
    public function enviar($id)
    {
        $user = $this->user();
        if (!$user) {
            return redirect()->to('login');
        }
        $ctx = $this->contexto((int)$id, $user);
        if (!$ctx) {
            return redirect()->back()->with('error', 'Conversacion no encontrada.');
        }
        $contacto = $ctx['contacto'];

        if (($contacto['estado'] ?? '') === 'finalizado') {
            return redirect()->to('chat/' . (int)$id)->with('error', 'Esta conversacion fue finalizada.');
        }
        if (($contacto['estado'] ?? '') === 'rechazado') {
            return redirect()->to('chat/' . (int)$id)->with('error', 'Esta solicitud fue rechazada.');
        }

        $mensaje = trim((string)$this->request->getPost('mensaje'));
        $precio = $this->request->getPost('precio_propuesto');
        $precio = ($precio !== null && $precio !== '') ? (float)$precio : null;
        if ($mensaje === '') {
            return redirect()->to('chat/' . (int)$id);
        }

        $db = \Config\Database::connect();
        $db->table('srv_contacto_mensaje')->insert([
            'contacto_id'      => (int)$id,
            'emisor_tipo'      => $ctx['lado'],
            'emisor_nombre'    => trim($user['nombre'] . ' ' . $user['apellido']) ?: 'Usuario',
            'mensaje'          => $mensaje,
            'precio_propuesto' => $precio,
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $update = [
            'respuesta'       => $mensaje,
            'fecha_respuesta' => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        if ($precio !== null) {
            $update['precio_propuesto'] = $precio;
        }
        $db->table('srv_contacto')->where('id', (int)$id)->update($update);

        // Notificar a la contraparte (si tiene cuenta)
        $destino = null;
        if ($ctx['lado'] === 'prestador') {
            $destino = (int)$contacto['user_id'] ?: null;
        } else {
            $row = $db->table('cand_candidato')->select('user_id')
                ->where('id', (int)$contacto['servicio_candidato_id'])->get()->getRowArray();
            $destino = $row['user_id'] ?? null;
        }
        if ($destino && $destino !== (int)$user['id'] && $db->tableExists('not_notificacion')) {
            $nombre = trim($user['nombre'] . ' ' . $user['apellido']) ?: 'Un usuario';
            $db->table('not_notificacion')->insert([
                'user_id'    => $destino,
                'tipo'       => 'mensaje',
                'titulo'     => "Nuevo mensaje de {$nombre}",
                'mensaje'    => mb_substr($mensaje, 0, 150),
                'url'        => '/app/solicitudes',
                'leida'      => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('chat/' . (int)$id);
    }

    /** POST /chat/{id}/finalizar — cierre permanente (cualquiera de las partes) */
    public function finalizar($id)
    {
        $user = $this->user();
        if (!$user) {
            return redirect()->to('login');
        }
        $ctx = $this->contexto((int)$id, $user);
        if (!$ctx) {
            return redirect()->back()->with('error', 'Conversacion no encontrada.');
        }

        $db = \Config\Database::connect();
        $db->table('srv_contacto')->where('id', (int)$id)->update([
            'estado'     => 'finalizado',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $quien = trim($user['nombre'] . ' ' . $user['apellido']) ?: 'El usuario';
        $db->table('srv_contacto_mensaje')->insert([
            'contacto_id'   => (int)$id,
            'emisor_tipo'   => 'sistema',
            'emisor_nombre' => 'CONEX',
            'mensaje'       => "{$quien} finalizo esta conversacion. Ya no se pueden enviar mensajes.",
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('chat/' . (int)$id)->with('info', 'Conversacion finalizada.');
    }

    /** POST /empresa/chat/iniciar/{postulacion_id} — empresa contacta candidato */
    public function iniciarPostulante($postulacionId)
    {
        $user = $this->user();
        if (!$user || $this->roleSlug() !== 'empresa') {
            return redirect()->to('login');
        }
        $empresa = (new EmpresaModel())->where('user_id', $user['id'])->first();
        if (!$empresa) {
            return redirect()->back()->with('error', 'Completa el perfil de tu empresa.');
        }

        $db = \Config\Database::connect();
        $postulacion = $db->table('post_postulacion')
            ->select('post_postulacion.*, vac_vacante.titulo as vacante_titulo, vac_vacante.empresa_id')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->where('post_postulacion.id', (int)$postulacionId)
            ->get()->getRowArray();

        if (!$postulacion || (int)$postulacion['empresa_id'] !== (int)$empresa['id']) {
            return redirect()->back()->with('error', 'Postulacion no encontrada.');
        }

        $candidatoId = (int)$postulacion['candidato_id'];
        $candidato = $db->table('cand_candidato')->where('id', $candidatoId)->get()->getRowArray();
        if (!$candidato) {
            return redirect()->back()->with('error', 'Candidato no encontrado.');
        }
        $candUser = $db->table('auth_user')->where('id', $candidato['user_id'])->get()->getRowArray();

        // Servicio del candidato (auto "Perfil profesional" como en la app)
        $servicio = $db->table('srv_servicio')->where('candidato_id', $candidatoId)->get()->getRowArray();
        if (!$servicio) {
            $db->table('srv_servicio')->insert([
                'candidato_id' => $candidatoId,
                'oficio'       => $candidato['profesion'] ?: 'Perfil profesional',
                'descripcion'  => 'Servicios y postulacion de ' . ($candUser['nombre'] ?? 'candidato'),
                'ciudad'       => $candidato['ciudad'] ?: 'Managua',
                'precio_tipo'  => 'a_convenir',
                'estado'       => 'activo',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $servicioId = (int)$db->insertID();
        } else {
            $servicioId = (int)$servicio['id'];
        }

        $contacto = $db->table('srv_contacto')
            ->where('servicio_id', $servicioId)
            ->where('user_id', $user['id'])
            ->get()->getRowArray();

        if ($contacto) {
            if (($contacto['estado'] ?? '') === 'finalizado') {
                return redirect()->to('chat/' . $contacto['id'])
                    ->with('error', 'Esta conversacion fue finalizada.');
            }
            return redirect()->to('chat/' . $contacto['id']);
        }

        $initMsg = "Hola {$candUser['nombre']}, te contactamos de {$empresa['razon_social']} sobre tu postulacion a {$postulacion['vacante_titulo']}. Queremos coordinar contigo.";

        $db->table('srv_contacto')->insert([
            'servicio_id' => $servicioId,
            'user_id'     => $user['id'],
            'nombre'      => $empresa['razon_social'] ?: trim($user['nombre'] . ' ' . $user['apellido']),
            'telefono'    => $user['telefono'] ?: ($empresa['telefono'] ?? ''),
            'mensaje'     => $initMsg,
            'estado'      => 'aceptado',
            'ip_hash'     => hash('sha256', $this->request->getIPAddress()),
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $contactoId = (int)$db->insertID();

        $db->table('srv_contacto_mensaje')->insert([
            'contacto_id'   => $contactoId,
            'emisor_tipo'   => 'cliente',
            'emisor_nombre' => $empresa['razon_social'] ?: $user['nombre'],
            'mensaje'       => $initMsg,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        if ($db->tableExists('not_notificacion')) {
            $db->table('not_notificacion')->insert([
                'user_id'    => (int)$candUser['id'],
                'tipo'       => 'mensaje',
                'titulo'     => 'Mensaje de empresa',
                'mensaje'    => "{$empresa['razon_social']} te ha enviado un mensaje de contacto",
                'url'        => '/app/solicitudes',
                'leida'      => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('chat/' . $contactoId);
    }

    /** Sidebar reducido por rol con "Mensajes" activo */
    private function sidebar(string $rol): array
    {
        $iconHome = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        $iconChat = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
        $iconUser = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        $iconFile = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
        $iconUsers = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';

        if ($rol === 'empresa') {
            $msgUrl = 'empresa/chat';
            $links = [
                ['key' => 'dashboard', 'url' => 'empresa', 'label' => 'Inicio', 'icon' => $iconHome],
                ['key' => 'postulantes', 'url' => 'empresa/postulantes', 'label' => 'Postulantes', 'icon' => $iconUsers],
                ['key' => 'mensajes', 'url' => $msgUrl, 'label' => 'Mensajes', 'icon' => $iconChat],
                ['key' => 'perfil', 'url' => 'empresa/perfil', 'label' => 'Perfil Empresa', 'icon' => $iconUser],
            ];
        } else {
            $msgUrl = 'candidato/mensajes';
            $links = [
                ['key' => 'dashboard', 'url' => 'candidato', 'label' => 'Inicio', 'icon' => $iconHome],
                ['key' => 'perfil', 'url' => 'candidato/perfil', 'label' => 'Mi Perfil', 'icon' => $iconUser],
                ['key' => 'postulaciones', 'url' => 'candidato/postulaciones', 'label' => 'Mis Postulaciones', 'icon' => $iconFile],
                ['key' => 'mensajes', 'url' => $msgUrl, 'label' => 'Mensajes', 'icon' => $iconChat],
                ['key' => 'solicitudes', 'url' => 'candidato/solicitudes', 'label' => 'Solicitudes', 'icon' => $iconFile],
            ];
        }

        return [['title' => '', 'links' => $links]];
    }
}
