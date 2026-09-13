<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandExperiencia extends Migration
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
            'empresa' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'cargo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fecha_inicio' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'fecha_fin' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'actual' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
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
        $this->forge->createTable('cand_experiencia');
    }

    public function down()
    {
        $this->forge->dropTable('cand_experiencia');
    }
}
