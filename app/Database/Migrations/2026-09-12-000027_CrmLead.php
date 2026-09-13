<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmLead extends Migration
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
                'null'      => true,
            ],
            'nombre_contacto' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'email_contacto' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'telefono_contacto' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'empresa_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'origen' => [
                'type'       => 'ENUM',
                'constraint' => ['web', 'referido', 'campana', 'directo', 'otro'],
                'default'    => 'web',
                'null'       => false,
            ],
            'plan_interes_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['nuevo', 'contactado', 'calificado', 'propuesta', 'negociacion', 'ganado', 'perdido'],
                'default'    => 'nuevo',
                'null'       => false,
            ],
            'asesor_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
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
        $this->forge->addForeignKey('empresa_id', 'emp_empresa', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('plan_interes_id', 'crm_plan', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('asesor_id', 'auth_user', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('crm_lead');
    }

    public function down()
    {
        $this->forge->dropTable('crm_lead');
    }
}
