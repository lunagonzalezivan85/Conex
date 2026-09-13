<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmPago extends Migration
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
                'unsigned'  => true,
                'null'      => false,
            ],
            'empresa_id' => [
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
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'PEN',
                'null'       => false,
            ],
            'metodo_pago' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'pagado', 'fallido', 'reembolsado'],
                'default'    => 'pendiente',
                'null'       => false,
            ],
            'fecha_pago' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'comprobante_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'notas' => [
                'type' => 'TEXT',
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
        $this->forge->addUniqueKey('codigo');
        $this->forge->addForeignKey('contrato_id', 'crm_contrato', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('empresa_id', 'emp_empresa', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('crm_pago');
    }

    public function down()
    {
        $this->forge->dropTable('crm_pago');
    }
}
