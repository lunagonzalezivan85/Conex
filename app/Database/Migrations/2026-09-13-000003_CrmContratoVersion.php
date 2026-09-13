<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmContratoVersion extends Migration
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
            'contrato_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'version' => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => false,
                'default'   => 1,
            ],
            'plan_id_anterior' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'plan_id_nuevo' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tipo_facturacion_anterior' => [
                'type'       => 'ENUM',
                'constraint'  => ['mensual', 'anual'],
                'null'       => true,
            ],
            'tipo_facturacion_nuevo' => [
                'type'       => 'ENUM',
                'constraint'  => ['mensual', 'anual'],
                'null'       => true,
            ],
            'precio_anterior' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'precio_nuevo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'motivo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'modificado_por' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('contrato_id', 'crm_contrato', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('plan_id_anterior', 'crm_plan', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('plan_id_nuevo', 'crm_plan', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('modificado_por', 'auth_user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('crm_contrato_version');
    }

    public function down()
    {
        $this->forge->dropTable('crm_contrato_version');
    }
}
