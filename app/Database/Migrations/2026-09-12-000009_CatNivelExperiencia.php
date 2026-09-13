<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CatNivelExperiencia extends Migration
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
                'constraint' => 80,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => false,
            ],
            'anios_minimos' => [
                'type'    => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null'    => false,
            ],
            'anios_maximos' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
            ],
            'orden' => [
                'type'    => 'INT',
                'constraint' => 11,
                'default' => 0,
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
        $this->forge->createTable('cat_nivel_experiencia');
    }

    public function down()
    {
        $this->forge->dropTable('cat_nivel_experiencia');
    }
}
