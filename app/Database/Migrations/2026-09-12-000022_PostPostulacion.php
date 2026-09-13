<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PostPostulacion extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'vacante_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'candidato_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'cv_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'mensaje' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['enviada', 'en_revision', 'aceptada', 'rechazada', 'descartada'],
                'default'    => 'enviada',
                'null'       => false,
            ],
            'puntaje_match' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'fecha_entrevista' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notas_empresa' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vacante_id', 'vac_vacante', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('candidato_id', 'cand_candidato', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('cv_id', 'cand_cv', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('post_postulacion');
    }

    public function down()
    {
        $this->forge->dropTable('post_postulacion');
    }
}
