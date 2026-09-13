<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PostVerificacion extends Migration
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
            'requisito_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'verificador_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'aprobado', 'rechazado', 'no_aplica'],
                'default'    => 'pendiente',
                'null'       => false,
            ],
            'comentario' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fecha_verificacion' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addForeignKey('postulacion_id', 'post_postulacion', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('requisito_id', 'cat_requisito', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('verificador_id', 'auth_user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('post_verificacion');
    }

    public function down()
    {
        $this->forge->dropTable('post_verificacion');
    }
}
