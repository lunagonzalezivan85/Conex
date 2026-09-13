<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuthRolePermission extends Migration
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
            'role_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'permission_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'auth_role', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'auth_permission', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('auth_role_permission');
    }

    public function down()
    {
        $this->forge->dropTable('auth_role_permission');
    }
}
