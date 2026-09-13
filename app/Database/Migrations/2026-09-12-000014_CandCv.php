<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandCv extends Migration
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
            'archivo_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'archivo_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'es_principal' => [
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
        $this->forge->createTable('cand_cv');
    }

    public function down()
    {
        $this->forge->dropTable('cand_cv');
    }
}
