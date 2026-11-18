<?php

namespace App\Libraries;

use App\Models\UserModel;
use App\Models\CVModel;

/**
 * AccountPurgeService
 *
 * Implementa la baja y purga integral de datos de usuarios en estricto cumplimiento
 * de la Cláusula 6 de la Política de Privacidad de CONEX:
 * "Al confirmar la eliminación, la cuenta, los currículums, fotografías y fichas de
 * servicio son dados de baja y eliminados de forma permanente de nuestra base de datos activa."
 */
class AccountPurgeService
{
    /**
     * Ejecuta la purga integral de la cuenta y sus archivos físicos asociados.
     *
     * @param int $userId ID del usuario a purgar
     * @param string $reason Motivo o canal de la eliminación
     * @return array Resumen de la operación
     */
    public static function purgeUser(int $userId, string $reason = 'Solicitud de usuario'): array
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $user = $userModel->withDeleted()->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'error'   => 'Usuario no encontrado',
            ];
        }

        $summary = [
            'user_id'         => $userId,
            'cv_files_purged' => 0,
            'cv_rows_deleted' => 0,
            'services_closed' => 0,
            'avatar_deleted'  => false,
            'vacancies_closed'=> 0,
            'tokens_revoked'  => 0,
        ];

        // 1. Revocar tokens de API activos
        if ($db->tableExists('auth_token')) {
            $tokenBuilder = $db->table('auth_token')->where('user_id', $userId);
            $summary['tokens_revoked'] = $tokenBuilder->countAllResults(false);
            $tokenBuilder->delete();
        }

        // 2. Procesar datos de Candidato si aplica
        if ($db->tableExists('cand_candidato')) {
            $candidato = $db->table('cand_candidato')->where('user_id', $userId)->get()->getRowArray();

            if ($candidato) {
                $candId = (int)$candidato['id'];

                // 2.1 Purgar archivos físicos de CVs y registros en cand_cv
                if ($db->tableExists('cand_cv')) {
                    $cvModel = new CVModel();
                    $cvs = $cvModel->where('candidato_id', $candId)->findAll();

                    foreach ($cvs as $cv) {
                        if (!empty($cv['archivo_path'])) {
                            $rutasPosibles = [
                                WRITEPATH . $cv['archivo_path'],
                                ROOTPATH . 'writable/' . $cv['archivo_path'],
                                FCPATH . $cv['archivo_path'],
                                ROOTPATH . 'public/' . $cv['archivo_path'],
                            ];

                            foreach ($rutasPosibles as $ruta) {
                                if (is_file($ruta)) {
                                    @unlink($ruta);
                                    $summary['cv_files_purged']++;
                                    break;
                                }
                            }
                        }

                        // Eliminación permanente de la base de datos activa
                        $cvModel->delete($cv['id'], true);
                        $summary['cv_rows_deleted']++;
                    }
                }

                // 2.2 Purgar / dar de baja fichas de servicio activas
                if ($db->tableExists('srv_servicio')) {
                    $servicios = $db->table('srv_servicio')->where('candidato_id', $candId)->get()->getResultArray();

                    foreach ($servicios as $srv) {
                        // Archivar solicitudes de contacto vinculadas
                        if ($db->tableExists('srv_contacto')) {
                            $db->table('srv_contacto')
                                ->where('servicio_id', (int)$srv['id'])
                                ->whereIn('estado', ['pendiente', 'aceptado'])
                                ->update([
                                    'estado'     => 'archivado',
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        $db->table('srv_servicio')->where('id', (int)$srv['id'])->update([
                            'estado'     => 'baja',
                            'deleted_at' => date('Y-m-d H:i:s'),
                        ]);
                        $summary['services_closed']++;
                    }
                }

                // 2.3 Retirar postulaciones activas a vacantes
                if ($db->tableExists('post_postulacion')) {
                    $db->table('post_postulacion')
                        ->where('candidato_id', $candId)
                        ->whereNotIn('estado', ['contratado', 'rechazada'])
                        ->update([
                            'estado'     => 'rechazada',
                            'deleted_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                // 2.4 Dar de baja perfil del candidato y limpiar geolocalización / teléfono
                $db->table('cand_candidato')->where('id', $candId)->update([
                    'telefono'          => null,
                    'direccion'         => null,
                    'latitud'           => null,
                    'longitud'          => null,
                    'ofrece_servicios'  => 0,
                    'disponible_ahora'  => 0,
                    'disponible_desde'  => null,
                    'deleted_at'        => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 3. Procesar datos de Empresa si aplica
        if ($db->tableExists('emp_empresa')) {
            $empresa = $db->table('emp_empresa')->where('user_id', $userId)->get()->getRowArray();

            if ($empresa) {
                $empId = (int)$empresa['id'];

                // Eliminar logotipo físico si existe
                if (!empty($empresa['logo'])) {
                    $logoClean = ltrim(str_replace('uploads/', '', $empresa['logo']), '/');
                    $logoPaths = [
                        FCPATH . 'uploads/' . $logoClean,
                        FCPATH . 'uploads/logos/' . basename($logoClean),
                        FCPATH . $empresa['logo'],
                        ROOTPATH . 'public/uploads/' . $logoClean,
                    ];
                    foreach ($logoPaths as $lp) {
                        if (is_file($lp)) {
                            @unlink($lp);
                            break;
                        }
                    }
                }

                // Cancelar vacantes activas
                if ($db->tableExists('vac_vacante')) {
                    $vacBuilder = $db->table('vac_vacante')
                        ->where('empresa_id', $empId)
                        ->where('estado !=', 'cancelada');
                    $summary['vacancies_closed'] = $vacBuilder->countAllResults(false);

                    $vacBuilder->update([
                        'estado'     => 'cancelada',
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $db->table('emp_empresa')->where('id', $empId)->update([
                    'telefono'   => null,
                    'direccion'  => null,
                    'latitud'    => null,
                    'longitud'   => null,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 4. Purgar avatar del usuario físicamente del disco
        if (!empty($user['avatar'])) {
            $avatarClean = ltrim(str_replace('uploads/', '', $user['avatar']), '/');
            $avatarPaths = [
                FCPATH . 'uploads/' . $avatarClean,
                FCPATH . 'uploads/avatars/' . basename($avatarClean),
                FCPATH . $user['avatar'],
                ROOTPATH . 'public/uploads/' . $avatarClean,
                ROOTPATH . 'public/uploads/avatars/' . basename($avatarClean),
            ];

            foreach ($avatarPaths as $ap) {
                if (is_file($ap)) {
                    @unlink($ap);
                    $summary['avatar_deleted'] = true;
                    break;
                }
            }
        }

        // 5. Limpiar notificaciones
        if ($db->tableExists('not_notificacion')) {
            $db->table('not_notificacion')->where('user_id', $userId)->delete();
        }

        // 6. Anonimizar y dar de baja en auth_user
        // Se preserva la integridad relacional histórica en logs/auditoría sin datos personales identificables
        $randomHash = bin2hex(random_bytes(16));
        $anonymizedEmail = 'baja_' . $userId . '_' . time() . '@conex.local';

        $userModel->update($userId, [
            'nombre'          => 'Usuario',
            'apellido'        => 'Eliminado',
            'usuario'         => 'deleted_' . $userId . '_' . substr($randomHash, 0, 6),
            'email'           => $anonymizedEmail,
            'telefono'        => null,
            'avatar'          => null,
            'password'        => password_hash($randomHash, PASSWORD_BCRYPT),
            'estado'          => 'inactivo',
            'perfil_completo' => 0,
        ]);

        $userModel->delete($userId); // soft-delete con deleted_at

        log_message('info', "AccountPurgeService: Usuario {$userId} purgado exitosamente. Motivo: {$reason}. Detalle: " . json_encode($summary));

        return [
            'success' => true,
            'summary' => $summary,
        ];
    }
}
