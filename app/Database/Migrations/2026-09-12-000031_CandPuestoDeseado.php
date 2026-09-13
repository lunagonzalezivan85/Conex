<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandPuestoDeseado extends Migration
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
            'categoria_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'puesto' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('candidato_id', 'cand_candidato', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('categoria_id', 'cat_categoria', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cand_puesto_deseado');
    }

    public function down()
    {
        $this->forge->dropTable('cand_puesto_deseado');
    }
}
