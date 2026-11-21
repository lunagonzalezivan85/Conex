<?php

namespace App\Controllers\Api;

use App\Models\CandidatoModel;
use App\Models\EmpresaModel;
use App\Models\PostulacionModel;
use App\Models\UserModel;
use App\Models\VacanteModel;

class EmpresaController extends BaseApiController
{
    /**
     * GET /api/empresa/perfil
     */
    public function perfil()
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }
        return $this->ok(['data' => ['user' => $this->authUser(), 'empresa' => $empresa]]);
    }

    /**
     * POST /api/empresa/perfil — actualiza datos de empresa + usuario.
     */
    public function actualizarPerfil()
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }

        $user = $this->authUser();
        $userId = (int)$user['id'];
        $data = $this->body();

        if (isset($data['email'])) {
            $data['email'] = strtolower(trim((string)$data['email']));
        }
        if (!empty($data['sitio_web']) && !preg_match('#^https?://#i', (string)$data['sitio_web'])) {
            $data['sitio_web'] = 'https://' . ltrim((string)$data['sitio_web'], '/');
        }

        $rules = [
            'razon_social' => 'permit_empty|min_length[3]|max_length[150]',
            'email'        => "permit_empty|valid_email|is_unique[auth_user.email,id,{$userId}]",
            'ruc'          => 'permit_empty|max_length[50]',
            'rubro'        => 'permit_empty|max_length[100]',
            'descripcion'  => 'permit_empty|max_length[2000]',
            'sitio_web'    => 'permit_empty|valid_url|max_length[255]',
            'telefono'     => 'permit_empty|min_length[8]|max_length[20]',
            'direccion'    => 'permit_empty|max_length[255]',
            'ciudad'       => 'permit_empty|max_length[100]',
            'region'       => 'permit_empty|max_length[100]',
            'pais'         => 'permit_empty|max_length[100]',
            'latitud'      => 'permit_empty|numeric',
            'longitud'     => 'permit_empty|numeric',
        ];
        $validation = service('validation');
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->fail('Datos inválidos', 422, $validation->getErrors());
        }

        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }

        $campos = ['razon_social', 'ruc', 'rubro', 'descripcion', 'sitio_web', 'telefono', 'direccion', 'ciudad', 'region', 'pais', 'latitud', 'longitud'];
        $update = [];
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $update[$campo] = $data[$campo] === '' ? null : $data[$campo];
            }
        }
        if (!empty($update)) {
            (new EmpresaModel())->update($empresa['id'], $update);
        }

        // Actualizar auth_user si viene email o telefono
        $userModel = new UserModel();
        $updateUser = [];
        if (!empty($data['email']))    $updateUser['email'] = $data['email'];
        if (!empty($data['telefono'])) $updateUser['telefono'] = $data['telefono'];
        if (!empty($updateUser)) {
            $userModel->update($userId, $updateUser);
        }

        $updatedUser = $userModel->find($userId);
        unset($updatedUser['password']);

        return $this->ok([
            'message' => 'Perfil actualizado',
            'user'    => $this->userPayload($updatedUser, 'empresa'),
            'empresa' => (new EmpresaModel())->find($empresa['id']),
        ]);
    }

    /**
     * GET /api/empresa/vacantes — lista las vacantes de la empresa.
     * Params: estado, page, per_page
     */
    public function vacantes()
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->ok(['data' => []]);
        }

        $estado = $this->request->getGet('estado');
        [$page, $perPage] = $this->paginationParams();

        $vacanteModel = new VacanteModel();
        $builder = $vacanteModel->where('vac_vacante.empresa_id', $empresa['id']);
        if (!empty($estado)) {
            $builder->where('vac_vacante.estado', $estado);
        }

        $vacantes = $builder->orderBy('vac_vacante.created_at', 'DESC')->paginate($perPage, 'default', $page);
        $pager = $vacanteModel->pager;

        // Conteo de postulantes por vacante
        $db = \Config\Database::connect();
        foreach ($vacantes as &$v) {
            $v['total_postulantes'] = $db->table('post_postulacion')
                ->where('vacante_id', $v['id'])
                ->where('deleted_at IS NULL')
                ->countAllResults();
        }

        return $this->ok([
            'data' => $vacantes,
            'meta' => [
                'current_page' => $pager->getCurrentPage(),
                'per_page'     => $perPage,
                'total'        => $pager->getTotal(),
                'last_page'    => $pager->getPageCount(),
            ],
        ]);
    }

    /**
     * POST /api/empresa/vacantes — crear vacante.
     */
    public function crearVacante()
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }

        $data = $this->body();

        // Normalizar strings vacios
        foreach (['salario_min', 'salario_max', 'region', 'latitud', 'longitud', 'funciones', 'tipo_contrato_id'] as $f) {
            if (isset($data[$f]) && $data[$f] === '') {
                $data[$f] = null;
            }
        }

        $rules = [
            'titulo'               => 'required|min_length[3]|max_length[150]',
            'descripcion'          => 'required|min_length[10]',
            'categoria_id'         => 'required|is_natural_no_zero',
            'modalidad'            => 'required|in_list[presencial,remoto,hibrido]',
            'ciudad'               => 'permit_empty|max_length[100]',
            'region'               => 'permit_empty|max_length[100]',
            'latitud'              => 'permit_empty|numeric',
            'longitud'             => 'permit_empty|numeric',
            'salario_min'          => 'permit_empty|numeric',
            'salario_max'          => 'permit_empty|numeric',
            'anios_experiencia'    => 'permit_empty|integer|greater_than_equal_to[0]',
            'vacantes_disponibles' => 'permit_empty|integer|greater_than_equal_to[1]',
            'tipo_contrato_id'     => 'permit_empty|integer',
            'funciones'            => 'permit_empty|max_length[5000]',
            'moneda'               => 'permit_empty|in_list[NIO,USD]',
            'estado'               => 'permit_empty|in_list[borrador,publicada]',
        ];
        if ($res = $this->validateApi($rules, $data)) {
            return $res;
        }

        if (!empty($data['salario_min']) && !empty($data['salario_max']) && (float)$data['salario_max'] < (float)$data['salario_min']) {
            return $this->fail('El salario máximo no puede ser menor al salario mínimo', 422, [
                'salario_max' => 'El salario máximo no puede ser menor al salario mínimo.'
            ]);
        }

        $vacanteModel = new VacanteModel();

        // Idempotencia: evitar publicaciones duplicadas en caso de doble clic o reintentos
        $reciente = $vacanteModel
            ->where('empresa_id', $empresa['id'])
            ->where('titulo', trim($data['titulo']))
            ->where('categoria_id', (int)$data['categoria_id'])
            ->where('created_at >=', date('Y-m-d H:i:s', time() - 60))
            ->orderBy('id', 'DESC')
            ->first();
        if ($reciente) {
            return $this->ok([
                'message'    => 'Vacante creada con éxito',
                'vacante_id' => (int)$reciente['id'],
                'slug'       => $reciente['slug'],
            ], 200);
        }

        // Límite de 5 vacantes de prueba
        $totalVacantes = $vacanteModel->where('empresa_id', $empresa['id'])->where('deleted_at IS NULL')->countAllResults();
        if ($totalVacantes >= 5) {
            return $this->fail('Has alcanzado el límite de 5 vacantes de tu periodo de prueba gratuito.', 403);
        }

        $ciudad = !empty($data['ciudad']) ? trim($data['ciudad']) : ($empresa['ciudad'] ?? 'Managua');
        $coordsCity = [
            'Managua'     => ['lat' => 12.1364, 'lng' => -86.2514],
            'Masaya'      => ['lat' => 11.9744, 'lng' => -86.0942],
            'Granada'     => ['lat' => 11.9299, 'lng' => -85.9560],
            'León'        => ['lat' => 12.4379, 'lng' => -86.8780],
            'Chinandega'  => ['lat' => 12.6294, 'lng' => -87.1311],
            'Matagalpa'   => ['lat' => 12.9256, 'lng' => -85.9175],
            'Estelí'      => ['lat' => 13.0919, 'lng' => -86.3538],
            'Rivas'       => ['lat' => 11.4372, 'lng' => -85.8263],
            'Carazo'      => ['lat' => 11.8500, 'lng' => -86.1990],
            'Chontales'   => ['lat' => 12.1063, 'lng' => -85.3645],
        ];

        $lat = !empty($data['latitud']) ? (float)$data['latitud'] : ($coordsCity[$ciudad]['lat'] ?? null);
        $lng = !empty($data['longitud']) ? (float)$data['longitud'] : ($coordsCity[$ciudad]['lng'] ?? null);

        $tituloClean = strtr($data['titulo'], [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n', 'ü' => 'u', 'Ü' => 'u',
        ]);
        $slug = url_title($tituloClean, '-', true) . '-' . $empresa['id'] . '-' . rand(100, 999);
        $estado = $data['estado'] ?? 'publicada';

        $insertData = [
            'empresa_id'           => $empresa['id'],
            'categoria_id'         => (int)$data['categoria_id'],
            'titulo'               => $data['titulo'],
            'slug'                 => $slug,
            'descripcion'          => $data['descripcion'],
            'funciones'            => $data['funciones'] ?? null,
            'ciudad'               => $ciudad,
            'region'               => $data['region'] ?? ($empresa['region'] ?? null),
            'latitud'              => $lat,
            'longitud'             => $lng,
            'modalidad'            => $data['modalidad'],
            'salario_min'          => !empty($data['salario_min']) ? (float)$data['salario_min'] : null,
            'salario_max'          => !empty($data['salario_max']) ? (float)$data['salario_max'] : null,
            'moneda'               => in_array($data['moneda'] ?? '', ['NIO', 'USD']) ? $data['moneda'] : 'USD',
            'anios_experiencia'    => (int)($data['anios_experiencia'] ?? 0),
            'vacantes_disponibles' => (int)($data['vacantes_disponibles'] ?? 1),
            'tipo_contrato_id'     => !empty($data['tipo_contrato_id']) ? (int)$data['tipo_contrato_id'] : null,
            'estado'               => $estado,
            'fecha_publicacion'    => $estado === 'publicada' ? date('Y-m-d H:i:s') : null,
        ];

        $insertId = $vacanteModel->insert($insertData);
        if (!$insertId) {
            return $this->fail('No se pudo guardar la vacante', 500, $vacanteModel->errors());
        }

        return $this->ok([
            'message'    => 'Vacante creada con éxito',
            'vacante_id' => $insertId,
            'slug'       => $slug,
        ], 201);
    }

    /**
     * PUT /api/empresa/vacantes/{id} — actualizar vacante propia.
     */
    public function actualizarVacante($id)
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }

        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel->find((int)$id);
        if (!$vacante || (int)$vacante['empresa_id'] !== (int)$empresa['id']) {
            return $this->fail('Vacante no encontrada', 404);
        }

        $data = $this->body();
        foreach (['salario_min', 'salario_max', 'region', 'latitud', 'longitud', 'funciones', 'tipo_contrato_id'] as $f) {
            if (isset($data[$f]) && $data[$f] === '') {
                $data[$f] = null;
            }
        }

        $rules = [
            'titulo'               => 'permit_empty|min_length[3]|max_length[150]',
            'descripcion'          => 'permit_empty|min_length[10]',
            'categoria_id'         => 'permit_empty|is_natural_no_zero',
            'modalidad'            => 'permit_empty|in_list[presencial,remoto,hibrido]',
            'ciudad'               => 'permit_empty|max_length[100]',
            'region'               => 'permit_empty|max_length[100]',
            'latitud'              => 'permit_empty|numeric',
            'longitud'             => 'permit_empty|numeric',
            'salario_min'          => 'permit_empty|numeric',
            'salario_max'          => 'permit_empty|numeric',
            'anios_experiencia'    => 'permit_empty|integer|greater_than_equal_to[0]',
            'vacantes_disponibles' => 'permit_empty|integer|greater_than_equal_to[1]',
            'tipo_contrato_id'     => 'permit_empty|integer',
            'funciones'            => 'permit_empty|max_length[5000]',
            'moneda'               => 'permit_empty|in_list[NIO,USD]',
            'estado'               => 'permit_empty|in_list[borrador,publicada,cerrada,suspendida]',
        ];
        if ($res = $this->validateApi($rules, $data)) {
            return $res;
        }

        if (!empty($data['salario_min']) && !empty($data['salario_max']) && (float)$data['salario_max'] < (float)$data['salario_min']) {
            return $this->fail('El salario máximo no puede ser menor al salario mínimo', 422, [
                'salario_max' => 'El salario máximo no puede ser menor al salario mínimo.'
            ]);
        }

        $campos = [
            'titulo', 'descripcion', 'funciones', 'categoria_id', 'modalidad',
            'ciudad', 'region', 'latitud', 'longitud', 'salario_min', 'salario_max',
            'anios_experiencia', 'vacantes_disponibles', 'tipo_contrato_id', 'estado',
            'moneda',
        ];
        $update = [];
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $update[$campo] = $data[$campo] === '' ? null : $data[$campo];
            }
        }
        // Si pasa a publicada y no tenia fecha, marcarla
        if (($update['estado'] ?? null) === 'publicada' && empty($vacante['fecha_publicacion'])) {
            $update['fecha_publicacion'] = date('Y-m-d H:i:s');
        }

        if (!empty($update)) {
            $vacanteModel->update((int)$id, $update);
        }

        return $this->ok(['message' => 'Vacante actualizada', 'data' => $vacanteModel->find((int)$id)]);
    }

    /**
     * DELETE /api/empresa/vacantes/{id} — eliminar vacante propia (soft-delete).
     */
    public function eliminarVacante($id)
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }

        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel->find((int)$id);
        if (!$vacante || (int)$vacante['empresa_id'] !== (int)$empresa['id']) {
            return $this->fail('Vacante no encontrada o no pertenece a tu empresa', 404);
        }

        $vacanteModel->delete((int)$id);

        return $this->ok([
            'message'    => 'Vacante eliminada correctamente',
            'vacante_id' => (int)$id,
        ]);
    }

    /**
     * GET /api/empresa/vacantes/{id}/postulaciones
     */
    public function postulaciones($vacanteId)
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->ok(['data' => []]);
        }

        $vacante = (new VacanteModel())->find((int)$vacanteId);
        if (!$vacante || (int)$vacante['empresa_id'] !== (int)$empresa['id']) {
            return $this->ok(['data' => []]);
        }

        $empUser = $this->authUser();
        $builder = (new PostulacionModel())
            ->select('post_postulacion.*,
                auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.avatar,
                auth_user.telefono AS auth_telefono,
                cand_candidato.profesion, cand_candidato.ciudad,
                cand_candidato.sobre_mi, cand_candidato.salario_esperado_usd AS salario_deseado,
                ne.anios_minimos AS anios_experiencia, ne.nombre AS nivel_experiencia,
                (SELECT e.titulo FROM cand_educacion e WHERE e.candidato_id = cand_candidato.id ORDER BY e.id DESC LIMIT 1) AS titulo_academico,
                (SELECT cv.archivo_path FROM cand_cv cv WHERE cv.candidato_id = cand_candidato.id ORDER BY cv.es_principal DESC, cv.id DESC LIMIT 1) AS cv_url,
                sc.id AS contacto_id, sc.estado AS contacto_estado,
                EXISTS(SELECT 1 FROM srv_contacto_mensaje cm WHERE cm.contacto_id = sc.id AND cm.emisor_tipo = \'cliente\') AS contactado')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id', 'left')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id', 'left')
            ->join('cat_nivel_experiencia ne', 'ne.id = cand_candidato.nivel_experiencia_id', 'left')
            ->join('srv_servicio ss', 'ss.candidato_id = cand_candidato.id', 'left')
            ->join('srv_contacto sc', 'sc.servicio_id = ss.id AND sc.user_id = ' . (int)$empUser['id'], 'left')
            ->where('post_postulacion.vacante_id', (int)$vacanteId)
            ->groupBy('post_postulacion.id')
            ->orderBy('post_postulacion.created_at', 'DESC');

        $postulaciones = $builder->findAll();

        return $this->ok(['data' => $postulaciones]);
    }

    /**
     * POST /api/empresa/postulaciones/{id}/estado — cambiar estado de postulacion.
     * Body: { estado }
     */
    public function cambiarEstadoPostulacion($id)
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }

        $estados = ['enviada', 'en_revision', 'entrevista', 'oferta', 'contratado', 'rechazada'];
        $rules = ['estado' => 'required|in_list[' . implode(',', $estados) . ']'];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $postulacionModel = new PostulacionModel();
        $postulacion = $postulacionModel
            ->select('post_postulacion.*, vac_vacante.titulo as vacante_titulo, vac_vacante.empresa_id')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->where('post_postulacion.id', (int)$id)
            ->where('vac_vacante.empresa_id', $empresa['id'])
            ->first();

        if (!$postulacion) {
            return $this->fail('Postulacion no encontrada', 404);
        }

        $nuevoEstado = $this->body()['estado'];
        $postulacionModel->update($postulacion['id'], ['estado' => $nuevoEstado]);

        $db = \Config\Database::connect();
        $empUser = $this->authUser();
        $candidato = $db->table('cand_candidato')->where('id', (int)$postulacion['candidato_id'])->get()->getRowArray();
        $candUserId = $candidato ? (int)$candidato['user_id'] : 0;

        // 1. Asegurar conversación en srv_contacto y enviar mensaje en srv_contacto_mensaje
        $contactoId = null;
        if ($candidato && $db->tableExists('srv_contacto') && $db->tableExists('srv_servicio')) {
            $servicio = $db->table('srv_servicio')->where('candidato_id', (int)$candidato['id'])->get()->getRowArray();
            if (!$servicio) {
                $db->table('srv_servicio')->insert([
                    'candidato_id' => (int)$candidato['id'],
                    'oficio'       => !empty($candidato['profesion']) ? $candidato['profesion'] : 'Perfil profesional',
                    'descripcion'  => 'Servicios y postulación de candidato',
                    'ciudad'       => !empty($candidato['ciudad']) ? $candidato['ciudad'] : 'Managua',
                    'precio_tipo'  => 'a_convenir',
                    'estado'       => 'activo',
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
                $servicioId = $db->insertID();
            } else {
                $servicioId = (int)$servicio['id'];
            }

            $contacto = $db->table('srv_contacto')
                ->where('servicio_id', $servicioId)
                ->where('user_id', (int)$empUser['id'])
                ->get()->getRowArray();

            if (!$contacto) {
                $db->table('srv_contacto')->insert([
                    'servicio_id' => $servicioId,
                    'user_id'     => (int)$empUser['id'],
                    'nombre'      => $empresa['razon_social'] ?: 'Empresa',
                    'telefono'    => $empresa['telefono'] ?: '505-8888-8888',
                    'mensaje'     => "Postulación para '{$postulacion['vacante_titulo']}'",
                    'estado'      => 'aceptado',
                    'ip_hash'     => hash('sha256', $this->request->getIPAddress()),
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
                $contactoId = $db->insertID();
            } else {
                $contactoId = (int)$contacto['id'];
                $db->table('srv_contacto')->where('id', $contactoId)->update([
                    'estado'     => 'aceptado',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            if ($contactoId && $db->tableExists('srv_contacto_mensaje')) {
                $chatMsg = '';
                if ($nuevoEstado === 'contratado') {
                    $chatMsg = "🎉 ¡Felicitaciones! Te hemos marcado como CONTRATADO para la vacante '{$postulacion['vacante_titulo']}'. Podemos coordinar por aquí los detalles de tu contratación e inicio.";
                } elseif ($nuevoEstado === 'en_revision') {
                    $chatMsg = "👀 Tu postulación para '{$postulacion['vacante_titulo']}' ha sido pasada a etapa de revisión.";
                } elseif ($nuevoEstado === 'rechazada') {
                    $chatMsg = "ℹ️ Tu postulación para '{$postulacion['vacante_titulo']}' no continuará en el proceso.";
                }

                if (!empty($chatMsg)) {
                    $db->table('srv_contacto_mensaje')->insert([
                        'contacto_id'   => $contactoId,
                        'emisor_tipo'   => 'cliente',
                        'emisor_nombre' => $empresa['razon_social'] ?: 'Empresa',
                        'mensaje'       => $chatMsg,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 2. Notificación al candidato en not_notificacion
        if ($candUserId > 0 && $db->tableExists('not_notificacion')) {
            $tituloNotif = 'Actualización de tu postulación';
            $msgNotif = "Tu postulación para la vacante '{$postulacion['vacante_titulo']}' ha sido actualizada a {$nuevoEstado}.";

            if ($nuevoEstado === 'contratado') {
                $tituloNotif = '¡Felicitaciones! Has sido contratado 🎉';
                $msgNotif = "¡Buenas noticias! {$empresa['razon_social']} te ha seleccionado y marcado como CONTRATADO para la vacante '{$postulacion['vacante_titulo']}'. Revisa tu chat para coordinar los detalles.";
            } elseif ($nuevoEstado === 'en_revision') {
                $tituloNotif = 'Tu postulación está en revisión 👀';
                $msgNotif = "{$empresa['razon_social']} está revisando tu postulación para la vacante '{$postulacion['vacante_titulo']}'.";
            } elseif ($nuevoEstado === 'rechazada') {
                $tituloNotif = 'Estado de tu postulación';
                $msgNotif = "Tu postulación para la vacante '{$postulacion['vacante_titulo']}' no continuará en este proceso. ¡Sigue explorando más oportunidades!";
            }

            $db->table('not_notificacion')->insert([
                'user_id'    => $candUserId,
                'tipo'       => 'postulacion',
                'titulo'     => $tituloNotif,
                'mensaje'    => $msgNotif,
                'url'        => $contactoId ? ('/app/chat?contacto_id=' . $contactoId) : '/app/solicitudes',
                'leida'      => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->ok(['message' => 'Estado actualizado', 'estado' => $nuevoEstado, 'contacto_id' => $contactoId]);
    }

    /**
     * GET /api/empresa/postulantes — lista global de todos los candidatos postulados
     * a cualquiera de las vacantes de la empresa.
     */
    public function todosPostulantes()
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->ok(['data' => []]);
        }
        $db = \Config\Database::connect();
        $q = trim((string)($this->request->getGet('q') ?? ''));
        $vacanteId = (int)($this->request->getGet('vacante_id') ?? 0);
        $estado = trim((string)($this->request->getGet('estado') ?? ''));

        $builder = $db->table('post_postulacion')
            ->select('post_postulacion.*,
                vac_vacante.titulo as vacante_titulo, vac_vacante.slug as vacante_slug,
                auth_user.id as user_id, auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.avatar,
                cand_candidato.profesion, cand_candidato.ciudad, auth_user.telefono,
                cand_candidato.latitud, cand_candidato.longitud, cand_candidato.porcentaje_perfil,
                cand_candidato.sobre_mi')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('vac_vacante.empresa_id', (int)$empresa['id'])
            ->where('post_postulacion.deleted_at IS NULL');

        if ($vacanteId > 0) {
            $builder->where('post_postulacion.vacante_id', $vacanteId);
        }
        if ($estado !== '') {
            $builder->where('post_postulacion.estado', $estado);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('auth_user.nombre', $q)
                ->orLike('auth_user.apellido', $q)
                ->orLike('cand_candidato.profesion', $q)
                ->orLike('vac_vacante.titulo', $q)
                ->groupEnd();
        }

        $postulantes = $builder->orderBy('post_postulacion.created_at', 'DESC')->get()->getResultArray();

        // Buscar si ya tienen conversacion activa en srv_contacto
        $empUser = $this->authUser();
        $candIds = array_unique(array_filter(array_column($postulantes, 'candidato_id')));
        $contactosMap = [];
        if (!empty($candIds) && $db->tableExists('srv_contacto') && $db->tableExists('srv_servicio')) {
            $contacts = $db->table('srv_contacto')
                ->select('srv_contacto.id as contacto_id, srv_servicio.candidato_id')
                ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
                ->where('srv_contacto.user_id', $empUser['id'])
                ->whereIn('srv_servicio.candidato_id', $candIds)
                ->get()->getResultArray();
            foreach ($contacts as $c) {
                $contactosMap[(int)$c['candidato_id']] = (int)$c['contacto_id'];
            }
        }

        foreach ($postulantes as &$p) {
            $p['nombre_completo'] = trim($p['nombre'] . ' ' . $p['apellido']);
            $p['avatar_url'] = !empty($p['avatar']) ? (str_starts_with($p['avatar'], 'http') ? $p['avatar'] : base_url('uploads/' . $p['avatar'])) : null;
            $p['nueva'] = ($p['estado'] === 'enviada');
            $contactoId = $contactosMap[(int)$p['candidato_id']] ?? null;
            $p['contacto_id'] = $contactoId;

            // Cláusula 4 Política de Privacidad: "El número de teléfono solo se revela entre postulante y
            // empleador / cliente cuando una solicitud de contacto ha sido formalmente aceptada por ambas partes."
            $contactoAceptado = !empty($contactoId) || in_array($p['estado'], ['en_revision', 'entrevista', 'oferta', 'contratado'], true);
            if (!$contactoAceptado) {
                $p['telefono'] = null;
            }
        }
        unset($p);

        return $this->ok(['data' => $postulantes]);
    }

    /**
     * POST /api/empresa/iniciar-chat-postulante — abre o inicia una conversación
     * directa con un postulante desde la app móvil.
     * Body: { postulacion_id: number, mensaje?: string }
     */
    public function iniciarChatPostulante()
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa el perfil de tu empresa', 422);
        }
        $db = \Config\Database::connect();
        $body = $this->body();
        $postulacionId = (int)($body['postulacion_id'] ?? 0);
        $candidatoId = (int)($body['candidato_id'] ?? 0);
        $vacanteTitulo = '';

        if ($postulacionId > 0) {
            $postulacion = $db->table('post_postulacion')
                ->select('post_postulacion.*, vac_vacante.titulo as vacante_titulo, vac_vacante.empresa_id')
                ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                ->where('post_postulacion.id', $postulacionId)
                ->get()->getRowArray();

            if ($postulacion && (int)$postulacion['empresa_id'] === (int)$empresa['id']) {
                $candidatoId = (int)$postulacion['candidato_id'];
                $vacanteTitulo = $postulacion['vacante_titulo'];
            } else {
                // Verificar si $postulacionId fue enviado como ID de candidato directo
                $candCheck = $db->table('cand_candidato')->where('id', $postulacionId)->get()->getRowArray();
                if ($candCheck) {
                    $candidatoId = (int)$candCheck['id'];
                }
            }
        }

        if (!$candidatoId && !empty($body['user_id'])) {
            $candCheck = $db->table('cand_candidato')->where('user_id', (int)$body['user_id'])->get()->getRowArray();
            if ($candCheck) {
                $candidatoId = (int)$candCheck['id'];
            }
        }

        $candidato = $db->table('cand_candidato')->where('id', $candidatoId)->get()->getRowArray();
        if (!$candidato) {
            return $this->fail('Candidato no encontrado', 404);
        }
        $candUser = $db->table('auth_user')->where('id', $candidato['user_id'])->get()->getRowArray();
        $empUser = $this->authUser();

        // Buscar o crear servicio para este candidato
        $servicio = $db->table('srv_servicio')->where('candidato_id', $candidatoId)->get()->getRowArray();
        if (!$servicio) {
            $db->table('srv_servicio')->insert([
                'candidato_id' => $candidatoId,
                'oficio'       => $candidato['profesion'] ?: 'Perfil profesional',
                'descripcion'  => 'Servicios y postulación de ' . ($candUser['nombre'] ?? 'candidato'),
                'ciudad'       => $candidato['ciudad'] ?: 'Managua',
                'precio_tipo'  => 'a_convenir',
                'estado'       => 'activo',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $servicioId = $db->insertID();
        } else {
            $servicioId = (int)$servicio['id'];
        }

        // Buscar si ya existe conversacion / srv_contacto entre la empresa y este servicio
        $contacto = $db->table('srv_contacto')
            ->where('servicio_id', $servicioId)
            ->where('user_id', $empUser['id'])
            ->get()->getRowArray();

        $vacTxt = !empty($vacanteTitulo) ? " sobre tu postulación a {$vacanteTitulo}" : "";
        $initMsg = !empty($body['mensaje']) ? trim($body['mensaje']) : "Hola {$candUser['nombre']}, te contactamos de {$empresa['razon_social']}{$vacTxt}. Queremos coordinar contigo.";

        if (!$contacto) {
            $db->table('srv_contacto')->insert([
                'servicio_id' => $servicioId,
                'user_id'     => $empUser['id'],
                'nombre'      => $empresa['razon_social'] ?: ($empUser['nombre'] . ' ' . $empUser['apellido']),
                'telefono'    => $empUser['telefono'] ?: ($empresa['telefono'] ?: '505-8888-8888'),
                'mensaje'     => $initMsg,
                'estado'      => 'aceptado',
                'ip_hash'     => hash('sha256', $this->request->getIPAddress()),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $contactoId = $db->insertID();

            if ($db->tableExists('srv_contacto_mensaje')) {
                $db->table('srv_contacto_mensaje')->insert([
                    'contacto_id'   => $contactoId,
                    'emisor_tipo'   => 'cliente',
                    'emisor_nombre' => $empresa['razon_social'] ?: $empUser['nombre'],
                    'mensaje'       => $initMsg,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }

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
        } else {
            $contactoId = (int)$contacto['id'];
            if (($contacto['estado'] ?? '') === 'finalizado') {
                return $this->fail('Esta conversación fue finalizada. Ya no se pueden enviar mensajes.', 423);
            }
            if (!empty($body['mensaje']) && $db->tableExists('srv_contacto_mensaje')) {
                $db->table('srv_contacto_mensaje')->insert([
                    'contacto_id'   => $contactoId,
                    'emisor_tipo'   => 'cliente',
                    'emisor_nombre' => $empresa['razon_social'] ?: $empUser['nombre'],
                    'mensaje'       => trim($body['mensaje']),
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return $this->ok([
            'contacto_id' => $contactoId,
            'nombre'      => trim($candUser['nombre'] . ' ' . $candUser['apellido']),
            'oficio'      => !empty($vacanteTitulo) ? $vacanteTitulo : ($candidato['profesion'] ?: 'Candidato'),
            'avatar_url'  => !empty($candUser['avatar']) ? (str_starts_with($candUser['avatar'], 'http') ? $candUser['avatar'] : base_url('uploads/' . $candUser['avatar'])) : null,
            'telefono'    => $candUser['telefono'] ?? '',
        ]);
    }

    private function getEmpresa(): ?array
    {
        return (new EmpresaModel())->where('user_id', $this->authUser()['id'] ?? 0)->first();
    }
}
