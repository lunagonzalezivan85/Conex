<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmLeadServicio extends Migration
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
                'unsigned'   => true,
                'null'       => false,
            ],
            'servicio' => [
                'type'       => 'ENUM',
                'constraint'  => ['destacar_vacantes', 'candidatos_destacados', 'verificacion_express', 'analisis_cv_ia', 'multi_usuario', 'soporte_prioritario'],
                'null'       => false,
            ],
            'cantidad' => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => false,
                'default'   => 1,
            ],
            'precio' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'incluido' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lead_id', 'crm_lead', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('crm_lead_servicio');
    }

    public function down()
    {
        $this->forge->dropTable('crm_lead_servicio');
    }
}
