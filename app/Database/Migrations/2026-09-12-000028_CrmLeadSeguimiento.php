<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmLeadSeguimiento extends Migration
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
            'lead_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'asesor_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'tipo_contacto' => [
                'type'       => 'ENUM',
                'constraint' => ['llamada', 'email', 'reunion', 'whatsapp', 'otro'],
                'default'    => 'llamada',
                'null'       => false,
            ],
            'comentario' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'fecha_proxima_accion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lead_id', 'crm_lead', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('asesor_id', 'auth_user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('crm_lead_seguimiento');
    }

    public function down()
    {
        $this->forge->dropTable('crm_lead_seguimiento');
    }
}
