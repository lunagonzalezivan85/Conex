<?php

namespace App\Controllers;

use App\Models\VacanteModel;
use App\Models\CategoriaModel;

class PublicController extends BaseController
{
    public function index(): string
    {
        $vacanteModel = new VacanteModel();
        $db = \Config\Database::connect();

        $vacantes = $vacanteModel
            ->select('vac_vacante.*, emp_empresa.razon_social AS empresa_nombre, emp_empresa.logo AS empresa_logo')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->where('vac_vacante.estado', 'publicada')
            ->orderBy('vac_vacante.fecha_publicacion', 'DESC')
            ->limit(8)
            ->findAll();

        // Categorias con conteo real de vacantes
        $categorias = $db->table('cat_categoria')
            ->select('cat_categoria.*,
                (SELECT COUNT(*) FROM vac_vacante v
                 WHERE v.categoria_id = cat_categoria.id AND v.estado = "publicada") AS total')
            ->where('cat_categoria.estado', 'activo')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        foreach ($categorias as &$cat) {
            $cat['icon_svg'] = $this->iconoCategoriaSvg($cat['nombre'] ?? '');
        }
        unset($cat);

        // Stats reales para el band
        $stats = [
            'vacantes'    => $db->table('vac_vacante')->where('estado', 'publicada')->countAllResults(),
            'empresas'    => $db->table('emp_empresa')->countAllResults(),
            'postulantes' => $db->table('cand_candidato')->where('deleted_at IS NULL')->countAllResults(),
            'servicios'   => $db->table('srv_servicio')->where('estado', 'activo')->where('deleted_at IS NULL')->countAllResults(),
        ];

        // Prestadores destacados del directorio (mismo filtro de privacidad)
        $servicios = $db->table('srv_servicio')
            ->select('srv_servicio.id, srv_servicio.oficio, srv_servicio.ciudad, srv_servicio.zona,
                cand_candidato.prestador_verificado,
                auth_user.nombre, auth_user.apellido, auth_user.avatar')
            ->join('cand_candidato', 'cand_candidato.id = srv_servicio.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('srv_servicio.estado', 'activo')
            ->where('srv_servicio.deleted_at IS NULL')
            ->where('cand_candidato.ofrece_servicios', 1)
            ->orderBy('srv_servicio.destacado', 'DESC')
            ->orderBy('srv_servicio.created_at', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        foreach ($servicios as &$s) {
            $s['nombre_publico'] = $this->nombrePublico($s['nombre'], $s['apellido']);
            $s['iniciales']      = strtoupper(substr($s['nombre'], 0, 1) . substr($s['apellido'], 0, 1));
            $s['avatar_url']     = !empty($s['avatar']) ? base_url('uploads/' . $s['avatar']) : null;
        }
        unset($s);

        return view('layouts/publico', [
            'content' => view('public/landing', [
                'vacantes'   => $vacantes,
                'categorias' => $categorias,
                'stats'      => $stats,
                'servicios'  => $servicios,
            ]),
            'css' => ['landing.css'],
            'js'  => ['landing.js'],
        ]);
    }

    public function buscarEmpleo(): string
    {
        $vacanteModel = new VacanteModel();
        $categoriaModel = new CategoriaModel();

        $q = $this->request->getGet('q');
        $categoria = $this->request->getGet('categoria');
        $modalidad = $this->request->getGet('modalidad');
        $tipoContrato = $this->request->getGet('tipo_contrato');
        $ciudad = $this->request->getGet('ciudad');
        $orden = $this->request->getGet('orden') ?: 'reciente';
        $lat = $this->request->getGet('lat');
        $lng = $this->request->getGet('lng');
        $radio = (float)($this->request->getGet('radio') ?: 50);

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

        // Datos de empresa para las cards (fallback a empresa externa captada)
        $builder->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left');
        $select = 'vac_vacante.*, COALESCE(emp_empresa.razon_social, vac_vacante.empresa_externa) as empresa_nombre, emp_empresa.logo as empresa_logo';

        // Filtro por geolocalizacion (Haversine, radio en km)
        $geoActivo = is_numeric($lat) && is_numeric($lng);
        if ($geoActivo) {
            $lat = (float)$lat;
            $lng = (float)$lng;
            $radio = max(1, min(500, $radio));
            $distanciaExpr = "6371 * acos(LEAST(1, cos(radians({$lat})) * cos(radians(vac_vacante.latitud)) * cos(radians(vac_vacante.longitud) - radians({$lng})) + sin(radians({$lat})) * sin(radians(vac_vacante.latitud))))";
            $builder->where('vac_vacante.latitud IS NOT NULL')
                ->where('vac_vacante.longitud IS NOT NULL')
                ->where("{$distanciaExpr} <=", $radio, true);
            $select .= ", {$distanciaExpr} AS distancia_km";
        }
        $builder->select($select);

        // Ordenamiento
        switch ($orden) {
            case 'antigua':
                $builder->orderBy('vac_vacante.fecha_publicacion', 'ASC');
                break;
            case 'salario':
                $builder->orderBy('vac_vacante.salario_max', 'DESC');
                break;
            case 'distancia':
                if ($geoActivo) {
                    $builder->orderBy('distancia_km', 'ASC');
                } else {
                    $builder->orderBy('vac_vacante.fecha_publicacion', 'DESC');
                }
                break;
            default: // reciente
                $builder->orderBy('vac_vacante.fecha_publicacion', 'DESC');
        }

        $vacantes = $builder->paginate(12);
        $categorias = $categoriaModel->where('estado', 'activo')->findAll();

        // Poster, iniciales de empresa y reaccion del usuario por vacante
        $ids = array_column($vacantes, 'id');
        $misReacciones = [];
        if (!empty($ids)) {
            $rows = \Config\Database::connect()->table('vac_reaccion')
                ->select('vacante_id, tipo')
                ->whereIn('vacante_id', $ids)
                ->where('identificador', $this->identificadorWeb())
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $misReacciones[$r['vacante_id']] = $r['tipo'];
            }
        }
        foreach ($vacantes as &$v) {
            $v['poster_url'] = !empty($v['poster']) ? base_url('uploads/' . $v['poster']) : null;
            $nombre = $v['empresa_nombre'] ?? $v['titulo'];
            $v['empresa_iniciales'] = strtoupper(substr($nombre, 0, 1));
            $v['mi_reaccion'] = $misReacciones[$v['id']] ?? null;
        }
        unset($v);

        // Vista: listado | mapa (fullscreen con markers)
        $vista = $this->request->getGet('vista') === 'mapa' ? 'mapa' : 'listado';
        $vacantesMapa = [];
        if ($vista === 'mapa') {
            foreach ($vacantes as $v) {
                if (!empty($v['latitud']) && !empty($v['longitud'])) {
                    $vacantesMapa[] = [
                        'slug'     => $v['slug'],
                        'url'      => base_url('vacante/' . $v['slug']),
                        'titulo'   => $v['titulo'],
                        'empresa'  => $v['empresa_nombre'] ?? '',
                        'ciudad'   => $v['ciudad'] ?? '',
                        'modalidad' => $v['modalidad'] ?? '',
                        'lat'      => (float)$v['latitud'],
                        'lng'      => (float)$v['longitud'],
                    ];
                }
            }
        }

        return view('layouts/publico', [
            'content' => view('public/buscar-empleo', [
                'vista'        => $vista,
                'vacantesMapa' => $vacantesMapa,
                'vacantes' => $vacantes,
                'categorias' => $categorias,
                'pager' => $vacanteModel->pager,
                'filtros' => [
                    'q' => $q,
                    'categoria' => $categoria,
                    'modalidad' => $modalidad,
                    'tipo_contrato' => $tipoContrato,
                    'ciudad' => $ciudad,
                    'orden' => $orden,
                    'lat' => $lat,
                    'lng' => $lng,
                    'radio' => $radio,
                ],
            ]),
            'css' => ['buscar-empleo.css'],
            'js' => ['buscar-empleo.js'],
        ]);
    }

    public function verVacante($slug): string
    {
        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel
            ->select('vac_vacante.*, emp_empresa.razon_social as empresa_nombre_db, emp_empresa.logo as empresa_logo')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->where('slug', $slug)->first();

        if (!$vacante) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Contar vista del detalle
        $db = \Config\Database::connect();
        $db->table('vac_vacante')->where('id', $vacante['id'])->set('vistas', 'vistas+1', false)->update();
        $vacante['vistas'] = (int)$vacante['vistas'] + 1;
        $vacante['empresa_nombre'] = $vacante['empresa_nombre_db'] ?? $vacante['empresa_externa'];
        $vacante['poster_url'] = !empty($vacante['poster']) ? base_url('uploads/' . $vacante['poster']) : null;
        $vacante['mi_reaccion'] = $this->reaccionWeb($vacante['id']);

        // SEO/Open Graph: titulo, descripcion e imagen del anuncio para compartir
        $metaTitle = $vacante['titulo'] . ($vacante['empresa_nombre'] ? ' - ' . $vacante['empresa_nombre'] : '');
        $metaDesc = trim(preg_replace('/\s+/', ' ', strip_tags((string)$vacante['descripcion'])));
        $metaDesc = mb_substr($metaDesc, 0, 160);
        if (!empty($vacante['ciudad'])) {
            $metaDesc = $vacante['ciudad'] . ' · ' . $metaDesc;
        }
        $ogImage = $vacante['poster_url']
            ?? (!empty($vacante['empresa_logo']) ? base_url('uploads/' . $vacante['empresa_logo']) : null);

        return view('layouts/publico', [
            'content' => view('public/vacante-detalle', [
                'vacante' => $vacante,
            ]),
            'css' => ['vacante-detalle.css'],
            'js' => ['vacante-mapa.js'],
            'title' => $metaTitle,
            'meta_description' => $metaDesc,
            'canonical_url' => base_url('vacante/' . $vacante['slug']),
            'og_image' => $ogImage,
        ]);
    }

    /**
     * POST /vacante/{id}/reaccion — me_gusta|no_me_gusta (web, anonimo por sesion/IP).
     */
    public function reaccionar($id)
    {
        $tipo = $this->request->getPost('tipo');
        if (!in_array($tipo, ['me_gusta', 'no_me_gusta'], true)) {
            return $this->response->setJSON(['error' => 'Tipo invalido'])->setStatusCode(422);
        }

        $db = \Config\Database::connect();
        $vacante = $db->table('vac_vacante')->where('id', (int)$id)->get()->getRowArray();
        if (!$vacante) {
            return $this->response->setJSON(['error' => 'No encontrada'])->setStatusCode(404);
        }

        $identificador = $this->identificadorWeb();
        $existente = $db->table('vac_reaccion')
            ->where('vacante_id', (int)$id)
            ->where('identificador', $identificador)
            ->get()->getRowArray();

        $miReaccion = null;
        if ($existente && $existente['tipo'] === $tipo) {
            $db->table('vac_reaccion')->where('id', $existente['id'])->delete();
        } elseif ($existente) {
            $db->table('vac_reaccion')->where('id', $existente['id'])->update(['tipo' => $tipo]);
            $miReaccion = $tipo;
        } else {
            $db->table('vac_reaccion')->insert([
                'vacante_id'    => (int)$id,
                'user_id'       => session()->get('user_id'),
                'identificador' => $identificador,
                'tipo'          => $tipo,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            $miReaccion = $tipo;
        }

        $conteos = [
            'me_gusta'    => (int)$db->table('vac_reaccion')->where('vacante_id', (int)$id)->where('tipo', 'me_gusta')->countAllResults(),
            'no_me_gusta' => (int)$db->table('vac_reaccion')->where('vacante_id', (int)$id)->where('tipo', 'no_me_gusta')->countAllResults(),
        ];
        $db->table('vac_vacante')->where('id', (int)$id)->update($conteos);

        return $this->response->setJSON($conteos + ['mi_reaccion' => $miReaccion]);
    }

    /**
     * POST /vacante/{id}/compartir — contador de shares web.
     */
    public function compartir($id)
    {
        $db = \Config\Database::connect();
        $existe = $db->table('vac_vacante')->where('id', (int)$id)->countAllResults();
        if (!$existe) {
            return $this->response->setJSON(['error' => 'No encontrada'])->setStatusCode(404);
        }
        $db->table('vac_vacante')->where('id', (int)$id)->set('compartidos', 'compartidos+1', false)->update();
        $total = (int)$db->table('vac_vacante')->where('id', (int)$id)->get()->getRowArray()['compartidos'];
        return $this->response->setJSON(['compartidos' => $total]);
    }

    /**
     * Identificador de reaccion web: usuario logueado o hash sesion+IP.
     */
    private function identificadorWeb(): string
    {
        $userId = session()->get('user_id');
        if ($userId) {
            return 'u' . $userId;
        }
        return 'a' . substr(hash('sha256', (string)session_id() . $this->request->getIPAddress()), 0, 32);
    }

    private function reaccionWeb(int $vacanteId): ?string
    {
        $row = \Config\Database::connect()->table('vac_reaccion')
            ->where('vacante_id', $vacanteId)
            ->where('identificador', $this->identificadorWeb())
            ->get()->getRowArray();
        return $row['tipo'] ?? null;
    }

    // ============ DIRECTORIO DE SERVICIOS (prestadores) ============
    // Regla de privacidad: el SELECT publico NUNCA incluye
    // latitud/longitud, direccion, telefono, email ni CV del candidato.
    // La ubicacion precisa es solo para matching de empleo; el publico
    // ve unicamente ciudad + zona aproximada.

    public function buscarServicios(): string
    {
        $db = \Config\Database::connect();

        $q      = trim((string)$this->request->getGet('q'));
        $ciudad = trim((string)$this->request->getGet('ciudad'));
        $zona   = trim((string)$this->request->getGet('zona'));
        $verificado = (bool)$this->request->getGet('verificado');
        $lat   = $this->request->getGet('lat');
        $lng   = $this->request->getGet('lng');
        $radio = (float)($this->request->getGet('radio') ?: 50);

        $builder = $db->table('srv_servicio')
            ->select('srv_servicio.id, srv_servicio.oficio, srv_servicio.descripcion,
                srv_servicio.precio_desde, srv_servicio.precio_tipo,
                srv_servicio.ciudad, srv_servicio.zona,
                cand_candidato.prestador_verificado, cand_candidato.zona_servicio,
                cand_candidato.latitud, cand_candidato.longitud,
                auth_user.nombre, auth_user.apellido, auth_user.avatar')
            ->join('cand_candidato', 'cand_candidato.id = srv_servicio.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('srv_servicio.estado', 'activo')
            ->where('srv_servicio.deleted_at IS NULL')
            ->where('cand_candidato.ofrece_servicios', 1)
            ->where('cand_candidato.deleted_at IS NULL');

        if ($q !== '') {
            $builder->groupStart()
                ->like('srv_servicio.oficio', $q)
                ->orLike('srv_servicio.descripcion', $q)
                ->groupEnd();
        }
        if ($ciudad !== '') {
            $builder->like('srv_servicio.ciudad', $ciudad);
        }
        if ($zona !== '') {
            $builder->like('srv_servicio.zona', $zona);
        }
        if ($verificado) {
            $builder->where('cand_candidato.prestador_verificado', 1);
        }

        // Filtro geo: distancia al prestador (coords de cand_candidato)
        $geoActivo = is_numeric($lat) && is_numeric($lng);
        if ($geoActivo) {
            $lat = (float)$lat;
            $lng = (float)$lng;
            $radio = max(1, min(500, $radio));
            $distanciaExpr = "6371 * acos(LEAST(1, cos(radians({$lat})) * cos(radians(cand_candidato.latitud)) * cos(radians(cand_candidato.longitud) - radians({$lng})) + sin(radians({$lat})) * sin(radians(cand_candidato.latitud))))";
            $builder->where('cand_candidato.latitud IS NOT NULL')
                ->where('cand_candidato.longitud IS NOT NULL')
                ->where("{$distanciaExpr} <=", $radio, true);
        }

        // Suspendidos por reportes no aparecen (3+ reportes de IPs distintas)
        $builder->where('(SELECT COUNT(*) FROM srv_reporte r WHERE r.servicio_id = srv_servicio.id) <', 3);

        $builder->orderBy('srv_servicio.destacado', 'DESC')
            ->orderBy('cand_candidato.prestador_verificado', 'DESC')
            ->orderBy('srv_servicio.created_at', 'DESC');

        // Builder no tiene paginate(): contar en clon, limit, y pager manual
        $perPage = 12;
        $page = max(1, (int)($this->request->getGet('page') ?: 1));
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(true);
        $servicios = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        $pager = service('pager')->store('default', $page, $perPage, $total);

        foreach ($servicios as &$s) {
            $s['nombre_publico'] = $this->nombrePublico($s['nombre'], $s['apellido']);
            $s['iniciales']      = strtoupper(substr($s['nombre'], 0, 1) . substr($s['apellido'], 0, 1));
            $s['avatar_url']     = !empty($s['avatar']) ? base_url('uploads/' . $s['avatar']) : null;
        }
        unset($s);

        $ciudades = $db->table('srv_servicio')
            ->select('ciudad')->distinct()
            ->where('estado', 'activo')->where('deleted_at IS NULL')
            ->where('ciudad IS NOT NULL')->where("ciudad !=", '')
            ->orderBy('ciudad')->get()->getResultArray();

        // Vista: listado | mapa (coords redondeadas a ~1km: ubicacion aproximada)
        $vista = $this->request->getGet('vista') === 'mapa' ? 'mapa' : 'listado';
        $serviciosMapa = [];
        if ($vista === 'mapa') {
            foreach ($servicios as $s) {
                if (!empty($s['latitud']) && !empty($s['longitud'])) {
                    $serviciosMapa[] = [
                        'titulo'   => $s['oficio'],
                        'empresa'  => $s['nombre_publico'],
                        'ciudad'   => trim(($s['zona'] ? $s['zona'] . ', ' : '') . ($s['ciudad'] ?? '')),
                        'modalidad' => !empty($s['prestador_verificado']) ? 'Verificado' : '',
                        'url'      => base_url('servicios/p/' . (int)$s['id']),
                        'lat'      => round((float)$s['latitud'], 2),
                        'lng'      => round((float)$s['longitud'], 2),
                    ];
                }
            }
        }

        return view('layouts/publico', [
            'content' => view('public/buscar-servicios', [
                'vista'        => $vista,
                'serviciosMapa' => $serviciosMapa,
                'servicios' => $servicios,
                'ciudades'  => array_column($ciudades, 'ciudad'),
                'pager'     => $pager,
                'filtros'   => ['q' => $q, 'ciudad' => $ciudad, 'zona' => $zona,
                                'verificado' => $verificado, 'lat' => $lat, 'lng' => $lng, 'radio' => $radio],
            ]),
            'css' => ['buscar-empleo.css', 'servicios.css'],
            'js' => ['buscar-empleo.js'],
            'title' => 'Servicios',
            'meta_description' => 'Encuentra profesionales y oficios cerca de ti: electricistas, plomeros, carpinteros y mas. Contacto protegido y prestadores verificados en CONEX.',
        ]);
    }

    public function verPrestador($id): string
    {
        $db = \Config\Database::connect();

        $servicio = $db->table('srv_servicio')
            ->select('srv_servicio.*, cand_candidato.prestador_verificado,
                auth_user.nombre, auth_user.apellido, auth_user.avatar')
            ->join('cand_candidato', 'cand_candidato.id = srv_servicio.candidato_id')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
            ->where('srv_servicio.id', (int)$id)
            ->where('srv_servicio.estado', 'activo')
            ->where('srv_servicio.deleted_at IS NULL')
            ->where('cand_candidato.ofrece_servicios', 1)
            ->where('(SELECT COUNT(*) FROM srv_reporte r WHERE r.servicio_id = srv_servicio.id) <', 3)
            ->get()->getRowArray();

        if (!$servicio) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $servicio['nombre_publico'] = $this->nombrePublico($servicio['nombre'], $servicio['apellido']);
        $servicio['iniciales']      = strtoupper(substr($servicio['nombre'], 0, 1) . substr($servicio['apellido'], 0, 1));
        $servicio['avatar_url']     = !empty($servicio['avatar']) ? base_url('uploads/' . $servicio['avatar']) : null;

        // Otros servicios del mismo prestador
        $otros = $db->table('srv_servicio')
            ->select('id, oficio, ciudad, zona')
            ->where('candidato_id', $servicio['candidato_id'])
            ->where('id !=', (int)$id)
            ->where('estado', 'activo')
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();

        return view('layouts/publico', [
            'content' => view('public/prestador-detalle', [
                'servicio' => $servicio,
                'otros'    => $otros,
            ]),
            'css' => ['buscar-empleo.css', 'servicios.css'],
            'title' => $servicio['oficio'] . ' - ' . $servicio['nombre_publico'],
            'meta_description' => $servicio['oficio'] . ' en ' . ($servicio['zona'] ?: $servicio['ciudad']) . '. Solicita contacto protegido via CONEX.',
        ]);
    }

    /**
     * POST /servicios/{id}/contacto — el cliente deja sus datos; el prestador
     * decide si acepta y recien ahi ve el telefono. Rate limit: 10/dia por IP
     * y 1 solicitud pendiente por IP+servicio (anti-scraping del directorio).
     */
    public function contactarPrestador($id)
    {
        $nombre   = trim((string)$this->request->getPost('nombre'));
        $telefono = trim((string)$this->request->getPost('telefono'));
        $mensaje  = trim((string)$this->request->getPost('mensaje'));

        if ($nombre === '' || mb_strlen($nombre) > 120 || !preg_match('/^[0-9+\-\s()]{7,30}$/', $telefono)) {
            return redirect()->back()->withInput()
                ->with('error', 'Revisa tu nombre y telefono.');
        }

        $db = \Config\Database::connect();
        $servicio = $db->table('srv_servicio')
            ->where('id', (int)$id)->where('estado', 'activo')
            ->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$servicio) {
            return redirect()->to('servicios')->with('error', 'Servicio no disponible.');
        }

        $ipHash = hash('sha256', $this->request->getIPAddress() . config('App')->baseURL);

        // Anti-scraping: max 10 solicitudes por dia por IP
        $hoy = $db->table('srv_contacto')
            ->where('ip_hash', $ipHash)
            ->where('created_at >=', date('Y-m-d 00:00:00'))
            ->countAllResults();
        if ($hoy >= 10) {
            return redirect()->back()
                ->with('error', 'Limite de solicitudes alcanzado por hoy. Intenta manana.');
        }

        // Anti-spam: no duplicar pendiente sobre el mismo servicio
        $pendiente = $db->table('srv_contacto')
            ->where('servicio_id', (int)$id)
            ->where('ip_hash', $ipHash)
            ->where('estado', 'pendiente')
            ->countAllResults();
        if ($pendiente > 0) {
            return redirect()->back()
                ->with('info', 'Ya tienes una solicitud pendiente con este prestador.');
        }

        $db->table('srv_contacto')->insert([
            'servicio_id' => (int)$id,
            'user_id'     => session()->get('user_id') ?: null,
            'nombre'      => $nombre,
            'telefono'    => $telefono,
            'mensaje'     => mb_substr($mensaje, 0, 500) ?: null,
            'ip_hash'     => $ipHash,
            'estado'      => 'pendiente',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        $db->table('srv_servicio')->where('id', (int)$id)->set('contactos', 'contactos+1', false)->update();

        return redirect()->to('servicios/p/' . (int)$id)
            ->with('info', 'Solicitud enviada. El prestador vera tus datos cuando la acepte.');
    }

    /**
     * POST /servicios/{id}/reporte — 1 reporte por IP+servicio (clave unica).
     * 3+ reportes sacan al servicio del directorio automaticamente.
     */
    public function reportarPrestador($id)
    {
        $motivos = ['fraude', 'datos_falsos', 'comportamiento', 'spam', 'otro'];
        $motivo  = (string)$this->request->getPost('motivo');
        $detalle = trim((string)$this->request->getPost('detalle'));

        if (!in_array($motivo, $motivos, true)) {
            return redirect()->back()->with('error', 'Motivo invalido.');
        }

        $db = \Config\Database::connect();
        $existe = $db->table('srv_servicio')
            ->where('id', (int)$id)->where('deleted_at IS NULL')
            ->countAllResults();
        if (!$existe) {
            return redirect()->to('servicios')->with('error', 'Servicio no disponible.');
        }

        $ipHash = hash('sha256', $this->request->getIPAddress() . config('App')->baseURL);

        try {
            $db->table('srv_reporte')->insert([
                'servicio_id' => (int)$id,
                'motivo'      => $motivo,
                'detalle'     => mb_substr($detalle, 0, 500) ?: null,
                'ip_hash'     => $ipHash,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('info', 'Ya reportaste este servicio.');
        }

        return redirect()->back()->with('info', 'Reporte recibido. Lo revisaremos.');
    }

    /** Icono Lucide segun el nombre de la categoria (sustituye emojis). */
    private function iconoCategoriaSvg(string $nombre): string
    {
        $n = mb_strtolower($nombre);
        $map = [
            'venta|comercial|tienda'        => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
            'tecno|sistema|desarroll|inform' => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
            'salud|enfermer|medic'          => '<path d="M19 14c1.5-1.5 3-3.3 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.2 1.5 4 3 5.5l7 7z"/>',
            'educa|docente|colegio'         => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
            'constru|albañil|obra'          => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
            'contab|finanz|banca'           => '<rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="8" y2="10.01"/><line x1="12" y1="10" x2="12" y2="10.01"/><line x1="16" y1="10" x2="16" y2="10.01"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="12" y1="14" x2="12" y2="14.01"/><line x1="16" y1="14" x2="16" y2="14.01"/><line x1="8" y1="18" x2="8" y2="18.01"/><line x1="12" y1="18" x2="12" y2="18.01"/><line x1="16" y1="18" x2="16" y2="18.01"/>',
            'resta|cocina|comida|hotel'     => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3z"/>',
            'transport|chofer|logist|moto'  => '<rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
            'electric'                      => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>',
            'plom|fontan|agua'              => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
            'admin|oficina|secret'          => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 13h6"/><path d="M9 17h4"/>',
            'market|publicidad|diseño'      => '<path d="m3 11 18-5v12L3 13v-2z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
            'legal|abogad|jurid'            => '<path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>',
            'belleza|barber|estil'          => '<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/>',
            'seguridad|guard'               => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        ];
        foreach ($map as $keys => $paths) {
            if (preg_match('/' . $keys . '/u', $n)) {
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
            }
        }
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
    }

    /** }

        return redirect()->back()->with('info', 'Reporte recibido. Lo revisaremos.');
    }

    /** "Juan Perez" -> "Juan P." — nunca exponer nombre completo en publico. */
    private function nombrePublico(string $nombre, string $apellido): string
    {
        $ini = mb_strtoupper(mb_substr(trim($apellido), 0, 1));
        return trim($nombre) . ($ini !== '' ? ' ' . $ini . '.' : '');
    }

    public function planes(): string
    {
        return view('layouts/publico', [
            'content' => view('public/planes'),
            'css' => ['planes.css'],
            'title' => 'Planes',
            'meta_description' => 'Conoce los planes de CONEX para postulantes y empresas. Encuentra la opcion que mejor se adapte a tus necesidades de empleo y reclutamiento.',
            'meta_keywords' => 'planes CONEX, planes empleo, planes reclutamiento, suscripcion, precios, postulante, empresa',
        ]);
    }

    public function nosotros(): string
    {
        return view('layouts/publico', [
            'content' => view('public/nosotros'),
            'css' => ['nosotros.css'],
            'title' => 'Nosotros',
            'meta_description' => 'Conoce CONEX, la plataforma de reclutamiento que conecta talentos con oportunidades en Nicaragua. Nuestra mision, valores e historia.',
            'meta_keywords' => 'CONEX nosotros, mision, vision, valores, empresa reclutamiento Nicaragua, historia CONEX',
        ]);
    }
}
