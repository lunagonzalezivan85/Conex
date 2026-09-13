<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandEducacion extends Migration
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
            'nivel_educacion_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'institucion' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'fecha_inicio' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'fecha_fin' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'en_curso' => [
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
        $this->forge->addForeignKey('nivel_educacion_id', 'cat_nivel_educacion', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('cand_educacion');
    }

    public function down()
    {
        $this->forge->dropTable('cand_educacion');
    }
}
