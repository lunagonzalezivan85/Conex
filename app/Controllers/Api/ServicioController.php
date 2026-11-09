<?php

namespace App\Controllers\Api;

class ServicioController extends BaseApiController
{
    /**
     * GET /api/servicios
     * Directorio publico de prestadores: personas que ofrecen
     * servicios activos, con su oficio, zona y precio.
     * Params: q (busca en oficio), page, per_page (max 30).
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $q    = trim((string)($this->request->getGet('q') ?? ''));
        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $per  = min(30, max(1, (int)($this->request->getGet('per_page') ?? 15)));

        $builder = $db->table('srv_servicio')
            ->select('srv_servicio.id, srv_servicio.oficio, srv_servicio.descripcion,
                srv_servicio.precio_desde, srv_servicio.precio_tipo,
                srv_servicio.ciudad, srv_servicio.zona, srv_servicio.contactos, srv_servicio.destacado,
                auth_user.nombre, auth_user.avatar, cand_candidato.apellidos,
                cand_candidato.id AS candidato_id,
                cand_candidato.prestador_verificado,
                cand_candidato.disponible_ahora, cand_candidato.disponible_desde,
                cand_candidato.latitud, cand_candidato.longitud,
                cat_nivel_experiencia.nombre AS nivel_experiencia_nombre,
                cat_nivel_experiencia.anios_minimos')
            ->join('cand_candidato', 'cand_candidato.id = srv_servicio.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->join('cat_nivel_experiencia', 'cat_nivel_experiencia.id = cand_candidato.nivel_experiencia_id', 'left')
            ->where('srv_servicio.estado', 'activo')
            ->where('srv_servicio.deleted_at IS NULL')
            ->where('cand_candidato.deleted_at IS NULL')
            ->where('cand_candidato.ofrece_servicios', 1)
            // Mismo filtro antifraaude del sitio publico
            ->where('(SELECT COUNT(*) FROM srv_reporte r WHERE r.servicio_id = srv_servicio.id) <', 3);

        if ($q !== '') {
            $builder->like('srv_servicio.oficio', $q);
        }

        $total = $builder->countAllResults(false);
        $items = $builder
            ->orderBy('srv_servicio.destacado', 'DESC')
            ->orderBy('cand_candidato.disponible_ahora', 'DESC')
            ->orderBy('srv_servicio.contactos', 'DESC')
            ->orderBy('srv_servicio.id', 'DESC')
            ->limit($per, ($page - 1) * $per)
            ->get()->getResultArray();

        // Calcular anios de experiencia reales si tiene registros en cand_experiencia
        $candIds = array_unique(array_filter(array_column($items, 'candidato_id')));
        $expPorCand = [];
        if (!empty($candIds)) {
            $exps = $db->table('cand_experiencia')
                ->select('candidato_id, fecha_inicio, fecha_fin')
                ->whereIn('candidato_id', $candIds)
                ->get()->getResultArray();
            foreach ($exps as $e) {
                if (!empty($e['fecha_inicio'])) {
                    $ini = strtotime($e['fecha_inicio']);
                    $fin = !empty($e['fecha_fin']) ? strtotime($e['fecha_fin']) : time();
                    if ($fin > $ini) {
                        $cid = (int)$e['candidato_id'];
                        $expPorCand[$cid] = ($expPorCand[$cid] ?? 0) + (($fin - $ini) / (365 * 24 * 3600));
                    }
                }
            }
        }

        foreach ($items as &$s) {
            // "Ivan G." como en la web publica de prestadores
            $s['nombre_publico'] = trim($s['nombre'])
                . ($s['apellidos'] ? ' ' . strtoupper(substr($s['apellidos'], 0, 1)) . '.' : '');
            $s['avatar_url'] = !empty($s['avatar']) ? base_url('uploads/' . $s['avatar']) : null;

            // Pin en el mapa solo si marco 'disponible' y es reciente (<4h)
            $fresh = !empty($s['disponible_ahora'])
                && !empty($s['disponible_desde'])
                && strtotime($s['disponible_desde']) >= time() - 4 * 3600;
            $s['disponible'] = $fresh ? 1 : 0;
            $s['lat'] = $fresh && $s['latitud'] !== null ? (float) $s['latitud'] : null;
            $s['lng'] = $fresh && $s['longitud'] !== null ? (float) $s['longitud'] : null;
            $s['verificado'] = (int)($s['prestador_verificado'] ?? 0);

            $cid = (int)($s['candidato_id'] ?? 0);
            $aniosCalc = isset($expPorCand[$cid]) ? (int)max(1, round($expPorCand[$cid])) : (int)($s['anios_minimos'] ?? 0);
            $s['anios_experiencia'] = $aniosCalc;
            if ($aniosCalc > 0) {
                $s['experiencia_label'] = $aniosCalc . ($aniosCalc === 1 ? ' año de experiencia' : ' años de experiencia');
            } elseif (!empty($s['nivel_experiencia_nombre'])) {
                $s['experiencia_label'] = $s['nivel_experiencia_nombre'];
            } else {
                $s['experiencia_label'] = 'Experiencia comprobada';
            }

            unset($s['apellidos'], $s['avatar'], $s['disponible_ahora'],
                $s['disponible_desde'], $s['latitud'], $s['longitud'],
                $s['candidato_id'], $s['prestador_verificado'], $s['anios_minimos']);
        }

        return $this->ok([
            'data' => $items,
            'meta' => [
                'total'        => $total,
                'current_page' => $page,
                'per_page'     => $per,
                'last_page'    => max(1, (int)ceil($total / $per)),
            ],
        ]);
    }

    /**
     * POST /api/servicios/:id/contacto
     * Solicitud de contacto al prestador (misma logica que la web):
     * el telefono del cliente solo se revela si el prestador acepta.
     * Body: { nombre, telefono, mensaje? }
     */
    public function contactar($id = null)
    {
        if ($res = $this->validateApi([
            'nombre'   => 'required|min_length[2]|max_length[120]',
            'telefono' => 'required|min_length[7]|max_length[30]',
            'mensaje'  => 'permit_empty|max_length[500]',
        ])) {
            return $res;
        }

        $data = $this->body();
        if (!preg_match('/^[0-9+\-\s()]{7,30}$/', $data['telefono'])) {
            return $this->fail('Telefono invalido', 422);
        }

        $db = \Config\Database::connect();
        $servicio = $db->table('srv_servicio')
            ->where('id', (int)$id)->where('estado', 'activo')
            ->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$servicio) {
            return $this->fail('Servicio no disponible', 404);
        }

        $ipHash = hash('sha256', $this->request->getIPAddress() . config('App')->baseURL);

        // Anti-scraping: max 10 solicitudes por dia por IP
        $hoy = $db->table('srv_contacto')
            ->where('ip_hash', $ipHash)
            ->where('created_at >=', date('Y-m-d 00:00:00'))
            ->countAllResults();
        if ($hoy >= 10) {
            return $this->fail('Limite de solicitudes alcanzado por hoy. Intenta manana.', 429);
        }

        // Anti-spam: no duplicar pendiente sobre el mismo servicio
        $pendiente = $db->table('srv_contacto')
            ->where('servicio_id', (int)$id)
            ->where('ip_hash', $ipHash)
            ->where('estado', 'pendiente')
            ->countAllResults();
        if ($pendiente > 0) {
            return $this->fail('Ya tienes una solicitud pendiente con este prestador.', 409);
        }

        $db->table('srv_contacto')->insert([
            'servicio_id' => (int)$id,
            'user_id'     => $this->authUser()['id'] ?? null,
            'nombre'      => trim($data['nombre']),
            'telefono'    => trim($data['telefono']),
            'mensaje'     => trim($data['mensaje'] ?? ''),
            'ip_hash'     => $ipHash,
            'estado'      => 'pendiente',
        ]);
        $db->table('srv_servicio')
            ->where('id', (int)$id)
            ->set('contactos', 'contactos+1', false)
            ->update();

        return $this->ok([
            'message' => 'Solicitud enviada. El prestador vera tus datos si la acepta.',
        ], 201);
    }
}
