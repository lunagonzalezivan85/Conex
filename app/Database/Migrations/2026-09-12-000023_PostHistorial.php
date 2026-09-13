<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PostHistorial extends Migration
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
            'postulacion_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'estado_anterior' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'estado_nuevo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'comentario' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('postulacion_id', 'post_postulacion', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'auth_user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('post_historial');
    }

    public function down()
    {
        $this->forge->dropTable('post_historial');
    }
}
