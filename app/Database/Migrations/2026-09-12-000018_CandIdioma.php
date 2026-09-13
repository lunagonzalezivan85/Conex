<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandIdioma extends Migration
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
            'idioma_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'nivel' => [
                'type'       => 'ENUM',
                'constraint' => ['basico', 'intermedio', 'avanzado', 'nativo'],
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
        $this->forge->addForeignKey('idioma_id', 'cat_idioma', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cand_idioma');
    }

    public function down()
    {
        $this->forge->dropTable('cand_idioma');
    }
}
