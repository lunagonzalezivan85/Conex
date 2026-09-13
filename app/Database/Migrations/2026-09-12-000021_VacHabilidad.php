<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VacHabilidad extends Migration
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
            'habilidad_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'nivel_requerido' => [
                'type'       => 'ENUM',
                'constraint' => ['basico', 'intermedio', 'avanzado', 'experto'],
                'default'    => 'intermedio',
                'null'       => false,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['obligatorio', 'deseable'],
                'default'    => 'obligatorio',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vacante_id', 'vac_vacante', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('habilidad_id', 'cat_habilidad', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vac_habilidad');
    }

    public function down()
    {
        $this->forge->dropTable('vac_habilidad');
    }
}
