<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CatRequisito extends Migration
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
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => false,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['obligatorio', 'deseable'],
                'default'    => 'obligatorio',
                'null'       => false,
            ],
            'es_default' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default'    => 'activo',
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
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('cat_requisito');
    }

    public function down()
    {
        $this->forge->dropTable('cat_requisito');
    }
}
