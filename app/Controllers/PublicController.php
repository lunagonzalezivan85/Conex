<?php

namespace App\Controllers;

use App\Models\VacanteModel;
use App\Models\CategoriaModel;

class PublicController extends BaseController
{
    public function index(): string
    {
        $vacanteModel = new VacanteModel();
        $categoriaModel = new CategoriaModel();

        $vacantes = $vacanteModel->where('estado', 'publicada')
            ->orderBy('fecha_publicacion', 'DESC')
            ->limit(8)
            ->findAll();

        $categorias = $categoriaModel->where('estado', 'activo')->findAll();

        return view('layouts/publico', [
            'content' => view('public/landing', [
                'vacantes' => $vacantes,
                'categorias' => $categorias,
            ]),
            'css' => ['landing.css'],
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

        // Datos de empresa para las cards
        $builder->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left');
        $select = 'vac_vacante.*, emp_empresa.razon_social as empresa_nombre, emp_empresa.logo as empresa_logo';

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

        return view('layouts/publico', [
            'content' => view('public/buscar-empleo', [
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
            ->select('vac_vacante.*, emp_empresa.razon_social as empresa_nombre_db')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->where('slug', $slug)->first();

        if (!$vacante) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Metrica: contar vista del detalle
        $db = \Config\Database::connect();
        $db->table('vac_vacante')->where('id', $vacante['id'])->set('vistas', 'vistas+1', false)->update();
        $vacante['vistas'] = (int)$vacante['vistas'] + 1;
        $vacante['empresa_nombre'] = $vacante['empresa_nombre_db'] ?? $vacante['empresa_externa'];
        $vacante['poster_url'] = !empty($vacante['poster']) ? base_url('uploads/' . $vacante['poster']) : null;
        $vacante['mi_reaccion'] = $this->reaccionWeb($vacante['id']);

        return view('layouts/publico', [
            'content' => view('public/vacante-detalle', [
                'vacante' => $vacante,
            ]),
            'css' => ['vacante-detalle.css'],
            'js' => ['vacante-mapa.js'],
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
