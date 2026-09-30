<?php

namespace App\Controllers\Api;

use App\Models\CVModel;

/**
 * GET /api/cv/{id} — descarga un CV.
 * Permisos: el candidato dueno del CV, o la empresa duena de una
 * vacante a la que se uso ese CV en una postulacion.
 */
class CvController extends BaseApiController
{
    public function descargar($id)
    {
        $cv = (new CVModel())->find((int)$id);
        if (!$cv) {
            return $this->fail('CV no encontrado', 404);
        }

        $role = $this->roleSlug();
        $user = $this->authUser();
        $permitido = false;

        if ($role === 'candidato') {
            $candidato = \Config\Database::connect()->table('cand_candidato')
                ->where('user_id', $user['id'])->get()->getRowArray();
            $permitido = $candidato && (int)$candidato['id'] === (int)$cv['candidato_id'];
        } elseif ($role === 'empresa') {
            $empresa = \Config\Database::connect()->table('emp_empresa')
                ->where('user_id', $user['id'])->get()->getRowArray();
            if ($empresa) {
                $permitido = \Config\Database::connect()->table('post_postulacion')
                    ->join('vac_vacante', 'vac_vacante.id = post_postulacion.vacante_id')
                    ->where('post_postulacion.cv_id', $cv['id'])
                    ->where('vac_vacante.empresa_id', $empresa['id'])
                    ->countAllResults() > 0;
            }
        }

        if (!$permitido) {
            return $this->fail('No tienes acceso a este CV', 403);
        }

        $path = WRITEPATH . $cv['archivo_path'];
        if (!is_file($path)) {
            return $this->fail('El archivo ya no existe', 404);
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path) ?: 'application/octet-stream')
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($cv['archivo_nombre']) . '"')
            ->setBody(file_get_contents($path));
    }
}
