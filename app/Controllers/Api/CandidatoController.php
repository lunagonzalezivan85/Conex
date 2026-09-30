<?php

namespace App\Controllers\Api;

use App\Models\CandidatoModel;
use App\Models\CVModel;
use App\Models\PostulacionModel;
use App\Models\UserModel;
use App\Models\VacanteModel;

class CandidatoController extends BaseApiController
{
    /**
     * GET /api/candidato/perfil
     */
    public function perfil()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }

        $user = $this->authUser();
        $candidato = (new CandidatoModel())->where('user_id', $user['id'])->first();

        return $this->ok([
            'data' => [
                'user'      => $user,
                'candidato' => $candidato,
            ],
        ]);
    }

    /**
     * POST /api/candidato/perfil — actualiza datos del candidato y usuario.
     */
    public function actualizarPerfil()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }

        $rules = [
            'nombre'      => 'permit_empty|min_length[2]|max_length[100]',
            'apellidos'   => 'permit_empty|min_length[2]|max_length[150]',
            'telefono'    => 'permit_empty|min_length[8]|max_length[20]',
            'profesion'   => 'permit_empty|max_length[150]',
            'sobre_mi'    => 'permit_empty|max_length[2000]',
            'ciudad'      => 'permit_empty|max_length[100]',
            'region'      => 'permit_empty|max_length[100]',
            'pais'        => 'permit_empty|max_length[100]',
            'direccion'   => 'permit_empty|max_length[255]',
            'latitud'     => 'permit_empty|numeric',
            'longitud'    => 'permit_empty|numeric',
            'disponibilidad'        => 'permit_empty|in_list[inmediata,1_semana,2_semanas,1_mes]',
            'modalidad_preferida'   => 'permit_empty|in_list[presencial,remoto,hibrido]',
            'salario_esperado_usd'  => 'permit_empty|numeric',
            'portafolio_url'        => 'permit_empty|valid_url_strict|max_length[255]',
            'linkedin_url'          => 'permit_empty|valid_url_strict|max_length[255]',
            'fecha_nacimiento'      => 'permit_empty|valid_date[Y-m-d]',
        ];
        if ($res = $this->validateApi($rules)) {
            return $res;
        }

        $data = $this->body();
        $user = $this->authUser();

        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $user['id'])->first();
        if (!$candidato) {
            $candidatoModel->insert(['user_id' => $user['id'], 'apellidos' => $data['apellidos'] ?? '']);
            $candidato = $candidatoModel->where('user_id', $user['id'])->first();
        }

        // Campos de cand_candidato
        $camposCandidato = [
            'apellidos', 'fecha_nacimiento', 'profesion', 'nivel_experiencia_id',
            'sobre_mi', 'direccion', 'ciudad', 'region', 'pais', 'latitud', 'longitud',
            'disponibilidad', 'modalidad_preferida', 'salario_esperado_usd',
            'portafolio_url', 'linkedin_url',
        ];
        $update = [];
        foreach ($camposCandidato as $campo) {
            if (array_key_exists($campo, $data)) {
                $update[$campo] = $data[$campo] === '' ? null : $data[$campo];
            }
        }
        if (!empty($update)) {
            $candidatoModel->update($candidato['id'], $update);
        }

        // Campos de auth_user
        $userModel = new UserModel();
        $updateUser = [];
        if (isset($data['nombre']))   $updateUser['nombre'] = $data['nombre'];
        if (isset($data['apellido'])) $updateUser['apellido'] = $data['apellido'];
        if (isset($data['telefono'])) $updateUser['telefono'] = $data['telefono'];
        if (!empty($updateUser)) {
            $userModel->update($user['id'], $updateUser);
        }

        return $this->ok([
            'message'   => 'Perfil actualizado',
            'candidato' => $candidatoModel->where('user_id', $user['id'])->first(),
        ]);
    }

    /**
     * GET /api/candidato/cvs
     */
    public function cvs()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $cvs = (new CVModel())->where('candidato_id', $candidato['id'])->orderBy('created_at', 'DESC')->findAll();
        foreach ($cvs as &$cv) {
            $cv['url'] = base_url('api/cv/' . $cv['id']);
        }
        return $this->ok(['data' => $cvs]);
    }

    /**
     * POST /api/candidato/cv — multipart con campo "cv" (PDF/DOC/DOCX, max 5MB)
     */
    public function subirCv()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $file = $this->request->getFile('cv');
        if (!$file || !$file->isValid()) {
            return $this->fail('No se recibio el archivo', 422);
        }

        $allowed = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file->getMimeType(), $allowed, true)) {
            return $this->fail('Formato no permitido. Solo PDF, DOC o DOCX', 422);
        }
        if ($file->getSize() > 5242880) {
            return $this->fail('El archivo supera el limite de 5MB', 422);
        }

        $uploadPath = WRITEPATH . 'uploads/cv/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $cvModel = new CVModel();
        $cvId = $cvModel->insert([
            'candidato_id'   => $candidato['id'],
            'archivo_path'   => 'uploads/cv/' . $newName,
            'archivo_nombre' => $file->getClientName(),
            'es_principal'   => true,
        ], true);

        return $this->ok([
            'message' => 'CV subido correctamente',
            'cv'      => $cvModel->find($cvId),
        ], 201);
    }

    /**
     * DELETE /api/candidato/cv/{id}
     */
    public function eliminarCv($id)
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $cvModel = new CVModel();
        $cv = $cvModel->find((int)$id);
        if (!$cv || (int)$cv['candidato_id'] !== (int)$candidato['id']) {
            return $this->fail('CV no encontrado', 404);
        }

        $path = WRITEPATH . $cv['archivo_path'];
        if (is_file($path)) {
            unlink($path);
        }
        $cvModel->delete($cv['id']);

        return $this->ok(['message' => 'CV eliminado']);
    }

    /**
     * GET /api/candidato/postulaciones
     */
    public function postulaciones()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $postulaciones = (new PostulacionModel())
            ->select('post_postulacion.*, vac_vacante.titulo, vac_vacante.slug, vac_vacante.ciudad, vac_vacante.modalidad, vac_vacante.estado AS vacante_estado, emp_empresa.razon_social AS empresa_nombre')
            ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id', 'left')
            ->join('emp_empresa', 'emp_empresa.id = vac_vacante.empresa_id', 'left')
            ->where('post_postulacion.candidato_id', $candidato['id'])
            ->orderBy('post_postulacion.created_at', 'DESC')
            ->findAll();

        return $this->ok(['data' => $postulaciones]);
    }

    /**
     * POST /api/candidato/postularse/{vacante_id}
     * Body: { cv_id?, mensaje? }
     */
    public function postularse($vacanteId)
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $vacante = (new VacanteModel())->find((int)$vacanteId);
        if (!$vacante || $vacante['estado'] !== 'publicada') {
            return $this->fail('Esta vacante no esta disponible', 404);
        }

        $postulacionModel = new PostulacionModel();
        $yaPostulado = $postulacionModel
            ->where('vacante_id', (int)$vacanteId)
            ->where('candidato_id', $candidato['id'])
            ->countAllResults() > 0;

        if ($yaPostulado) {
            return $this->fail('Ya te has postulado a esta vacante', 409);
        }

        $data = $this->body();
        $cvId = null;
        if (!empty($data['cv_id'])) {
            $cv = (new CVModel())->find((int)$data['cv_id']);
            if (!$cv || (int)$cv['candidato_id'] !== (int)$candidato['id']) {
                return $this->fail('CV no valido', 422);
            }
            $cvId = (int)$data['cv_id'];
        }

        $postulacionModel->insert([
            'vacante_id'   => (int)$vacanteId,
            'candidato_id' => $candidato['id'],
            'cv_id'        => $cvId,
            'mensaje'      => $data['mensaje'] ?? null,
            'estado'       => 'enviada',
        ]);

        return $this->ok([
            'message'        => 'Te has postulado correctamente',
            'postulacion_id' => $postulacionModel->getInsertID(),
        ], 201);
    }

    private function getCandidato(): ?array
    {
        return (new CandidatoModel())->where('user_id', $this->authUser()['id'] ?? 0)->first();
    }

    private function requireCandidato(?array $candidato): ?\CodeIgniter\HTTP\ResponseInterface
    {
        if (!$candidato) {
            return $this->fail('Completa primero tu informacion personal', 422);
        }
        return null;
    }
}
