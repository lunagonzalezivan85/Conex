<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmContrato extends Migration
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
            'empresa_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'plan_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'tipo_facturacion' => [
                'type'       => 'ENUM',
                'constraint' => ['mensual', 'anual'],
                'default'    => 'mensual',
                'null'       => false,
            ],
            'fecha_inicio' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'fecha_fin' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'suspendido', 'cancelado', 'vencido', 'pendiente'],
                'default'    => 'pendiente',
                'null'       => false,
            ],
            'auto_renovar' => [
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
        $this->forge->addUniqueKey('codigo');
        $this->forge->addForeignKey('empresa_id', 'emp_empresa', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('plan_id', 'crm_plan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('crm_contrato');
    }

    public function down()
    {
        $this->forge->dropTable('crm_contrato');
    }
}
