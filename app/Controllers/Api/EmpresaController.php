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

        $rules = [
            'razon_social' => 'permit_empty|min_length[3]|max_length[150]',
            'ruc'          => 'permit_empty|max_length[50]',
            'rubro'        => 'permit_empty|max_length[100]',
            'descripcion'  => 'permit_empty|max_length[2000]',
            'sitio_web'    => 'permit_empty|valid_url_strict|max_length[255]',
            'telefono'     => 'permit_empty|min_length[8]|max_length[20]',
            'direccion'    => 'permit_empty|max_length[255]',
            'ciudad'       => 'permit_empty|max_length[100]',
            'region'       => 'permit_empty|max_length[100]',
            'pais'         => 'permit_empty|max_length[100]',
            'latitud'      => 'permit_empty|numeric',
            'longitud'     => 'permit_empty|numeric',
        ];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $data = $this->body();
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

        return $this->ok([
            'message' => 'Perfil actualizado',
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
            return $this->fail('Completa primero el perfil de tu empresa', 422);
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

        $rules = [
            'titulo'       => 'required|min_length[5]|max_length[150]',
            'descripcion'  => 'required|min_length[20]',
            'categoria_id' => 'required|integer',
            'modalidad'    => 'required|in_list[presencial,remoto,hibrido]',
            'ciudad'       => 'required|max_length[100]',
            'region'       => 'permit_empty|max_length[100]',
            'latitud'      => 'permit_empty|numeric',
            'longitud'     => 'permit_empty|numeric',
            'salario_min'  => 'permit_empty|numeric',
            'salario_max'  => 'permit_empty|numeric|greater_than_equal_to[salario_min]',
            'anios_experiencia'    => 'permit_empty|integer|greater_than_equal_to[0]',
            'vacantes_disponibles' => 'permit_empty|integer|greater_than_equal_to[1]',
            'tipo_contrato_id'     => 'permit_empty|integer',
            'funciones'            => 'permit_empty|max_length[5000]',
            'estado'               => 'permit_empty|in_list[borrador,publicada]',
        ];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $data = $this->body();
        $slug = url_title($data['titulo'], '-', true) . '-' . $empresa['id'] . '-' . rand(100, 999);
        $estado = $data['estado'] ?? 'publicada';

        $vacanteModel = new VacanteModel();
        $vacanteModel->insert([
            'empresa_id'           => $empresa['id'],
            'categoria_id'         => (int)$data['categoria_id'],
            'titulo'               => $data['titulo'],
            'slug'                 => $slug,
            'descripcion'          => $data['descripcion'],
            'funciones'            => $data['funciones'] ?? null,
            'ciudad'               => $data['ciudad'],
            'region'               => $data['region'] ?? null,
            'latitud'              => !empty($data['latitud']) ? (float)$data['latitud'] : null,
            'longitud'             => !empty($data['longitud']) ? (float)$data['longitud'] : null,
            'modalidad'            => $data['modalidad'],
            'salario_min'          => !empty($data['salario_min']) ? (float)$data['salario_min'] : null,
            'salario_max'          => !empty($data['salario_max']) ? (float)$data['salario_max'] : null,
            'moneda'               => 'USD',
            'anios_experiencia'    => (int)($data['anios_experiencia'] ?? 0),
            'vacantes_disponibles' => (int)($data['vacantes_disponibles'] ?? 1),
            'tipo_contrato_id'     => !empty($data['tipo_contrato_id']) ? (int)$data['tipo_contrato_id'] : null,
            'estado'               => $estado,
            'fecha_publicacion'    => $estado === 'publicada' ? date('Y-m-d H:i:s') : null,
        ]);

        return $this->ok([
            'message'    => 'Vacante creada',
            'vacante_id' => $vacanteModel->getInsertID(),
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

        $rules = [
            'titulo'       => 'permit_empty|min_length[5]|max_length[150]',
            'descripcion'  => 'permit_empty|min_length[20]',
            'categoria_id' => 'permit_empty|integer',
            'modalidad'    => 'permit_empty|in_list[presencial,remoto,hibrido]',
            'ciudad'       => 'permit_empty|max_length[100]',
            'region'       => 'permit_empty|max_length[100]',
            'latitud'      => 'permit_empty|numeric',
            'longitud'     => 'permit_empty|numeric',
            'salario_min'  => 'permit_empty|numeric',
            'salario_max'  => 'permit_empty|numeric',
            'anios_experiencia'    => 'permit_empty|integer|greater_than_equal_to[0]',
            'vacantes_disponibles' => 'permit_empty|integer|greater_than_equal_to[1]',
            'tipo_contrato_id'     => 'permit_empty|integer',
            'funciones'            => 'permit_empty|max_length[5000]',
            'estado'               => 'permit_empty|in_list[borrador,publicada,pausada,cerrada]',
        ];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $data = $this->body();
        $campos = [
            'titulo', 'descripcion', 'funciones', 'categoria_id', 'modalidad',
            'ciudad', 'region', 'latitud', 'longitud', 'salario_min', 'salario_max',
            'anios_experiencia', 'vacantes_disponibles', 'tipo_contrato_id', 'estado',
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
     * GET /api/empresa/vacantes/{id}/postulaciones
     */
    public function postulaciones($vacanteId)
    {
        if ($res = $this->requireRole(['empresa'])) {
            return $res;
        }
        $empresa = $this->getEmpresa();
        if (!$empresa) {
            return $this->fail('Completa primero el perfil de tu empresa', 422);
        }

        $vacante = (new VacanteModel())->find((int)$vacanteId);
        if (!$vacante || (int)$vacante['empresa_id'] !== (int)$empresa['id']) {
            return $this->fail('Vacante no encontrada', 404);
        }

        $postulaciones = (new PostulacionModel())
            ->select('post_postulacion.*, auth_user.nombre, auth_user.apellido, auth_user.email, auth_user.avatar, cand_candidato.profesion, cand_candidato.ciudad')
            ->join('cand_candidato', 'cand_candidato.id = post_postulacion.candidato_id', 'left')
            ->join('auth_user', 'auth_user.id = cand_candidato.user_id', 'left')
            ->where('post_postulacion.vacante_id', (int)$vacanteId)
            ->orderBy('post_postulacion.created_at', 'DESC')
            ->findAll();

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
            ->select('post_postulacion.*')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
            ->where('post_postulacion.id', (int)$id)
            ->where('vac_vacante.empresa_id', $empresa['id'])
            ->first();

        if (!$postulacion) {
            return $this->fail('Postulacion no encontrada', 404);
        }

        $postulacionModel->update($postulacion['id'], ['estado' => $this->body()['estado']]);

        return $this->ok(['message' => 'Estado actualizado', 'estado' => $this->body()['estado']]);
    }

    private function getEmpresa(): ?array
    {
        return (new EmpresaModel())->where('user_id', $this->authUser()['id'] ?? 0)->first();
    }
}
