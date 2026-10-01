<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Captacion de vacantes externas (ej. Facebook) + metricas de interaccion.
 * - empresa_id nullable: el admin puede registrar vacante sin empresa registrada
 *   (se asigna luego cuando se contacta/afilia la empresa).
 * - empresa_externa/contacto_externo/origen_url/origen: datos de captacion.
 * - poster: imagen del anuncio original.
 * - vistas/me_gusta/no_me_gusta/compartidos: contadores de metricas.
 * - vac_reaccion: una reaccion por vacante+identificador (user o sesion anonima).
 */
class VacVacanteCaptacion extends Migration
{
    public function up()
    {
        // empresa_id pasa a ser opcional (vacantes captadas externamente)
        $this->forge->modifyColumn('vac_vacante', [
            'empresa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addColumn('vac_vacante', [
            'empresa_externa' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'empresa_id',
            ],
            'contacto_externo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'empresa_externa',
            ],
            'origen' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'manual',
                'null'       => false,
                'after'      => 'contacto_externo',
            ],
            'origen_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'origen',
            ],
            'poster' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'origen_url',
            ],
            'vistas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'destacada',
            ],
            'me_gusta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'vistas',
            ],
            'no_me_gusta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'me_gusta',
            ],
            'compartidos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'no_me_gusta',
            ],
        ]);

        // Reacciones por usuario/sesion (evita votos repetidos)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'vacante_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'identificador' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['me_gusta', 'no_me_gusta'],
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['vacante_id', 'tipo', 'identificador'], 'vac_reaccion_uniq');
        $this->forge->addForeignKey('vacante_id', 'vac_vacante', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vac_reaccion');
    }

    public function down()
    {
        $this->forge->dropTable('vac_reaccion');
        $this->forge->dropColumn('vac_vacante', [
            'empresa_externa', 'contacto_externo', 'origen', 'origen_url',
            'poster', 'vistas', 'me_gusta', 'no_me_gusta', 'compartidos',
        ]);
        $this->forge->modifyColumn('vac_vacante', [
            'empresa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
