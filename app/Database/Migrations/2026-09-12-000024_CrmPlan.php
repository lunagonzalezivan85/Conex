<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrmPlan extends Migration
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
                'constraint' => 100,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'precio_mensual' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'precio_anual' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'USD',
                'null'       => false,
            ],
            'max_vacantes' => [
                'type'       => 'INT',
                'constraint'  => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'max_postulaciones' => [
                'type'       => 'INT',
                'constraint'  => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'destacar_vacantes' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'ver_perfil_completo' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'ver_datos_basicos' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'acceso_candidatos_verificados' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'acceso_cvs' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'soporte_prioritario' => [
                'type'    => 'BOOLEAN',
                'default' => false,
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
        $this->forge->createTable('crm_plan');
    }

    public function down()
    {
        $this->forge->dropTable('crm_plan');
    }
}
