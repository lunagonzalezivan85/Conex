<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VacRequisito extends Migration
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
            'requisito_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'habilidad_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'nivel_educacion_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
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
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vacante_id', 'vac_vacante', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requisito_id', 'cat_requisito', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('habilidad_id', 'cat_habilidad', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('nivel_educacion_id', 'cat_nivel_educacion', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('vac_requisito');
    }

    public function down()
    {
        $this->forge->dropTable('vac_requisito');
    }
}
