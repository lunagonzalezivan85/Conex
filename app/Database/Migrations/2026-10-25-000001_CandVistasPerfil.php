<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Contador de vistas al perfil publico del candidato.
 * Se incrementa cuando un visitante abre su pagina de prestador
 * (PublicController::verPrestador) o cuando una empresa revisa
 * su perfil desde una postulacion (EmpresaController::verPostulante).
 */
class CandVistasPerfil extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cand_candidato', [
            'vistas_perfil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'prestador_verificado',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('cand_candidato', 'vistas_perfil');
    }
}
