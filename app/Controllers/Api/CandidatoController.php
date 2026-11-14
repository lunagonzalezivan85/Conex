<?php

namespace App\Controllers\Api;

use App\Models\CandidatoModel;
use App\Models\CandServicioModel;
use App\Models\CVModel;
use App\Models\EducacionModel;
use App\Models\ExperienciaModel;
use App\Models\HabilidadModel;
use App\Models\IdiomaModel;
use App\Models\PostulacionModel;
use App\Models\UserModel;
use App\Models\VacanteModel;

class CandidatoController extends BaseApiController
{
    /**
     * GET /api/candidato/perfil
     * Devuelve perfil completo + listas (experiencias, educacion, habilidades,
     * idiomas, cvs, servicios) + catalogos para editar todo desde la app.
     */
    public function perfil()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }

        $user = $this->authUser();
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $user['id'])->first();
        $db = \Config\Database::connect();

        $experiencias = [];
        $educaciones  = [];
        $habilidades  = [];
        $idiomas      = [];
        $cvs          = [];
        $servicios    = [];

        if ($candidato) {
            $this->calcularPorcentajePerfil((int)$candidato['id']);
            $candidato = $candidatoModel->find($candidato['id']);

            $experiencias = (new ExperienciaModel())
                ->where('candidato_id', $candidato['id'])
                ->orderBy('actual', 'DESC')
                ->orderBy('fecha_inicio', 'DESC')
                ->findAll();

            $educaciones = $db->table('cand_educacion')
                ->select('cand_educacion.*, cat_nivel_educacion.nombre AS nivel_nombre')
                ->join('cat_nivel_educacion', 'cat_nivel_educacion.id = cand_educacion.nivel_educacion_id', 'left')
                ->where('cand_educacion.candidato_id', $candidato['id'])
                ->orderBy('cand_educacion.en_curso', 'DESC')
                ->orderBy('cand_educacion.fecha_inicio', 'DESC')
                ->get()->getResultArray();

            $habilidades = $db->table('cand_habilidad')
                ->select('cand_habilidad.id, cand_habilidad.habilidad_id, cand_habilidad.nivel, cat_habilidad.nombre, cat_habilidad.categoria_id')
                ->join('cat_habilidad', 'cat_habilidad.id = cand_habilidad.habilidad_id')
                ->where('cand_habilidad.candidato_id', $candidato['id'])
                ->get()->getResultArray();

            $idiomas = $db->table('cand_idioma')
                ->select('cand_idioma.id, cand_idioma.idioma_id, cand_idioma.nivel, cat_idioma.nombre')
                ->join('cat_idioma', 'cat_idioma.id = cand_idioma.idioma_id')
                ->where('cand_idioma.candidato_id', $candidato['id'])
                ->get()->getResultArray();

            $cvs = (new CVModel())
                ->where('candidato_id', $candidato['id'])
                ->orderBy('created_at', 'DESC')
                ->findAll();
            foreach ($cvs as &$cv) {
                $cv['url'] = base_url('api/cv/' . $cv['id']);
            }
            unset($cv);

            if ($db->tableExists('srv_servicio')) {
                $servicios = (new CandServicioModel())
                    ->where('candidato_id', $candidato['id'])
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
            }
        }

        $nivelesExp = $db->table('cat_nivel_experiencia')
            ->select('id, nombre, anios_minimos, anios_maximos')
            ->where('estado', 'activo')
            ->orderBy('orden', 'ASC')
            ->get()->getResultArray();

        $nivelesEdu = $db->table('cat_nivel_educacion')
            ->select('id, nombre')
            ->where('estado', 'activo')
            ->orderBy('orden', 'ASC')
            ->get()->getResultArray();

        $catIdiomas = $db->table('cat_idioma')
            ->select('id, nombre')
            ->where('estado', 'activo')
            ->orderBy('nombre', 'ASC')
            ->get()->getResultArray();

        $user['avatar_url'] = !empty($user['avatar']) ? base_url('uploads/' . $user['avatar']) : null;

        return $this->ok([
            'data' => [
                'user'         => $user,
                'candidato'    => $candidato,
                'experiencias' => $experiencias,
                'educaciones'  => $educaciones,
                'habilidades'  => $habilidades,
                'idiomas'      => $idiomas,
                'cvs'          => $cvs,
                'servicios'    => $servicios,
                'catalogos'    => [
                    'niveles_experiencia' => $nivelesExp,
                    'niveles_educacion'   => $nivelesEdu,
                    'idiomas'             => $catIdiomas,
                ],
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
            'disponibilidad'        => 'permit_empty|in_list[inmediata,15_dias,30_dias,a_convenir]',
            'modalidad_preferida'   => 'permit_empty|in_list[presencial,remoto,hibrido,indiferente]',
            'ofrece_servicios'      => 'permit_empty|in_list[0,1]',
            'zona_servicio'         => 'permit_empty|max_length[150]',
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
            'ofrece_servicios', 'zona_servicio',
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
            // Sin fila en srv_servicio el prestador no sale en /api/servicios:
            // al activar 'ofrezco servicios' se crea/reactiva su ficha minima
            if ((int)($update['ofrece_servicios'] ?? $candidato['ofrece_servicios'] ?? 0) === 1) {
                $this->ensureServicioActivo(array_merge($candidato, $update));
            }
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

        $pct = $this->calcularPorcentajePerfil((int)$candidato['id']);

        return $this->ok([
            'message'           => 'Perfil actualizado',
            'porcentaje_perfil' => $pct,
            'candidato'         => $candidatoModel->where('user_id', $user['id'])->first(),
        ]);
    }

    /**
     * POST /api/candidato/avatar — sube foto de perfil (JPG/PNG/WEBP, max 5MB).
     */
    public function subirAvatar()
    {
        $user = $this->authUser();
        if (empty($user['id'])) {
            return $this->fail('Token de autenticacion requerido', 401);
        }

        $file = $this->request->getFile('avatar');
        if (!$file || !$file->isValid()) {
            return $this->fail('No se recibio la imagen o archivo no valido: ' . ($file ? $file->getErrorString() : 'vacio'), 422);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowed, true)) {
            return $this->fail('Formato no permitido. Usa JPG, PNG o WEBP', 422);
        }
        if ($file->getSize() > 5242880) {
            return $this->fail('La imagen supera el limite de 5MB', 422);
        }

        try {
            $targetDir1 = FCPATH . 'uploads/avatars/';
            $targetDir2 = ROOTPATH . 'public/uploads/avatars/';
            if (!is_dir($targetDir1)) {
                @mkdir($targetDir1, 0777, true);
            }
            if (!is_dir($targetDir2)) {
                @mkdir($targetDir2, 0777, true);
            }

            $destDir = is_dir($targetDir1) ? $targetDir1 : $targetDir2;
            $newName = $file->getRandomName();
            $file->move($destDir, $newName);

            if (is_file($targetDir1 . $newName) && !is_file($targetDir2 . $newName) && is_dir($targetDir2)) {
                @copy($targetDir1 . $newName, $targetDir2 . $newName);
            } elseif (is_file($targetDir2 . $newName) && !is_file($targetDir1 . $newName) && is_dir($targetDir1)) {
                @copy($targetDir2 . $newName, $targetDir1 . $newName);
            }

            $relPath = 'avatars/' . $newName;
            (new UserModel())->update($user['id'], ['avatar' => $relPath]);

            return $this->ok([
                'message'    => 'Foto de perfil actualizada',
                'avatar'     => $relPath,
                'avatar_url' => base_url('uploads/' . $relPath),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error en subirAvatar: ' . $e->getMessage());
            return $this->fail('Error al procesar la imagen: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/candidato/disponibilidad
     * Body: { disponible: 0|1, lat?, lng? }
     * Enciende/apaga el pin 'disponible ahora' del prestador en el mapa
     * del buscador (estilo inDrive). Al apagar se limpia el timestamp.
     */
    public function disponibilidad()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        if ($res = $this->validateApi([
            'disponible' => 'required|in_list[0,1]',
            'lat'        => 'permit_empty|numeric',
            'lng'        => 'permit_empty|numeric',
        ])) {
            return $res;
        }

        $data      = $this->body();
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $on = (int) $data['disponible'] === 1;
        $update = [
            'disponible_ahora' => $on ? 1 : 0,
            'disponible_desde' => $on ? date('Y-m-d H:i:s') : null,
        ];
        if ($on) {
            // Disponible implica ofrecer: el directorio filtra por ese flag
            $update['ofrece_servicios'] = 1;
            if (isset($data['lat'], $data['lng'])) {
                $update['latitud']  = $data['lat'];
                $update['longitud'] = $data['lng'];
            }
        }

        (new CandidatoModel())->update($candidato['id'], $update);
        if ($on) {
            $this->ensureServicioActivo(array_merge($candidato, $update));
        }

        return $this->ok([
            'message'          => $on ? 'Ahora eres visible en el mapa' : 'Ya no apareces en el mapa',
            'disponible_ahora' => $on ? 1 : 0,
        ]);
    }

    /**
     * Sin una fila srv_servicio el prestador no aparece en /api/servicios
     * aunque ofrece_servicios=1. Crea la ficha minima (oficio=profesion)
     * o reactiva una pausada; el usuario la edita despues en 'mis servicios'.
     */
    private function ensureServicioActivo(array $candidato): void
    {
        $srvModel = new CandServicioModel();
        $srv = $srvModel->where('candidato_id', $candidato['id'])->first();
        if ($srv) {
            if ($srv['estado'] !== 'activo') {
                $srvModel->update($srv['id'], ['estado' => 'activo']);
            }
            return;
        }
        $srvModel->insert([
            'candidato_id' => $candidato['id'],
            'oficio'       => trim($candidato['profesion'] ?? '') ?: 'Servicio general',
            'descripcion'  => $candidato['sobre_mi'] ?? null,
            'ciudad'       => $candidato['ciudad'] ?? null,
            'zona'         => $candidato['zona_servicio'] ?? null,
            'estado'       => 'activo',
        ]);
    }

    /**
     * POST /api/candidato/habilidades
     * Body: { habilidades: [{ habilidad_id, nivel? }] }
     * Reemplaza el set actual; nivel: basico|intermedio|avanzado|experto.
     * Solo acepta IDs del catalogo cat_habilidad (la app muestra el bolso por categoria).
     */
    public function guardarHabilidades()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }

        $data = $this->body();
        $lista = $data['habilidades'] ?? [];
        if (!is_array($lista)) {
            return $this->fail('Formato de habilidades invalido', 422);
        }

        $user = $this->authUser();
        $candidato = (new CandidatoModel())->where('user_id', $user['id'])->first();
        if (!$candidato) {
            return $this->fail('Completa primero tu informacion personal', 422);
        }

        $niveles = ['basico', 'intermedio', 'avanzado', 'experto'];
        $db = \Config\Database::connect();

        $idsValidos = array_flip(array_map('intval', array_column(
            $db->table('cat_habilidad')->select('id')->get()->getResultArray(),
            'id'
        )));

        $habilidadModel = new HabilidadModel();
        $habilidadModel->where('candidato_id', $candidato['id'])->delete();

        $guardadas = 0;
        foreach ($lista as $item) {
            if (!is_array($item)) {
                continue;
            }
            $id = (int) ($item['habilidad_id'] ?? 0);
            if ($id === 0 || !isset($idsValidos[$id])) {
                continue;
            }
            $nivel = $item['nivel'] ?? 'intermedio';
            if (!in_array($nivel, $niveles, true)) {
                $nivel = 'intermedio';
            }
            $habilidadModel->insert([
                'candidato_id' => $candidato['id'],
                'habilidad_id' => $id,
                'nivel'        => $nivel,
            ]);
            $guardadas++;
        }

        return $this->ok([
            'message'           => 'Habilidades guardadas',
            'total'             => $guardadas,
            'porcentaje_perfil' => $this->calcularPorcentajePerfil((int)$candidato['id']),
        ]);
    }

    /**
     * POST /api/candidato/experiencia — crea o actualiza una experiencia laboral.
     * Body: { id?, empresa, cargo, descripcion?, fecha_inicio, fecha_fin?, actual? }
     */
    public function guardarExperiencia()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        if ($res = $this->validateApi([
            'empresa'      => 'required|min_length[2]|max_length[150]',
            'cargo'        => 'required|min_length[2]|max_length[100]',
            'descripcion'  => 'permit_empty|max_length[2000]',
            'fecha_inicio' => 'required|valid_date[Y-m-d]',
            'fecha_fin'    => 'permit_empty|valid_date[Y-m-d]',
        ])) {
            return $res;
        }

        $data = $this->body();
        $actual = !empty($data['actual']) ? 1 : 0;
        $payload = [
            'candidato_id' => $candidato['id'],
            'empresa'      => trim($data['empresa']),
            'cargo'        => trim($data['cargo']),
            'descripcion'  => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $actual ? null : (!empty($data['fecha_fin']) ? $data['fecha_fin'] : null),
            'actual'       => $actual,
        ];

        $model = new ExperienciaModel();
        if (!empty($data['id'])) {
            $existente = $model->find((int)$data['id']);
            if (!$existente || (int)$existente['candidato_id'] !== (int)$candidato['id']) {
                return $this->fail('Registro no encontrado', 404);
            }
            $model->update((int)$data['id'], $payload);
            $id = (int)$data['id'];
        } else {
            $id = $model->insert($payload, true);
        }

        return $this->ok([
            'message'           => 'Experiencia guardada',
            'experiencia'       => $model->find($id),
            'porcentaje_perfil' => $this->calcularPorcentajePerfil((int)$candidato['id']),
        ]);
    }

    /**
     * DELETE /api/candidato/experiencia/{id}
     */
    public function eliminarExperiencia($id)
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $model = new ExperienciaModel();
        $row = $model->find((int)$id);
        if (!$row || (int)$row['candidato_id'] !== (int)$candidato['id']) {
            return $this->fail('Experiencia no encontrada', 404);
        }
        $model->delete((int)$id);

        return $this->ok([
            'message'           => 'Experiencia eliminada',
            'porcentaje_perfil' => $this->calcularPorcentajePerfil((int)$candidato['id']),
        ]);
    }

    /**
     * POST /api/candidato/educacion — crea o actualiza educacion.
     * Body: { id?, nivel_educacion_id?, institucion, titulo, fecha_inicio?, fecha_fin?, en_curso? }
     */
    public function guardarEducacion()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        if ($res = $this->validateApi([
            'institucion'        => 'required|min_length[2]|max_length[150]',
            'titulo'             => 'required|min_length[2]|max_length[150]',
            'nivel_educacion_id' => 'permit_empty|integer',
            'fecha_inicio'       => 'permit_empty|valid_date[Y-m-d]',
            'fecha_fin'          => 'permit_empty|valid_date[Y-m-d]',
        ])) {
            return $res;
        }

        $data = $this->body();
        $enCurso = !empty($data['en_curso']) ? 1 : 0;
        $payload = [
            'candidato_id'       => $candidato['id'],
            'nivel_educacion_id' => !empty($data['nivel_educacion_id']) ? (int)$data['nivel_educacion_id'] : 1,
            'institucion'        => trim($data['institucion']),
            'titulo'             => trim($data['titulo']),
            'fecha_inicio'       => !empty($data['fecha_inicio']) ? $data['fecha_inicio'] : null,
            'fecha_fin'          => $enCurso ? null : (!empty($data['fecha_fin']) ? $data['fecha_fin'] : null),
            'en_curso'           => $enCurso,
        ];

        $model = new EducacionModel();
        if (!empty($data['id'])) {
            $existente = $model->find((int)$data['id']);
            if (!$existente || (int)$existente['candidato_id'] !== (int)$candidato['id']) {
                return $this->fail('Registro no encontrado', 404);
            }
            $model->update((int)$data['id'], $payload);
            $id = (int)$data['id'];
        } else {
            $id = $model->insert($payload, true);
        }

        return $this->ok([
            'message'           => 'Educacion guardada',
            'educacion'         => $model->find($id),
            'porcentaje_perfil' => $this->calcularPorcentajePerfil((int)$candidato['id']),
        ]);
    }

    /**
     * DELETE /api/candidato/educacion/{id}
     */
    public function eliminarEducacion($id)
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $model = new EducacionModel();
        $row = $model->find((int)$id);
        if (!$row || (int)$row['candidato_id'] !== (int)$candidato['id']) {
            return $this->fail('Educacion no encontrada', 404);
        }
        $model->delete((int)$id);

        return $this->ok([
            'message'           => 'Educacion eliminada',
            'porcentaje_perfil' => $this->calcularPorcentajePerfil((int)$candidato['id']),
        ]);
    }

    /**
     * POST /api/candidato/idiomas
     * Body: { idiomas: [{ idioma_id?, nombre?, nivel }] }
     */
    public function guardarIdiomas()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $lista = $this->body()['idiomas'] ?? [];
        if (!is_array($lista)) {
            return $this->fail('Formato de idiomas invalido', 422);
        }

        $niveles = ['basico', 'intermedio', 'avanzado', 'nativo'];
        $db = \Config\Database::connect();
        $idiomaModel = new IdiomaModel();
        $idiomaModel->where('candidato_id', $candidato['id'])->delete();

        $guardados = 0;
        foreach ($lista as $item) {
            if (!is_array($item)) continue;
            $idiomaId = (int)($item['idioma_id'] ?? 0);
            if ($idiomaId === 0 && !empty($item['nombre'])) {
                $nombre = trim($item['nombre']);
                $row = $db->table('cat_idioma')->where('nombre', $nombre)->get()->getRowArray();
                if ($row) {
                    $idiomaId = (int)$row['id'];
                } else {
                    $db->table('cat_idioma')->insert(['nombre' => $nombre, 'slug' => url_title($nombre, '-', true)]);
                    $idiomaId = (int)$db->insertID();
                }
            }
            if ($idiomaId === 0) continue;
            $nivel = in_array($item['nivel'] ?? '', $niveles, true) ? $item['nivel'] : 'intermedio';
            $idiomaModel->insert([
                'candidato_id' => $candidato['id'],
                'idioma_id'    => $idiomaId,
                'nivel'        => $nivel,
            ]);
            $guardados++;
        }

        return $this->ok([
            'message'           => 'Idiomas guardados',
            'total'             => $guardados,
            'porcentaje_perfil' => $this->calcularPorcentajePerfil((int)$candidato['id']),
        ]);
    }

    /**
     * POST /api/candidato/servicios — crea o actualiza ficha de servicio del prestador.
     * Body: { id?, oficio, descripcion?, precio_desde?, precio_tipo?, ciudad?, zona?, estado? }
     */
    public function guardarServicio()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        if ($res = $this->validateApi([
            'oficio'       => 'required|min_length[2]|max_length[100]',
            'descripcion'  => 'permit_empty|max_length[2000]',
            'precio_desde' => 'permit_empty',
            'ciudad'       => 'permit_empty|max_length[100]',
            'zona'         => 'permit_empty|max_length[150]',
        ])) {
            return $res;
        }

        $data = $this->body();
        $precioTipo = $data['precio_tipo'] ?? 'a_convenir';
        if (!in_array($precioTipo, ['por_hora', 'por_servicio', 'a_convenir'], true)) {
            $precioTipo = 'a_convenir';
        }
        $estado = ($data['estado'] ?? 'activo') === 'pausado' ? 'pausado' : 'activo';

        $precioDesde = (isset($data['precio_desde']) && is_numeric($data['precio_desde'])) ? (float)$data['precio_desde'] : null;

        $payload = [
            'candidato_id' => $candidato['id'],
            'oficio'       => trim($data['oficio']),
            'descripcion'  => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            'precio_desde' => $precioDesde,
            'precio_tipo'  => $precioTipo,
            'ciudad'       => !empty($data['ciudad']) ? trim($data['ciudad']) : ($candidato['ciudad'] ?? null),
            'zona'         => !empty($data['zona']) ? trim($data['zona']) : ($candidato['zona_servicio'] ?? null),
            'estado'       => $estado,
        ];

        $srvModel = new CandServicioModel();
        if (!empty($data['id'])) {
            $existente = $srvModel->find((int)$data['id']);
            if (!$existente || (int)$existente['candidato_id'] !== (int)$candidato['id']) {
                return $this->fail('Servicio no encontrado', 404);
            }
            $srvModel->update((int)$data['id'], $payload);
            $id = (int)$data['id'];
        } else {
            $id = $srvModel->insert($payload, true);
        }

        if (empty($candidato['ofrece_servicios'])) {
            (new CandidatoModel())->update($candidato['id'], ['ofrece_servicios' => 1]);
        }

        return $this->ok([
            'message'  => 'Servicio guardado',
            'servicio' => $srvModel->find($id),
        ]);
    }

    /**
     * DELETE /api/candidato/servicios/{id}
     */
    public function eliminarServicio($id)
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $srvModel = new CandServicioModel();
        $row = $srvModel->find((int)$id);
        if (!$row || (int)$row['candidato_id'] !== (int)$candidato['id']) {
            return $this->fail('Servicio no encontrado', 404);
        }
        $srvModel->delete((int)$id);

        return $this->ok(['message' => 'Servicio eliminado']);
    }

    /**
     * GET /api/candidato/solicitudes — solicitudes de contacto recibidas y enviadas.
     */
    public function solicitudes()
    {
        if ($res = $this->requireRole(['candidato', 'empresa'])) {
            return $res;
        }

        $db = \Config\Database::connect();
        $user = $this->authUser();
        $candidato = $this->getCandidato();

        $recibidas = [];
        if ($candidato && $db->tableExists('srv_contacto')) {
            $recibidas = $db->table('srv_contacto')
                ->select('srv_contacto.id, srv_contacto.nombre, srv_contacto.mensaje,
                    srv_contacto.respuesta, srv_contacto.precio_propuesto, srv_contacto.fecha_respuesta,
                    srv_contacto.estado, srv_contacto.created_at,
                    srv_servicio.oficio,
                    IF(srv_contacto.estado = "aceptado", srv_contacto.telefono, NULL) AS telefono')
                ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
                ->where('srv_servicio.candidato_id', $candidato['id'])
                ->orderBy('srv_contacto.created_at', 'DESC')
                ->limit(100)
                ->get()->getResultArray();
        }

        $enviadas = [];
        if ($db->tableExists('srv_contacto')) {
            $enviadas = $db->table('srv_contacto')
                ->select('srv_contacto.id, srv_contacto.mensaje,
                    srv_contacto.respuesta, srv_contacto.precio_propuesto, srv_contacto.fecha_respuesta,
                    srv_contacto.estado, srv_contacto.created_at,
                    srv_servicio.oficio, srv_servicio.ciudad, srv_servicio.zona,
                    auth_user.nombre AS prestador_nombre,
                    IF(srv_contacto.estado = "aceptado", auth_user.telefono, NULL) AS prestador_telefono')
                ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
                ->join('cand_candidato', 'cand_candidato.id = srv_servicio.candidato_id')
                ->join('auth_user', 'auth_user.id = cand_candidato.user_id')
                ->where('srv_contacto.user_id', $user['id'])
                ->orderBy('srv_contacto.created_at', 'DESC')
                ->limit(100)
                ->get()->getResultArray();
        }

        // Cargar historial de conversacion / mensajes estilo Messenger si existe la tabla
        $allIds = array_filter(array_unique(array_merge(
            array_column($recibidas, 'id'),
            array_column($enviadas, 'id')
        )));

        $mensajesPorContacto = [];
        if (!empty($allIds) && $db->tableExists('srv_contacto_mensaje')) {
            $msgs = $db->table('srv_contacto_mensaje')
                ->whereIn('contacto_id', $allIds)
                ->orderBy('created_at', 'ASC')
                ->get()->getResultArray();
            foreach ($msgs as $m) {
                $mensajesPorContacto[$m['contacto_id']][] = $m;
            }
        }

        foreach ($recibidas as &$r) {
            $r['mensajes'] = $mensajesPorContacto[$r['id']] ?? [];
        }
        unset($r);

        foreach ($enviadas as &$e) {
            $e['mensajes'] = $mensajesPorContacto[$e['id']] ?? [];
        }
        unset($e);

        return $this->ok([
            'data' => [
                'recibidas' => $recibidas,
                'enviadas'  => $enviadas,
            ],
        ]);
    }

    /**
     * POST /api/candidato/solicitudes/{id}
     * Responder o negociar en la app (estilo chat / messenger)
     * Body: { accion?: 'aceptar'|'rechazar'|'negociar'|'mensaje', mensaje?: string, precio_propuesto?: number|string }
     */
    public function responderSolicitud($id)
    {
        if ($res = $this->requireRole(['candidato', 'empresa'])) {
            return $res;
        }

        $user = $this->authUser();
        $candidato = $this->getCandidato();
        $db = \Config\Database::connect();
        $body = $this->body();

        // Buscar solicitud y determinar si es el prestador o el cliente
        $sol = $db->table('srv_contacto')
            ->select('srv_contacto.*, srv_servicio.candidato_id as servicio_candidato_id')
            ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
            ->where('srv_contacto.id', (int)$id)
            ->get()->getRowArray();

        if (!$sol) {
            return $this->fail('Solicitud no encontrada', 404);
        }

        $esPrestador = ($candidato && (int)$sol['servicio_candidato_id'] === (int)$candidato['id']);
        $esCliente = ((int)$sol['user_id'] === (int)$user['id']);

        if (!$esPrestador && !$esCliente) {
            return $this->fail('No tienes permisos sobre esta solicitud', 403);
        }

        $accion = $body['accion'] ?? '';
        $mensajeTexto = trim($body['mensaje'] ?? $body['mensaje_respuesta'] ?? '');
        $precioPropuesto = isset($body['precio_propuesto']) && $body['precio_propuesto'] !== '' ? (float)$body['precio_propuesto'] : null;

        if (empty($accion) && !empty($mensajeTexto)) {
            $accion = 'negociar';
        }

        if (!in_array($accion, ['aceptar', 'rechazar', 'negociar', 'mensaje'], true) && empty($mensajeTexto)) {
            return $this->fail('Debes indicar una acción o escribir un mensaje de respuesta', 422);
        }

        $nuevoEstado = $sol['estado'];
        if ($accion === 'aceptar') {
            $nuevoEstado = 'aceptado';
        } elseif ($accion === 'rechazar') {
            $nuevoEstado = 'rechazado';
        }

        // Actualizar registro principal
        $updateData = [
            'estado'     => $nuevoEstado,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if (!empty($mensajeTexto)) {
            $updateData['respuesta'] = $mensajeTexto;
            $updateData['fecha_respuesta'] = date('Y-m-d H:i:s');
        }
        if ($precioPropuesto !== null) {
            $updateData['precio_propuesto'] = $precioPropuesto;
        }

        $db->table('srv_contacto')->where('id', (int)$id)->update($updateData);

        // Si se envió mensaje, guardarlo en el hilo de conversacion srv_contacto_mensaje
        if (!empty($mensajeTexto) && $db->tableExists('srv_contacto_mensaje')) {
            $db->table('srv_contacto_mensaje')->insert([
                'contacto_id'      => (int)$id,
                'emisor_tipo'      => $esPrestador ? 'prestador' : 'cliente',
                'emisor_nombre'    => trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?: 'Usuario',
                'mensaje'          => $mensajeTexto,
                'precio_propuesto' => $precioPropuesto,
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        // Obtener historial completo de mensajes actualizado
        $mensajes = [];
        if ($db->tableExists('srv_contacto_mensaje')) {
            $mensajes = $db->table('srv_contacto_mensaje')
                ->where('contacto_id', (int)$id)
                ->orderBy('created_at', 'ASC')
                ->get()->getResultArray();
        }

        $msgRetorno = 'Respuesta enviada con éxito.';
        if ($accion === 'aceptar') {
            $msgRetorno = 'Solicitud aceptada. Ya puedes coordinar con el cliente.';
        } elseif ($accion === 'rechazar') {
            $msgRetorno = 'Solicitud rechazada.';
        } elseif ($precioPropuesto !== null) {
            $msgRetorno = 'Propuesta de negociación enviada al cliente.';
        }

        return $this->ok([
            'message'          => $msgRetorno,
            'estado'           => $nuevoEstado,
            'telefono'         => $nuevoEstado === 'aceptado' ? $sol['telefono'] : null,
            'precio_propuesto' => $precioPropuesto ?? $sol['precio_propuesto'],
            'respuesta'        => $mensajeTexto ?: ($sol['respuesta'] ?? null),
            'mensajes'         => $mensajes,
        ]);
    }

    /**dashboard
     * Una sola llamada para el inicio de la app: stats del candidato
     * (postulaciones, servicios brindados = contactos aceptados, vistas al
     * perfil) + sus servicios publicados para el toggle vacantes/servicios.
     */
    public function dashboard()
    {
        if ($res = $this->requireRole(['candidato'])) {
            return $res;
        }
        $candidato = $this->getCandidato();
        if ($res = $this->requireCandidato($candidato)) {
            return $res;
        }

        $db = \Config\Database::connect();

        $postulaciones = (new PostulacionModel())
            ->where('candidato_id', $candidato['id'])
            ->countAllResults();

        $servicios = [];
        $brindados = 0;
        // Las tablas srv_* existen desde la migracion ServiciosPrestador
        if ($db->tableExists('srv_servicio')) {
            $servicios = (new CandServicioModel())
                ->where('candidato_id', $candidato['id'])
                ->orderBy('created_at', 'DESC')
                ->findAll();

            if ($db->tableExists('srv_contacto')) {
                $brindados = $db->table('srv_contacto')
                    ->join('srv_servicio', 'srv_servicio.id = srv_contacto.servicio_id')
                    ->where('srv_servicio.candidato_id', $candidato['id'])
                    ->where('srv_contacto.estado', 'aceptado')
                    ->countAllResults();
            }
        }

        return $this->ok([
            'data' => [
                'postulaciones'       => $postulaciones,
                'servicios_brindados' => $brindados,
                'vistas_perfil'       => (int) ($candidato['vistas_perfil'] ?? 0),
                'ofrece_servicios'    => (int) ($candidato['ofrece_servicios'] ?? 0),
                'disponible_ahora'    => (int) ($candidato['disponible_ahora'] ?? 0),
                'servicios'           => $servicios,
            ],
        ]);
    }

    /**
     * GET /api/candidato/
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

    private function calcularPorcentajePerfil(int $candidatoId): int
    {
        $db = \Config\Database::connect();
        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->find($candidatoId);
        if (!$candidato) {
            return 0;
        }

        $porcentaje = 0;

        // Datos personales y profesionales (40%)
        if (!empty($candidato['profesion'])) $porcentaje += 5;
        if (!empty($candidato['sobre_mi'])) $porcentaje += 10;
        if (!empty($candidato['ciudad'])) $porcentaje += 5;
        if (!empty($candidato['disponibilidad'])) $porcentaje += 5;
        if (!empty($candidato['modalidad_preferida'])) $porcentaje += 5;
        if (!empty($candidato['linkedin_url']) || !empty($candidato['portafolio_url'])) $porcentaje += 5;
        if (!empty($candidato['direccion']) || !empty($candidato['zona_servicio'])) $porcentaje += 5;

        // Experiencia (20%)
        if ($db->table('cand_experiencia')->where('candidato_id', $candidatoId)->countAllResults() > 0) {
            $porcentaje += 20;
        }

        // Educacion (15%)
        if ($db->table('cand_educacion')->where('candidato_id', $candidatoId)->countAllResults() > 0) {
            $porcentaje += 15;
        }

        // Habilidades (15%)
        if ($db->table('cand_habilidad')->where('candidato_id', $candidatoId)->countAllResults() > 0) {
            $porcentaje += 15;
        }

        // Idiomas o CV subido (10%)
        $tieneIdioma = $db->table('cand_idioma')->where('candidato_id', $candidatoId)->countAllResults() > 0;
        $tieneCv     = $db->table('cand_cv')->where('candidato_id', $candidatoId)->countAllResults() > 0;
        if ($tieneIdioma || $tieneCv) {
            $porcentaje += 10;
        }

        $porcentaje = min(100, $porcentaje);
        $candidatoModel->update($candidatoId, ['porcentaje_perfil' => $porcentaje]);

        if ($porcentaje >= 60 && !empty($candidato['user_id'])) {
            (new UserModel())->update($candidato['user_id'], ['perfil_completo' => true]);
        }

        return $porcentaje;
    }

    private function getCandidato(): ?array
    {
        $user = $this->authUser();
        $userId = $user['id'] ?? 0;
        if (!$userId) {
            return null;
        }

        $candidatoModel = new CandidatoModel();
        $candidato = $candidatoModel->where('user_id', $userId)->first();
        if (!$candidato) {
            $candidatoModel->insert([
                'user_id'   => $userId,
                'apellidos' => $user['apellido'] ?? '',
                'ciudad'    => 'Managua',
                'pais'      => 'Nicaragua',
            ]);
            $candidato = $candidatoModel->where('user_id', $userId)->first();
        }

        return $candidato;
    }

    private function requireCandidato(?array $candidato): ?\CodeIgniter\HTTP\ResponseInterface
    {
        if (!$candidato) {
            return $this->fail('Completa primero tu informacion personal', 422);
        }
        return null;
    }
}
