<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandHabilidad extends Migration
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
            'habilidad_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'nivel' => [
                'type'       => 'ENUM',
                'constraint' => ['basico', 'intermedio', 'avanzado', 'experto'],
                'default'    => 'intermedio',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('candidato_id', 'cand_candidato', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('habilidad_id', 'cat_habilidad', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cand_habilidad');
    }

    public function down()
    {
        $this->forge->dropTable('cand_habilidad');
    }
}
