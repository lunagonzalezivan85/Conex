<?php

namespace App\Controllers\Api;

use App\Models\CategoriaModel;
use App\Models\VacanteModel;

class VacanteController extends BaseApiController
{
    /**
     * GET /api/vacantes
     * Params: q, categoria, modalidad, ciudad, lat, lng, radio (km),
     *         orden (reciente|antigua|salario|distancia), page, per_page
     */
    public function index()
    {
        $vacanteModel = new VacanteModel();

        $q           = trim((string)$this->request->getGet('q'));
        $categoria   = $this->request->getGet('categoria');
        $modalidad   = $this->request->getGet('modalidad');
        $ciudad      = trim((string)$this->request->getGet('ciudad'));
        $orden       = $this->request->getGet('orden') ?: 'reciente';
        $lat         = $this->request->getGet('lat');
        $lng         = $this->request->getGet('lng');
        $radio       = (float)($this->request->getGet('radio') ?: 50);
        [$page, $perPage] = $this->paginationParams();

        $builder = $vacanteModel->where('vac_vacante.estado', 'publicada')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->join('cat_categoria', 'cat_categoria.id = vac_vacante.categoria_id', 'left');

        if ($q !== '') {
            $builder->groupStart()
                ->like('vac_vacante.titulo', $q)
                ->orLike('emp_empresa.razon_social', $q)
                ->groupEnd();
        }
        if (!empty($categoria)) {
            $builder->where('vac_vacante.categoria_id', (int)$categoria);
        }
        if (!empty($modalidad)) {
            $builder->where('vac_vacante.modalidad', $modalidad);
        }
        if ($ciudad !== '') {
            $builder->like('vac_vacante.ciudad', $ciudad);
        }

        $select = 'vac_vacante.*, emp_empresa.razon_social AS empresa_nombre, emp_empresa.logo AS empresa_logo, cat_categoria.nombre AS categoria_nombre';

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

        switch ($orden) {
            case 'antigua':
                $builder->orderBy('vac_vacante.fecha_publicacion', 'ASC');
                break;
            case 'salario':
                $builder->orderBy('vac_vacante.salario_max', 'DESC');
                break;
            case 'distancia':
                $builder->orderBy($geoActivo ? 'distancia_km' : 'vac_vacante.fecha_publicacion', 'ASC');
                break;
            default:
                $builder->orderBy('vac_vacante.fecha_publicacion', 'DESC');
        }

        $vacantes = $builder->paginate($perPage, 'default', $page);
        $pager = $vacanteModel->pager;

        // URLs absolutas para logos
        foreach ($vacantes as &$v) {
            $v['empresa_logo_url'] = !empty($v['empresa_logo'])
                ? base_url('uploads/' . $v['empresa_logo'])
                : null;
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
     * GET /api/vacantes/{slug}
     */
    public function show($slug)
    {
        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel
            ->select('vac_vacante.*, emp_empresa.razon_social AS empresa_nombre, emp_empresa.logo AS empresa_logo, emp_empresa.sitio_web AS empresa_sitio_web, emp_empresa.verificada AS empresa_verificada, cat_categoria.nombre AS categoria_nombre')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->join('cat_categoria', 'cat_categoria.id = vac_vacante.categoria_id', 'left')
            ->where('vac_vacante.slug', $slug)
            ->where('vac_vacante.estado', 'publicada')
            ->first();

        if (!$vacante) {
            return $this->fail('Vacante no encontrada', 404);
        }

        $vacante['empresa_logo_url'] = !empty($vacante['empresa_logo'])
            ? base_url('uploads/' . $vacante['empresa_logo'])
            : null;

        // Requisitos asociados
        $db = \Config\Database::connect();
        $vacante['requisitos'] = $db->table('vac_requisito')
            ->select('vac_requisito.tipo, cat_requisito.nombre')
            ->join('cat_requisito', 'cat_requisito.id = vac_requisito.requisito_id', 'left')
            ->where('vac_requisito.vacante_id', $vacante['id'])
            ->get()->getResultArray();

        $vacante['habilidades'] = $db->table('vac_habilidad')
            ->select('cat_habilidad.nombre')
            ->join('cat_habilidad', 'cat_habilidad.id = vac_habilidad.habilidad_id', 'left')
            ->where('vac_habilidad.vacante_id', $vacante['id'])
            ->get()->getResultArray();

        return $this->ok(['data' => $vacante]);
    }

    /**
     * GET /api/vacantes/meta/filtros — categorias + modalidades para el app.
     */
    public function filtros()
    {
        $categorias = (new CategoriaModel())->where('estado', 'activo')->findAll();
        return $this->ok([
            'categorias'  => $categorias,
            'modalidades' => ['presencial', 'remoto', 'hibrido'],
            'ordenes'     => ['reciente', 'antigua', 'salario', 'distancia'],
        ]);
    }
}
