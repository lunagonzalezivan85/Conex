<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AiAnalisisCv extends Migration
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
                'null'      => false,
            ],
            'postulacion_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'vacante_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'prompt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'respuesta' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'score_match' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'fortalezas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'debilidades' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recomendacion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'modelo_ia' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'gemini',
                'null'       => false,
            ],
            'tokens_usados' => [
                'type'       => 'INT',
                'constraint'  => 11,
                'null'       => true,
            ],
            'tiempo_respuesta_ms' => [
                'type'       => 'INT',
                'constraint'  => 11,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['procesando', 'completado', 'error'],
                'default'    => 'procesando',
                'null'       => false,
            ],
            'error' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('candidato_id', 'cand_candidato', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cv_id', 'cand_cv', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('postulacion_id', 'post_postulacion', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('vacante_id', 'vac_vacante', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('ai_analisis_cv');
    }

    public function down()
    {
        $this->forge->dropTable('ai_analisis_cv');
    }
}
