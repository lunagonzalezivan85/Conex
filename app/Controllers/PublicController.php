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

        $vacantes = $builder->orderBy('vac_vacante.fecha_publicacion', 'DESC')->paginate(12);
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
                ],
            ]),
            'css' => ['buscar-empleo.css'],
        ]);
    }

    public function verVacante($slug): string
    {
        $vacanteModel = new VacanteModel();
        $vacante = $vacanteModel->where('slug', $slug)->first();

        if (!$vacante) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('layouts/publico', [
            'content' => view('public/vacante-detalle', [
                'vacante' => $vacante,
            ]),
            'css' => ['vacante-detalle.css'],
        ]);
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
